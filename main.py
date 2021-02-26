import extraction
import web_scraping
import csv
import os
from tqdm import tqdm
from extraction import extract_pdf, classes

import time
# Scrape for links

# Download pdfs into data folder

# Extract jsons from 
def extract_jsons():
    correct_format_nb = 0
    total_nb = 0
    for parent, dirnames, filenames in os.walk('./data'):
        print(parent)
        for fn in tqdm(filenames):
            if fn.lower().endswith('.pdf'):
                #found all pdfs
                total_nb += 1
                #create folders and subfolders
                output = os.path.join(os.path.dirname(__file__), "parsed_matches"+parent.split('./data')[1])
                if not os.path.exists(output):
                    os.makedirs(output)
                
                #checking format for stats
                pickle = os.path.join(os.path.dirname(__file__), "extraction/format.pkl")
                try :
                    #print(os.path.join(parent, fn))
                    # Ignoring Pro league
                    if (fn.startswith('L')):
                        raise classes.FormatInvalidError
                    extract_pdf.check_format(os.path.join(parent,fn), pickle)
                    #time.sleep(0.05)
                    correct_format_nb += 1
                except classes.FormatInvalidError:
                    print("FormatInvalidError")

                #extract json into theses
                #extract_pdf.extract_pdf(fn, output)
        print("Correct format stats : " + str(correct_format_nb) + "/" + str(total_nb))


if __name__ == "__main__":
    # load csv list
    # make dir with season/div/pdf&json if doesnt exists
    # download pdfs in appropriate folder
    # use extract pdf 
    extract_jsons()