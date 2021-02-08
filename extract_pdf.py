#!/usr/bin/python
from tempfile import template
import tabula
import json
import pandas as pd
from datetime import datetime
from dateutil.parser import parse


def extract_set(file, ref):
    """ Reads a set the pdf in file at the passed ref point (top left point just after the "S E T X" column) """
    #Test to see if set isnt empty
    test_data = tabula.read_pdf(file, area=[[(ref[0] + 0.0), (ref[1] + 0.0), (ref[0] + 13.7), (ref[1] + 154.3)]], pages=1)
    set_data = list()
    if ((not test_data) or (test_data[0].columns.size == 1)):
        print("Empty set at (" + str(ref[0]) + ", " + str(ref[1]) + ")")
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
        'Décembre':'December'
        }
    #ttime = parse(test_string)
    #print(ttime)
    time_string = time_data[0].columns.values[0][time_data[0].columns.values[0].index(' ') + 1:]
    print(time_string)
    for fr, en in month_dict.items():
        time_string = time_string.replace(fr, en)
    start_time = parse(time_string + " " + set_data[0][0].columns[1].split()[1])
    print(start_time)
    # read the date as DD ... 
    ## Can store in time format but not needed
    #start = datetime.strptime(set_data[0][0].columns[1].split()[1], "%H:%M")
    #end = datetime.strptime(set_data[0][1].columns[1].split()[1], "%H:%M")
    
    #Replacing data
    set_data[0] = team_data
    set_data[1] = set_data[1][0].columns.values

    #Substitutions
    set_data[2] = set_data[2][0]
    set_data[3] = set_data[3][0]

    #Serves
    set_data[4] = set_data[4][0]
    set_data[5] = set_data[5][0]
    
    #Timeouts
    set_data[6] = set_data[6][0]
    set_data[7] = set_data[7][0]

    
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

    return (team_data)

if __name__ == "__main__":
    """ Main function of extracting data """
    file = "test_EMA.pdf"
    output = file.split('.')[0]+".json"

    ## ----------------------  Extract set data ------------------------------- ##
    #Set 1 (73.4, 127.4)
    set1 = extract_set(file, (73.4, 127.4))
    #print(set1)
    """
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
    
    
    ## ----------------------  Extract set data ------------------------------- ##
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
    
    #Gathering into a Match dataframe
    match = pd.DataFrame({
        'Index': ['Sets', 'Teams'],
        'Match': [sets, teams]
    }).set_index('Index')
    #Exporting data to Json

    json_output = match.to_json(force_ascii=False)
    #print(json_output)
    with open(output,'w') as outfile:
        outfile.write(json_output)
        print(output + " saved.")

    #"""