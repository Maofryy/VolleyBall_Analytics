from bs4 import BeautifulSoup
import requests
from urllib.parse import urljoin
import csv
from tqdm import tqdm
import pandas as pd
import urllib3
import json
import webbrowser
import os
from urllib.parse import urlparse, parse_qs


def download_file(download_url, folder):
    """Download pdf from url into folder (make sure the folder var ends in a slash)"""
    # Filename :
    #       if links ends with pdf, just split and take the name of pdf
    #       if not, take the code match, last element after '=' and add ".pdf"
    if download_url.endswith(".pdf") or download_url.endswith(".jpg"):
        filename = download_url.split("/")[-1]
    else:
        filename = download_url.split("=")[-1] + ".pdf"
    try:
        file = open(folder + filename, "xb")
        response = requests.get(download_url, verify=False)
        file.write(response.content)
        print("New File : " + filename)
        file.close()
    except FileExistsError as e:
        # print("File already downloaded : " + download_url)
        pass


def scrape_pdfs(SOURCE_URL):
    """Returns list of match sheets pdf of the given [url]"""
    source = requests.get(SOURCE_URL, verify=False).text
    soup = BeautifulSoup(source, "lxml")

    # table_lst = soup.findAll("table", {"cellspacing" : "0"})
    lst = list()
    table_list = soup.findAll("table")
    for table in table_list:
        for form in table.findAll("form", {"name": "divers"}):
            url = form["action"]
            url = url.replace("..", "https://www.ffvbbeach.org/ffvbapp")
            if not url.endswith(".php"):
                lst.append(url)
    lst = list(set(lst))
    return lst

    # Can serialize into csv, probably better than directly downloading


def scrape_matchs(SOURCE_URL):
    """Returns the list of al matchs of the given [SOURCE_URL]"""
    source = requests.get(SOURCE_URL, verify=False).text
    soup = BeautifulSoup(source, "lxml")

    # table_lst = soup.findAll("table", {"cellspacing" : "0"})
    lst = list()
    table_list = soup.findAll("table")
    for table in table_list:
        for form in table.findAll("form", {"name": "divers"}):
            url = form["action"]
            url = url.replace("..", "https://www.ffvbbeach.org/ffvbapp")
            if not url.endswith(".php"):
                lst.append(url)
    lst = list(set(lst))
    return lst

    # Can serialize into csv, probably better than directly downloading


def scrape_urls_nat(SOURCE_URL):
    """Scraper of pool's urls for national level"""
    source = requests.get(SOURCE_URL, verify=False)
    soup = BeautifulSoup(source.content, "lxml")

    lst = list()
    div = soup.find("div")
    for select in div.findAll("select")[:-1]:
        for option in select.findAll("option")[1:]:
            lst.append({"div": option.text.split("\n")[0], "url": option.get("value")})
    return lst


def compile_links(output_file):
    """Compiling all urls into an [output_file] csv file"""
    # National levels first step : getting all the pool web pages from the index structure they have (only for 2020-2021 ?)
    url_nat = "https://www.ffvbbeach.org/ffvbapp/resu/seniors/2019-2020/pbscript.htm"
    url_list_nat = scrape_urls_nat(url_nat)
    pdfs = list()
    for pool in tqdm(url_list_nat, desc="Getting pdf list"):
        # Then for each pull gather the list of pdf's address
        pdf_lst = scrape_pdfs(pool["url"])
        for link in pdf_lst:
            pdfs.append(
                {
                    "season": "2019-2020",
                    "div": pool["div"].replace(" ", "_"),
                    "url": link,
                }
            )

    # Compiling the list into a csv to hold urls
    with open(output_file, "w", encoding="utf8", newline="") as output_file:
        keys = pdfs[0].keys()
        dict_writer = csv.DictWriter(output_file, keys)
        dict_writer.writeheader()
        dict_writer.writerows(pdfs)

    print("National pdf scraped into " + str(output_file))


def download_links(input_file):
    """Dowloading pdfs from links.csv"""
    links_df = pd.read_csv(input_file)

    for index, link in tqdm(links_df.iterrows(), desc="Dowloading pdfs"):
        # Create folders if they doesnt exits
        folder = "./data/" + link["season"] + "/" + link["div"] + "/"
        if not os.path.exists(folder):
            os.makedirs(folder)
        download_file(link["url"], folder)
    print("Data folder updated from " + str(input_file))


def list_to_csv(list, label):
    with open(label, "w", newline="") as file:
        mywriter = csv.writer(file, delimiter=",")
        mywriter.writerows(list)


def translateJSONtoLinks(links_file, start, end):
    """Links file is a JSON array of objects like the following :
    # {
    #    column : [pdf_link]
    # }

    # we use csv with the following headers [season, div, url]"""
    links_list = []
    links_list.append(["season", "div", "url"])

    f = open(links_file)
    data = json.load(f)

    count = 0
    for i in data:
        if count >= start and count < end:
            url = i["column"]
            param = urlparse(url)
            query = parse_qs(param.query)
            links_list.append(
                [
                    "".join(query["saison"]).replace("/", "-"),
                    "".join(query["codent"]),
                    url,
                ]
            )
        count += 1

    print("Total Rows : " + str(count))
    # for i in data:
    #    print(i)

    f.close()
    # print(links_list)
    list_to_csv(links_list, "links.csv")


if __name__ == "__main__":

    # HTTPS requires certificate, pushing the prbm for later rn: "verify=False"+suppress warning
    urllib3.disable_warnings(urllib3.exceptions.InsecureRequestWarning)

    # compile_links("links.csv")

    links_file = "link_match_nationaux-1.json"
    # links_file = "link_match_regionaux-1.json"

    translateJSONtoLinks(links_file, 0, 2600)
    download_links("links.csv")

    """
    ## Extracting pdf's url from pool page
    url = "https://www.ffvbbeach.org/ffvbapp/resu/vbspo_calendrier.php?saison=2020/2021&codent=ABCCS&poule=EMA"
    pdf_list = scrape_pdfs(url)
    for url in pdf_list:
        print(url)
        """

    # url = "https://www.ffvbbeach.org/ffvbapp/resu/vbspo_calendrier.php?saison=2020/2021&codent=ABCCS&poule=EMA"
    # url = "https://www.ffvbbeach.org/ffvbapp/resu/vbspo_calendrier.php?saison=2019/2020&codent=ABCCS&poule=CPM"
    # url = "https://www.ffvbbeach.org/ffvbapp/resu/vbspo_calendrier.php?saison=2019/2020&codent=ABCCS&division=COM&tour=02"
    # pdf_list_test = scrape_pdfs(url)
