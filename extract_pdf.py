#!/usr/bin/python
from tempfile import template
import tabula
import json
import pandas as pd
from datetime import datetime
from dateutil.parser import parse
from classes import Title

def translate_month(time_string):
    month_dict={
        'Janvier':'January',
        'Février': 'February',
        'Mars': 'March',
        'Avril': 'April',
        'Mai': 'May',
        'Juin': 'June',
        'Juillet': 'July',
        'Août': 'August',
        'Septembre': 'September',
        'Octobre': 'October',
        'Novembre':'November',
        'Décembre':'December',
        'à': 'at'
        }
    for fr, en in month_dict.items():
        time_string = time_string.replace(fr, en)
    return (time_string)

def extract_set(file, ref):
    """ Reads a set the pdf in file at the passed ref point (top left point just after the "S E T X" column) """
    #Test to see if set isnt empty
    test_data = tabula.read_pdf(file, area=[[(ref[0] + 0.0), (ref[1] + 0.0), (ref[0] + 13.7), (ref[1] + 154.3)]], pages=1)
    set_data = list()
    if ((not test_data) or (test_data[0].columns.size == 1)):
        print("Empty set at (" + str(ref[0]) + ", " + str(ref[1]) + ") : KO")
        return [0,0,0,0,0,0,0,0]
    #Size of columns in the sub and serve table
    column_size = 20
    #Diff between the two teams tables in the first (y) axis
    team_diff = 155.1
    # 0 : Team 1 (left)
    set_data.append(tabula.read_pdf(file, area=[(ref[0] + 0.0), (ref[1] + 0.0), (ref[0] + 13.7), (ref[1] + 154.3)], pages='1'))
    # 1 : Team 2
    set_data.append(tabula.read_pdf(file, area=[(ref[0] + 0.8), (ref[1] + 154.5), (ref[0] + 13.7), (ref[1] + 310.6)], pages='1'))
    # 2 : Substitutions Team 1
    set_data.append(tabula.read_pdf(file, area=[(ref[0] + 14.0), (ref[1] + 0.0), (ref[0] + 57), (ref[1] + 118.3)], columns=[ref[1] +column_size*1, ref[1] + column_size*2, ref[1] + column_size*3, ref[1] + column_size*4, ref[1] + column_size*5, ref[1] + column_size*6], pages='1'))
    # 3 : Substitutions Team 2
    set_data.append(tabula.read_pdf(file, area=[(ref[0] + 15.2), (ref[1] + 0.0 + team_diff), (ref[0] + 57), (ref[1] + 119 + team_diff)], columns=[ref[1] + team_diff + column_size*1, ref[1] + team_diff + column_size*2, ref[1] + team_diff + column_size*3, ref[1] + team_diff + column_size*4, ref[1] + team_diff + column_size*5, ref[1] + team_diff + column_size*6], pages='1'))
    # 4 : Serves Team 1
    set_data.append(tabula.read_pdf(file, area=[(ref[0] + 57.6), (ref[1] + 0.0), (ref[0] + 90.8), (ref[1] + 118.3)], columns=[ref[1] +column_size*1, ref[1] + column_size*2, ref[1] + column_size*3, ref[1] + column_size*4, ref[1] + column_size*5, ref[1] + column_size*6], pages='1'))
    # 5 : Serves Team 2
    set_data.append(tabula.read_pdf(file, area=[(ref[0] + 57.6), (ref[1] + 0.0 + team_diff), (ref[0] + 90.6), (ref[1] + 118.8 + team_diff)], columns=[ref[1] + team_diff + column_size*1, ref[1] + team_diff + column_size*2, ref[1] + team_diff + column_size*3, ref[1] + team_diff + column_size*4, ref[1] + team_diff + column_size*5, ref[1] + team_diff + column_size*6],pages='1'))
    # 6 : Time outs Team 1
    set_data.append(tabula.read_pdf(file, area=[(ref[0] + 64.6), (ref[1] + 118.3), (ref[0] + 91.5), (ref[1] + 155.1)], pages='1'))
    # 7 : Time outs Team 2
    set_data.append(tabula.read_pdf(file, area=[(ref[0] + 64.6), (ref[1] + 118.3 + team_diff), (ref[0] + 91.5), (ref[1] + 155 + team_diff)], pages='1'))
    
    # Flattening and cleaning set structure
    if (set_data[0][0].columns[1].split()[2] == 'S'):
        team1 = 'Service'
        team2 = 'Reception'
    else:
        team2 = 'Service'
        team1 = 'Reception'

    #Teams
    team_data = pd.DataFrame({
        'Index':['Team1', 'Team2'],
        'Name':[set_data[0][0].columns[0], set_data[1][0].columns[0]],
        'Starting':[team1, team2]
    })
    team_data = team_data.set_index('Index')

    #Time
    # gather the date on the tp right of the file
    time_data = tabula.read_pdf(file, area=[39.6, 659.5, 49.7, 789.1], pages='1')

    #ttime = parse(test_string)
    #print(ttime)
    time_string = time_data[0].columns.values[0][time_data[0].columns.values[0].index(' ') + 1:]
    time_string = translate_month(time_string)
    start_time = parse(time_string + " " + set_data[0][0].columns[1].split()[1])
    end_time = parse(time_string + " " + set_data[1][0].columns[1].split()[1])

    
    #Replacing data
    set_data[0] = team_data
    set_data[1] = pd.DataFrame({
        'Index':['Start', 'End'],
        'Time':[start_time, end_time]
    }).set_index('Index')

    #Substitutions
    set_data[2] = set_data[2][0]
    set_data[3] = set_data[3][0]

    #Serves
    set_data[4] = set_data[4][0]
    set_data[5] = set_data[5][0]
    
    #Timeouts
    #Need to check if its empty
    #if (set_data[6][0].empty):
    #    set_data[6] = pd.DataFrame({'T'})
    set_data[6] = set_data[6][0]
    set_data[7] = set_data[7][0]

    
    print("Parsing set at (" + str(ref[0]) + ", " + str(ref[1]) + ") : OK")
    return (set_data)

