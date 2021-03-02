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
    valid_files = list()
    for parent, dirnames, filenames in os.walk('./data'):
        for fn in tqdm(filenames, ncols=90, desc=parent+" :"):
            total_nb += 1
            if fn.lower().endswith('.pdf'):
                #found all pdfs
                #create folders and subfolders
                output_folder = os.path.join(os.path.dirname(__file__), "parsed_matches"+parent.split('./data')[1])
                if not os.path.exists(output_folder):
                    print("Creating dir : "+ output_folder)
                    os.makedirs(output_folder)
                
                output = os.path.join(output_folder, fn.split('.pdf')[0]+'.json')
                if os.path.isfile(output):
                    correct_format_nb += 1
                    continue
                
                #checking format for stats
                pickle = os.path.join(os.path.dirname(__file__), "extraction/format.pkl")
                try :
                    #print(os.path.join(parent, fn))
                    # Ignoring Pro league
                    if (fn.startswith('L')):
                        raise classes.FormatInvalidError
                    extract_pdf.extract_pdf(os.path.join(parent, fn), output_folder)
                    #time.sleep(0.05)
                    correct_format_nb += 1
                except classes.FormatInvalidError:
                    print("FormatInvalidError: "+fn)

                #extract json into theses
                #extract_pdf.extract_pdf(fn, output)
            else :
                print("FormatInvalidError: "+fn)
        print("Correct format stats : " + str(correct_format_nb) + "/" + str(total_nb))


if __name__ == "__main__":
    # load csv list
    # make dir with season/div/pdf&json if doesnt exists
    # download pdfs in appropriate folder
    # use extract pdf 
    extract_jsons()