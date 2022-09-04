import extraction
import web_scraping
import csv
import os
from tqdm import tqdm
from extraction import extract_pdf, classes
import sys

import time

# Scrape for links

# Download pdfs into data folder

# Extract jsons from
def list_to_csv(list, label):
    with open(label + ".csv", "w", newline="") as myfile:
        wr = csv.writer(myfile, dialect="excel")
        for item in list:
            wr.writerow(
                [
                    item,
                ]
            )


def extract_jsons(folder="./data", out_folder="./parsed_matches", all=False):
    correct_format_nb = 0
    total_nb = 0
    invalid_files = list()
    error_files = list()
    for parent, dirnames, filenames in os.walk("./data"):
        for fn in tqdm(filenames, ncols=90, desc=parent + " :"):
            total_nb += 1
            if fn.lower().endswith(".pdf"):
                # found all pdfs
                # create folders and subfolders
                output_folder = os.path.join(
                    os.path.dirname(__file__), out_folder + parent.split(folder)[1]
                )
                if not os.path.exists(output_folder):
                    print("Creating dir : " + output_folder)
                    os.makedirs(output_folder)

                output = os.path.join(output_folder, fn.split(".pdf")[0] + ".json")
                if os.path.isfile(output):
                    # TODO Add all option
                    if not all:
                        correct_format_nb += 1
                        continue

                # checking format for stats
                pickle = os.path.join(
                    os.path.dirname(__file__), "extraction/format.pkl"
                )
                try:
                    # print(os.path.join(parent, fn))
                    # Ignoring Pro league
                    if fn.startswith("L"):
                        raise classes.FormatInvalidError
                    extract_pdf.extract_pdf(os.path.join(parent, fn), output_folder)
                    # time.sleep(0.05)
                    correct_format_nb += 1
                except classes.FormatInvalidError as e:
                    print(
                        "FormatInvalidError: %s at %s"
                        % (str(e), str(parent) + "/" + str(fn))
                    )
                    print(str(parent))
                    invalid_files.append(str(parent) + "/" + str(fn))
                    list_to_csv(invalid_files, "invalid_files")
                except KeyboardInterrupt:
                    print("Interrupted")
                    try:
                        sys.exit(0)
                    except SystemExit:
                        os._exit(0)
                except:
                    print("Other Error : " + str(parent) + "/" + str(fn))
                    error_files.append(str(parent) + "/" + str(fn))
                    list_to_csv(error_files, "error_files")

                # extract json into theses
                # extract_pdf.extract_pdf(fn, output)
            else:
                print("FormatInvalidError: " + str(parent) + "/" + str(fn))
                invalid_files.append(str(parent) + "/" + str(fn))
                list_to_csv(invalid_files, "invalid_files")
        print("Correct format stats : " + str(correct_format_nb) + "/" + str(total_nb))
        list_to_csv(invalid_files, "invalid_files")
        list_to_csv(error_files, "error_files")


def print_usage():
    print("Usage: main.py folder [-a, --all] [-o | output_folder] [-h, --help] \n ")
    print(
        "     folder:             Name of input folder, in relative to the script folder"
    )
    print(
        "     -a, --all:          Enable overwriting of existing outputs, default=False"
    )
    print(
        "     -o <output_folder>: Name of input folder, in relative to the script folder"
    )
    print(
        "     -i, --ignore:       Ignoring all errors going through files, default=True"
    )
    print("     -h, --help:         Printing this usage message")


if __name__ == "__main__":
    # load csv list
    # make dir with season/div/pdf&json if doesnt exists
    # download pdfs in appropriate folder
    # use extract pdf
    folder = "./data"
    output = "./parsed_matches"
    all = False
    # TODO check if folder field is valid
    if len(sys.argv) < 2:
        print_usage()
    if (not sys.argv[1]) or (not os.path.exists(sys.argv[1])):
        print("Error folder field invalid.")
        print_usage()
        exit()
    folder = sys.argv[1]

    # ? Check optional arguments
    for i, opt in enumerate(sys.argv[2:]):
        if opt == "-h" or opt == "--help":
            print_usage()
            exit()
        if opt == "-a" or opt == "--all":
            all = True
        if (opt == "-o") and (len(sys.argv[2:]) > 2 + i):
            print(os.path.exists(sys.argv[2 + i + 1]))
            if os.path.exists(sys.argv[2 + i + 1]):
                output = sys.argv[2 + i + 1]
        print(opt)

    extract_jsons(folder, output, all)