def extract_team(file, ref):
    """ Reads a team and returns the table with player and officiels infos """
    team_data = list()
    ## Test if empty (BUT SHOULDNT ?!)
    column_size = [11.5, 100, 128.2]
    # 0 : Name
    team_data.append(tabula.read_pdf(file, area=[(ref[0] + 0.0), (ref[1] + 0.0), (ref[0] + 13.7), (ref[1] + 127.4)], pages='1'))
    # 1 : Players
    team_data.append(tabula.read_pdf(file, area=[(ref[0] + 13.7), (ref[1] + 0.0), (ref[0] + 141.8), (ref[1] + 127.4)], columns=[ref[1] + column_size[0], ref[1] + column_size[1], ref[1] + column_size[2]] , pages='1'))
    # 2 : Liberos
    #team_data.append(tabula.read_pdf(file, area=[(ref[0] + 153.7), (ref[1] + 0.0), (ref[0] + 169.9), (ref[1] + 127.4)], columns=[ref[1] + column_size[0], ref[1] + column_size[1], ref[1] + column_size[2]] , pages='1'))
    team_data.append(tabula.read_pdf(file, area=[(ref[0] + 141.1), (ref[1] + 0.0), (ref[0] + 169.9), (ref[1] + 127.4)], columns=[ref[1] + column_size[0], ref[1] + column_size[1], ref[1] + column_size[2]] , pages='1'))
    # 3 : Officials
    #team_data.append(tabula.read_pdf(file, area=[(ref[0] + 181.4), (ref[1] + 0.0), (ref[0] + 215.3), (ref[1] + 127.4)], columns=[ref[1] + column_size[0], ref[1] + column_size[1], ref[1] + column_size[2]] , pages='1'))
    team_data.append(tabula.read_pdf(file, area=[(ref[0] + 169.9), (ref[1] + 0.0), (ref[0] + 215.3), (ref[1] + 127.4)], columns=[ref[1] + column_size[0], ref[1] + column_size[1], ref[1] + column_size[2]] , pages='1'))
    
    #Flattening and cleaning structures
    team_data[0] = team_data[0][0].columns.values
    team_data[1] = team_data[1][0]
    team_data[2] = team_data[2][0]    
    team_data[3] = team_data[3][0]    
    
    #team_data[1] = team_data[1].rename(columns={'N°':'Number', 'Nom Prénom':'Name'})
    #print(team_data[1].columns.values)
    team_data[2].columns = team_data[1].columns.values 
    team_data[3].columns = team_data[1].columns.values 
    print("Parsing Team at (" + str(ref[0]) + ", " + str(ref[1]) + ") : OK")
    return (team_data)

