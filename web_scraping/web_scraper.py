from bs4 import BeautifulSoup
import requests
import webbrowser

#Instead of working with local html :
url = "https://www.ffvbbeach.org/ffvbapp/resu/vbspo_calendrier.php?saison=2020/2021&codent=ABCCS&poule=EMA"
source = requests.get(url, verify = False).text
soup = BeautifulSoup(source, 'lxml')

#with open('pool_test.html') as source:
#    soup = BeautifulSoup(source, 'lxml')

#print(soup.prettify())
#print(soup.title.text)

i = 0
for table in soup.body.findAll('table'):
    i += 1
    if (i != 4):
        continue
    for match in table.findAll('tr', {"bgcolor" : "#EEEEF8"}):
        try :
            data = match.form['action']
            url = data.replace("..", "https://www.ffvbbeach.org/ffvbapp")
            print(url)
        except Exception as e:
            pass
        print()
        #webbrowser.open(url)

#Can serialize into csv, probably better than directly downloading
