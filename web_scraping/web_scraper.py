from bs4 import BeautifulSoup
import requests
from urllib.parse import urljoin
import csv
from tqdm import tqdm
import urllib3
import webbrowser

def scrape_urls_nat(SOURCE_URL):
    """ Scraper of pool's urls for national level"""
    source = requests.get(SOURCE_URL, verify=False)
    soup = BeautifulSoup(source.content, 'lxml')

    lst = list()
    div = soup.find('div')
    for select in div.findAll('select')[:-1]:
        for option in select.findAll('option')[1:]:
            lst.append({'div': option.text.split('\n')[0], 'url': option.get('value')})
    return (lst)


    

def scrape_pdfs(SOURCE_URL):
    """ Returns list of match sheets pdf of the given [url]"""
    source = requests.get(SOURCE_URL, verify = False).text
    soup = BeautifulSoup(source, 'lxml')
    
    #table_lst = soup.findAll("table", {"cellspacing" : "0"})
    lst = list()
    table_list = soup.findAll("table")
    for table in table_list:
        for form in table.findAll("form", {"name" : "divers"}):
            url = form['action']
            url = url.replace("..", "https://www.ffvbbeach.org/ffvbapp")
            if (not url.endswith(".php")):
                lst.append(url)
    lst = list(set(lst))
    return (lst)

    #Can serialize into csv, probably better than directly downloading


if __name__ == "__main__":
    #HTTPS requires certificate, pushing the prbm for later rn: "verify=False"+suppress warning
    urllib3.disable_warnings(urllib3.exceptions.InsecureRequestWarning)

    # National levels first step : getting all the pool web pages from the index structure they have (only for 2020-2021 ?)
    url = "https://www.ffvbbeach.org/ffvbapp/resu/seniors/2019-2020/pbscript.htm"
    url_list_nat = scrape_urls_nat(url)
    pdfs = list()
    for pool in tqdm(url_list_nat):
        #Then for each pull gather the list of pdf's address and compiling it in a list of dict
        pdf_lst = scrape_pdfs(pool['url'])
        for link in pdf_lst:
            pdfs.append({
                'season': "2020-2021",
                'div': pool['div'],
                'url': link,
                })

    with open('links.csv', 'w', encoding='utf8',newline='')  as output_file:
        keys = pdfs[0].keys()
        dict_writer = csv.DictWriter(output_file, keys)
        dict_writer.writeheader()
        dict_writer.writerows(pdfs)

    """
    ## Extracting pdf's url from pool page
    url = "https://www.ffvbbeach.org/ffvbapp/resu/vbspo_calendrier.php?saison=2020/2021&codent=ABCCS&poule=EMA"
    pdf_list = scrape_pdfs(url)
    for url in pdf_list:
        print(url)
        """

    #url = "https://www.ffvbbeach.org/ffvbapp/resu/vbspo_calendrier.php?saison=2020/2021&codent=ABCCS&poule=EMA"
    #url = "https://www.ffvbbeach.org/ffvbapp/resu/vbspo_calendrier.php?saison=2019/2020&codent=ABCCS&poule=CPM"
    #url = "https://www.ffvbbeach.org/ffvbapp/resu/vbspo_calendrier.php?saison=2019/2020&codent=ABCCS&division=COM&tour=02"
    #pdf_list_test = scrape_pdfs(url)
    