def extract_title(file):
    #ref = 
    title_data = list()
    table_data = list()

    # 0 Division: Code, Name, Pool  (string) (25.9, 113.8, 38.9, 429.1) # Split('-')
    table_data.append(tabula.read_pdf(file, area=[25.9, 113.8, 38.9, 429.1], pages='1'))
    # 1 Match, Day (number) (26.6,692.6,39.6,820.8) 
    table_data.append(tabula.read_pdf(file, area=[26.6,692.6,39.6,820.8 ], pages='1'))
    # 2 City (string) (38.2, 115.2,46,295.9)
    table_data.append(tabula.read_pdf(file, area=[38.2, 115.2,46,295.9 ], pages='1'))
    # 3 Gym (string) (46.1, 115.2,53.3, 295.9)
    table_data.append(tabula.read_pdf(file, area=[46.1, 115.2,53.3, 295.9 ], pages='1'))
    # 4 Category (string) (46.1, 352.8,53.3, 533.5)
    table_data.append(tabula.read_pdf(file, area=[46.1, 352.8,53.3, 533.5 ], pages='1'))
    # 5 Ligue (string) (55.4, 1.4,71.3, 163.4)
    table_data.append(tabula.read_pdf(file, area=[55.4, 1.4,71.3, 163.4 ], pages='1'))
    # 6 Date (datetime) (38.9, 655.2, 50.4, 821.5)
    table_data.append(tabula.read_pdf(file, area=[38.9, 655.2, 50.4, 821.5 ], pages='1'))

    #Flatten and clean 
    div_list = table_data[0][0].columns.values[0].split('-')
    print(table_data[0][0].columns)
    match_list = table_data[1][0].columns[0].split('-')
    # 0 Division_Code  (string) (25.9, 113.8, 38.9, 429.1) # Split('-')
    # 1 Division_Name (string)
    # 2 Pool (letter) 
    # 3 Match (number) (26.6,692.6,39.6,820.8)
    # 4 Day (number)
    # 5 City (string) (38.2, 115.2,46,295.9)
    # 6 Gym (string) (46.1, 115.2,53.3, 295.9)
    # 7 Category (string) (46.1, 352.8,53.3, 533.5)
    # 8 Ligue (string) (55.4, 1.4,71.3, 163.4)
    # 9 Date (datetime string) (38.9, 655.2, 50.4, 821.5)
    
    time_string = table_data[6][0].columns.values[0][table_data[6][0].columns.values[0].index(' ') + 1:]
    print(time_string)
    time_string = translate_month(time_string)
    match_time = parse(time_string)
    print(match_time)
    title_data = Title(
        div_list[0],
        div_list[1].strip(),
        div_list[2].strip(),
        match_list[0].split(':')[1].strip(),
        match_list[1].split(':')[1].strip(),
        table_data[2][0].columns.values[0].split(':')[1].strip(),
        table_data[3][0].columns.values[0].split(':')[1].strip(),
        table_data[4][0].columns.values[0],
        table_data[5][0].columns.values[0],
        match_time.strftime("%y-%m-%d %H:%M:%S")
    )
    #print(title_data.export_json())
    print("Title parsing : OK")
    return (title_data)

def extract_result(file):

    col = 8
    winner = 0
    score = "0/0"
    for i in range(3):
        res_data = tabula.read_pdf(file, area=[501 + i*col, 424, 509.8 + i*col, 560.9], pages='1')
        if(res_data[0].columns.values[0] == 'Vainqueur:'):
            winner = res_data[0].columns.values[1]
            score = res_data[0].columns.values[2]
            break
    res = pd.DataFrame({
        'Index':['Winner', 'Score'],
        'Results':[winner, score]
    }).set_index('Index')
    print("Result parsed : OK")
    return (res)

def extract_penalization(file):
    ref = [371, 13.5, 515.5, 128.2]

    pen_data = tabula.read_pdf(file, area=ref, pages='1')
    print(pen_data[0])
    return (0)

def extract_match(file):
    output = file.split('.')[0]+".json"

    ## ----------------------  Extract title data  ----------------------------- ##
    title = extract_title(file)

    ## ----------------------  Extract set data  ------------------------------- ##
    #Set 1 (73.4, 127.4)
    set1 = extract_set(file, (73.4, 127.4))
    #print(set1)
    
    #Set 2 (73.4, 462.7)
    set2 = extract_set(file, (73.4, 462.7))
    #print(set2)

    #Set 3 (167, 128.2)
    set3 = extract_set(file, (167, 128.2))
    #print(set3)

    #Set 4 (167, 461.5)
    set4 = extract_set(file, (167, 461.5))
    #print(set4)

    #Set 5 (261.4, 28.1)
    set5 = extract_set(file, (261.4, 28.1))
    #print(set5)

    # Gathering info into single dataframe
    sets = pd.DataFrame({
        'Index' : ['Set 1', 'Set 2', 'Set 3', 'Set 4', 'Set 5'],
        'Teams  ':[set1[0], set2[0], set3[0], set4[0], set5[0]],
        'Time ':[set1[1], set2[1], set3[1], set4[1], set5[1]],
        'Substitutions 1 ':[set1[2], set2[2], set3[2], set4[2], set5[2]],
        'Substitutions 2 ':[set1[3], set2[3], set3[3], set4[3], set5[3]],
        'Serves 1 ':[set1[4], set2[4], set3[4], set4[4], set5[4]],
        'Serves 2 ':[set1[5], set2[5], set3[5], set4[5], set5[5]],
        'Timeouts 1 ':[set1[6], set2[6], set3[6], set4[6], set5[6]],
        'Timeouts 2 ':[set1[7], set2[7], set3[7], set4[7], set5[7]],
                        }).set_index('Index')
    #print("Sets : \n")
    #print(sets)
    
    
    ## ----------------------  Extract team data ------------------------------- ##
    #Team A (261, 575.3)
    teamA = extract_team(file, (261, 575.3))
    #Team B (261, 702.7)
    teamB = extract_team(file, (261, 702.7))

    #Gathering info into single dataframe
    teams = pd.DataFrame({
        'Index':['Team A', 'Team B'],
        'Name':[teamA[0], teamB[0]],
        'Players':[teamA[1], teamB[1]],
        'Liberos':[teamA[2], teamB[2]],
        'Officials':[teamA[3], teamB[3]]
    }).set_index('Index')
    #teams = pd.DataFrame({
    #    'TeamA' : [teamA[0], teamA[1], teamA[2], teamA[3]],
    #    'TeamB' : [teamB[0], teamB[1], teamB[2], teamB[3]]
    #})
    #print(teams)

    ## ----------------------  Extract results data ---------------------------- ##
    # Correct row depends on the number of sets, so might be easier to juste compile the results from the sets results
    # As it can only be in 3 places, let's just loop through them and store if they start by "Vainqueur"
    result = extract_result(file)
    #print(result)

    ## ----------------------  Extract sanctions data -------------------------- ##

    #pen = extract_penalization(file)


    ## ----------------------  Extract referees data --------------------------- ##
    refs = tabula.read_pdf(file, area=[433.4, 129, 504.1, 306.7], columns=[129+23, 129+119, 129+147.5, 129+175.7], pages='1')[0].set_index('Arbitres')
    #print(refs)
    
    
    ## ----------------------  Gather in Match Structure ----------------------- ##
    #Gathering into a Match dataframe
    match = pd.DataFrame({
        'Index': ['Title', 'Sets', 'Teams', 'Results', 'Referees'],
        'Match': [title.__dict__ ,sets, teams, result, refs]
    }).set_index('Index')
    #Exporting data to Json

    json_output = match.to_json(force_ascii=False)
    #print(json_output)
    with open(output,'w') as outfile:
        outfile.write(json_output)
        print(output + " saved.")

    
    match = 0
    return (match)

if __name__ == "__main__":
    """ Main function of extracting data """
    file = "test_EMA.pdf"
    match = extract_match(file)