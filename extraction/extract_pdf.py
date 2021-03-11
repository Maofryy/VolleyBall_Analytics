# !/usr/local/bin/python
# -*- coding: utf-8 -*-
from tempfile import template
import tabula
import time
import pandas as pd
import os
import csv
from datetime import datetime
from dateutil.parser import parse
import webbrowser
from .classes import FormatInvalidError, Penalty, Results, Title, Timer

def translate_month(time_string):
    """Translating time string from french to english

    Args:
        time_string ([string]): French time string

    Returns:
        [string]: English time string
    """

    month_dict={
        'Janvier':'January',
        'Février': 'February',
        'Fév': 'February',
        'Mars': 'March',
        'Avril': 'April',
        'Mai': 'May',
        'Juin': 'June',
        'Juillet': 'July',
        'Juil': 'July',
        'Août': 'August',
        'Septembre': 'September',
        'Octobre': 'October',
        'Novembre':'November',
        'Décembre':'December',
        'Décem':'December',
        'à': 'at'
        }
    for fr, en in month_dict.items():
        time_string = time_string.replace(fr, en)
    return (time_string)

def get_set_data(file, ref, set_nb, verbose=False):
    #Size of columns in the sub and serve table
    column_size = 20
    #Diff between the two teams tables in the first (y) axis
    if set_nb == 5:
        team_diff = 135
        set_swap = 303
    else:
        team_diff = 155.1
        set_swap = 0
    
    table_data = tabula.read_pdf(file, area=[
        #[(ref[0] + 3.6), (ref[1] + 0.0), (ref[0] + 13.7), (ref[1] + 154.3)],
        [(ref[0] + 14.0), (ref[1] + 0.0 + set_swap), (ref[0] + 57), (ref[1] + 118.3 + set_swap)], #* +303 pour set 5
        [(ref[0] + 15.2), (ref[1] + 0.0 + team_diff), (ref[0] + 57), (ref[1] + 119 + team_diff)],
        [(ref[0] + 56.7), (ref[1] + 0.0 + set_swap), (ref[0] + 98.8), (ref[1] + 118.8 + set_swap)], #* +303 pour set 5
        [(ref[0] + 56.7), (ref[1] + 0.0 + team_diff), (ref[0] + 98.6), (ref[1] + 118.8 + team_diff)],
        [(ref[0] + 64.6), (ref[1] + 118.3 + set_swap), (ref[0] + 91.5), (ref[1] + 155.1 + set_swap)], #* +303 pour set 5
        [(ref[0] + 64.6), (ref[1] + 118.3 + team_diff), (ref[0] + 91.5), (ref[1] + 155 + team_diff)]
    ],
    columns=[
        ref[1] +column_size*1 + set_swap, ref[1] + column_size*2 + set_swap, ref[1] + column_size*3 + set_swap, ref[1] + column_size*4 + set_swap, ref[1] + column_size*5 + set_swap, ref[1] + column_size*6 + set_swap, 
        ref[1] + team_diff + column_size*1, ref[1] + team_diff + column_size*2, ref[1] + team_diff + column_size*3, ref[1] + team_diff + column_size*4, ref[1] + team_diff + column_size*5, ref[1] + team_diff + column_size*6],
    pages='1')
    return table_data

def extract_set(ref, set_nb, title_data, table_data, verbose=False):
    """[Extract one set data]
    Reads a set the pdf in file at the passed ref point (top left point just after the "S E T X" column)

    Args:
        file ([string]): filename of pdf input file
        ref ([tuple]): top left point (X, Y) of the set area
        verbose (bool, optional): verbose option of the function. Defaults to False.

    Returns:
        [List(pandas.DataFrame)]: [List of tables with set data]
    """
    #title_data, table_data = get_set_data(file, ref, set_nb)
    
    if not title_data or not table_data:
        print("Empty set at (" + str(ref[0]) + ", " + str(ref[1]) + ") : KO") if (verbose == True) else 0
        return [0,0,0,0,0,0,0,0]
    
    set_data = list()
    sep = ""

    for x in title_data[:-1]:
        set_data.append(x)
    for x in table_data:
        set_data.append(x)
    set_data.append(title_data[-1])
    #for x in range(len(set_data)):
    #    print(x)
    #    print(set_data[x])


    lst = sep.join(set_data[0].columns.values)
    #print("lst: "+str(lst))
    if (lst == "Début:" or set_data[0].columns.size == 0):
        print("Empty set at (" + str(ref[0]) + ", " + str(ref[1]) + ") : KO") if (verbose == True) else 0
        return [0,0,0,0,0,0,0,0]
    
    lst = lst.split("Début:")
    lst[1] = "Début:" + lst[1]
    set_data[0] = pd.DataFrame(columns = lst)

    lst = sep.join(set_data[1].columns.values).split("Fin:")
    lst[1] = "Fin:" + lst[1]
    set_data[1] = pd.DataFrame(columns = lst)

    for i in range(2, 9, 1):
        for val in set_data[i].columns.values:
            if val.startswith("Unnamed:") and set_data[i][val].isnull().all():
                set_data[i] = set_data[i].drop(val, axis=1)

    lst = sep.join(set_data[8].columns.values)
    set_data[8] = pd.DataFrame(columns = [lst])
    #for i in [2,3,4,5,6,7]:
    #    set_data[i] = set_data[i].dropna(axis='columns', how='all')
    #Add columns for timeout
    if len(set_data[6].columns) == 0:
        set_data[6] = pd.DataFrame(columns = ["T"]) 
    if len(set_data[7].columns) == 0:
        set_data[7] = pd.DataFrame(columns = ["T"]) 
    
    # Flattening and cleaning set structure
    if (set_data[0].columns[1].split()[-1] == 'S'):
        service = ['Service', 'Reception']
    else:
        service = ['Reception', 'Service']

    #Teams
    if (set_nb % 2 == 0):
        a = 1
        b = 0
    else :
        a = 0
        b = 1
    team_data = pd.DataFrame({
        'Index':['Team 1', 'Team 2'],
        'Name':[set_data[a].columns[0], set_data[b].columns[0]],
        'Starting':[service[a], service[b]]
    })
    team_data = team_data.set_index('Index')

    #Time
    # gather the date on the tp right of the file
    time_data = set_data[8]
    time_string = time_data.columns.values[0][time_data.columns.values[0].index(' ') + 1:]
    time_string = translate_month(time_string)
    start_time = parse(time_string + " " + set_data[0].columns[1].split()[1])
    end_time = parse(time_string + " " + set_data[1].columns[1].split()[1])
    
    #Replacing data
    set_data[0] = team_data
    set_data[1] = pd.DataFrame({
        'Index':['Start', 'End'],
        'Time':[start_time.strftime("%Y-%m-%d %H:%M:%S"), end_time.strftime("%Y-%m-%d %H:%M:%S")]
    }).set_index('Index')

    i = 0
    while (len(set_data[4].columns) < 6):
        set_data[4][chr(ord('V')+i)] = None
        i += 1

    i = 0
    while (len(set_data[5].columns) < 6):
        set_data[5][chr(ord('V')+i)] = None
        i += 1
    
    #Adding correct columns to serve tab
    set_data[4].loc[-1] = set_data[4].columns.values
    set_data[4].index = set_data[4].index + 1
    set_data[4].sort_index(inplace=True)
    set_data[4].columns = ['I', 'II', 'III', 'IV', 'V', 'VI']
    set_data[4] = set_data[4].replace({
        'V':None,
        'W':None,
        'X':None,
        'Y':None,
        'Z':None,
    })
    
    set_data[5].loc[-1] = set_data[5].columns.values
    set_data[5].index = set_data[5].index + 1
    set_data[5].sort_index(inplace=True)
    set_data[5].columns = ['I', 'II', 'III', 'IV', 'V', 'VI']
    set_data[5] = set_data[5].replace({
        'V':None,
        'W':None,
        'X':None,
        'Y':None,
        'Z':None,
    })
    del set_data[8]
    #for x in set_data:
    #    print(x)

    print("Parsing set at (" + str(ref[0]) + ", " + str(ref[1]) + ") : OK") if (verbose == True) else 0
    return (set_data)

def extract_4calls(file, verbose=False):
    #TODO Win 4secs and grab all 4 sets in one query !!! You can do it EASY
    
    ref_list = [
        (73.4, 127.4),
        (73.4, 462.7),
        (167, 128.2),
        (167, 461.5),
        (261.4, 28.1)
    ]
    ref_team = [
        (261, 575.3),
        (261, 703.5)
    ]
    team_diff = 155
    set_swap = 303
    team_diff_5 = 135.1
    col = 8
    pickle = os.path.join(os.path.dirname(__file__), "format.pkl")
    
    
    ## ---------- Query with no table -------------- ##
    query_1 = tabula.read_pdf(file, area=[
        #* Check Format
        [87.8, 13.7, 165, 113],

        #* Title Data [0:7]
        [25.9, 113.8, 38.9, 429.1],
        [26.6,692.6,39.6,820.8],
        [38.2, 115.2,46,295.9 ],
        [46.1, 115.2,53.3, 295.9],
        [46.1, 352.8,53.3, 533.5 ],
        [55.4, 1.4, 71.3, 116.4 ],
        [38.9, 655.2, 50.4, 821.5 ],
        [55.4, 120.2, 72, 784.8],

        #* Set 1 [8:9]
        [(ref_list[0][0] + 3.6), (ref_list[0][1] + 0.0), (ref_list[0][0] + 13.7), (ref_list[0][1] + 154.3)], #* -18.3 pour set 5
        [(ref_list[0][0] + 3.6), (ref_list[0][1] + team_diff), (ref_list[0][0] + 13.7), (ref_list[0][1] + 154.3 + team_diff)],

        #* Set 2 [10:11]
        [(ref_list[1][0] + 3.6), (ref_list[1][1] + 0.0), (ref_list[1][0] + 13.7), (ref_list[1][1] + 154.3)], #* -18.3 pour set 5
        [(ref_list[1][0] + 3.6), (ref_list[1][1] + team_diff), (ref_list[1][0] + 13.7), (ref_list[1][1] + 154.3 + team_diff)],

        #* Set 3 [12:13]
        [(ref_list[2][0] + 3.6), (ref_list[2][1] + 0.0), (ref_list[2][0] + 13.7), (ref_list[2][1] + 154.3)], #* -18.3 pour set 5
        [(ref_list[2][0] + 3.6), (ref_list[2][1] + team_diff), (ref_list[2][0] + 13.7), (ref_list[2][1] + 154.3 + team_diff)],

        #* Set 4 [14:15]
        [(ref_list[3][0] + 3.6), (ref_list[3][1] + 0.0), (ref_list[3][0] + 13.7), (ref_list[3][1] + 154.3)], #* -18.3 pour set 5
        [(ref_list[3][0] + 3.6), (ref_list[3][1] + team_diff), (ref_list[3][0] + 13.7), (ref_list[3][1] + 154.3 + team_diff)],

        #* Time string [16]
        [39.6, 659.5, 49.7, 789.1],

        #* Teams names [17:18]
        [(ref_team[0][0] + 0.0), (ref_team[0][1] + 0.0), (ref_team[0][0] + 13.7), (ref_team[0][1] + 127.4)],
        [(ref_team[1][0] + 0.0), (ref_team[1][1] + 0.0), (ref_team[1][0] + 13.7), (ref_team[1][1] + 127.4)],
        
        #* Set 5 [19:20]
        [(ref_list[4][0] + 3.6), (ref_list[4][1] + 0.0), (ref_list[4][0] + 13.7), (ref_list[4][1] + 154.3)], #* -18.3 pour set 5
        [(ref_list[4][0] + 3.6), (ref_list[4][1] + team_diff_5), (ref_list[4][0] + 13.7), (ref_list[4][1] + 154.3 + team_diff_5)],

        #        #* Result tab [16-18?]
        #[501 + 2*col, 424, 509.8 + 2*col, 560.9],
        #[501 + 1*col, 424, 509.8 + 1*col, 560.9],
        #[501 + 0*col, 424, 509.8 + 0*col, 560.9],
        
    ], pages='1')
    
    format_ref = pd.read_pickle(pickle)
    if (not query_1):
        raise FormatInvalidError
    format_check = query_1.pop(0)
    if (not format_check.equals(format_ref)) or (query_1[8].columns.values[0] == "Début:") or (query_1[10].columns.values[0] == "Début:"):
        raise FormatInvalidError("Pdf format is wrong.")
    
    if len(query_1) == 19:
        #Means that the fifth set is empty
        query_1.append(0)
        query_1.append(0)
    #for x in range(0, len(query_1)):
    #    print(str(x) + " = "+str(query_1[x]))
    print("Query 1 done.") if (verbose == True) else 0
    timer.print_interval(time.time()) if (verbose == True) else 0
    
    ## ---------------- 4 Sets tables ------------- ##
    column_size = 20

    query_2 = tabula.read_pdf(file, area=[
        #* Set 1 [0:5]
        [(ref_list[0][0] + 14.0), (ref_list[0][1] + 0.0 ), (ref_list[0][0] + 57), (ref_list[0][1] + 118.3 )], #* +303 pour set 5
        [(ref_list[0][0] + 15.2), (ref_list[0][1] + 0.0 + team_diff), (ref_list[0][0] + 57), (ref_list[0][1] + 119 + team_diff)],
        [(ref_list[0][0] + 56.7), (ref_list[0][1] + 0.0 ), (ref_list[0][0] + 98.8), (ref_list[0][1] + 118.8 )], #* +303 pour set 5
        [(ref_list[0][0] + 56.7), (ref_list[0][1] + 0.0 + team_diff), (ref_list[0][0] + 98.6), (ref_list[0][1] + 118.8 + team_diff)],
        [(ref_list[0][0] + 64.6), (ref_list[0][1] + 118.3 ), (ref_list[0][0] + 91.5), (ref_list[0][1] + 155.1 )], #* +303 pour set 5
        [(ref_list[0][0] + 64.6), (ref_list[0][1] + 118.3 + team_diff), (ref_list[0][0] + 91.5), (ref_list[0][1] + 155 + team_diff)],

        #* Set 2 [6:11]
        [(ref_list[1][0] + 14.0), (ref_list[1][1] + 0.0 ), (ref_list[1][0] + 57), (ref_list[1][1] + 118.3 )], #* +303 pour set 5
        [(ref_list[1][0] + 15.2), (ref_list[1][1] + 0.0 + team_diff), (ref_list[1][0] + 57), (ref_list[1][1] + 119 + team_diff)],
        [(ref_list[1][0] + 56.7), (ref_list[1][1] + 0.0 ), (ref_list[1][0] + 98.8), (ref_list[1][1] + 118.8 )], #* +303 pour set 5
        [(ref_list[1][0] + 56.7), (ref_list[1][1] + 0.0 + team_diff), (ref_list[1][0] + 98.6), (ref_list[1][1] + 118.8 + team_diff)],
        [(ref_list[1][0] + 64.6), (ref_list[1][1] + 118.3 ), (ref_list[1][0] + 91.5), (ref_list[1][1] + 155.1 )], #* +303 pour set 5
        [(ref_list[1][0] + 64.6), (ref_list[1][1] + 118.3 + team_diff), (ref_list[1][0] + 91.5), (ref_list[1][1] + 155 + team_diff)],

        #* Set 3 [12:17]
        [(ref_list[2][0] + 14.0), (ref_list[2][1] + 0.0 ), (ref_list[2][0] + 57), (ref_list[2][1] + 118.3 )], #* +303 pour set 5
        [(ref_list[2][0] + 15.2), (ref_list[2][1] + 0.0 + team_diff), (ref_list[2][0] + 57), (ref_list[2][1] + 119 + team_diff)],
        [(ref_list[2][0] + 56.7), (ref_list[2][1] + 0.0 ), (ref_list[2][0] + 98.8), (ref_list[2][1] + 118.8 )], #* +303 pour set 5
        [(ref_list[2][0] + 56.7), (ref_list[2][1] + 0.0 + team_diff), (ref_list[2][0] + 98.6), (ref_list[2][1] + 118.8 + team_diff)],
        [(ref_list[2][0] + 64.6), (ref_list[2][1] + 118.3 ), (ref_list[2][0] + 91.5), (ref_list[2][1] + 155.1 )], #* +303 pour set 5
        [(ref_list[2][0] + 64.6), (ref_list[2][1] + 118.3 + team_diff), (ref_list[2][0] + 91.5), (ref_list[2][1] + 155 + team_diff)],

        #* Set 4 [18:23]
        [(ref_list[3][0] + 14.0), (ref_list[3][1] + 0.0 ), (ref_list[3][0] + 57), (ref_list[3][1] + 118.3 )], #* +303 pour set 5
        [(ref_list[3][0] + 15.2), (ref_list[3][1] + 0.0 + team_diff), (ref_list[3][0] + 57), (ref_list[3][1] + 119 + team_diff)],
        [(ref_list[3][0] + 56.7), (ref_list[3][1] + 0.0 ), (ref_list[3][0] + 98.8), (ref_list[3][1] + 118.8 )], #* +303 pour set 5
        [(ref_list[3][0] + 56.7), (ref_list[3][1] + 0.0 + team_diff), (ref_list[3][0] + 98.6), (ref_list[3][1] + 118.8 + team_diff)],
        [(ref_list[3][0] + 64.6), (ref_list[3][1] + 118.3 ), (ref_list[3][0] + 91.5), (ref_list[3][1] + 155.1 )], #* +303 pour set 5
        [(ref_list[3][0] + 64.6), (ref_list[3][1] + 118.3 + team_diff), (ref_list[3][0] + 91.5), (ref_list[3][1] + 155 + team_diff)],

    ],
    columns=[
        ref_list[0][1] +column_size*1 , ref_list[0][1] + column_size*2 , ref_list[0][1] + column_size*3 , ref_list[0][1] + column_size*4 , ref_list[0][1] + column_size*5 , ref_list[0][1] + column_size*6 , 
        ref_list[0][1] + team_diff + column_size*1, ref_list[0][1] + team_diff + column_size*2, ref_list[0][1] + team_diff + column_size*3, ref_list[0][1] + team_diff + column_size*4, ref_list[0][1] + team_diff + column_size*5, ref_list[0][1] + team_diff + column_size*6,
        ref_list[1][1] +column_size*1 , ref_list[1][1] + column_size*2 , ref_list[1][1] + column_size*3 , ref_list[1][1] + column_size*4 , ref_list[1][1] + column_size*5 , ref_list[1][1] + column_size*6 , 
        ref_list[1][1] + team_diff + column_size*1, ref_list[1][1] + team_diff + column_size*2, ref_list[1][1] + team_diff + column_size*3, ref_list[1][1] + team_diff + column_size*4, ref_list[1][1] + team_diff + column_size*5, ref_list[1][1] + team_diff + column_size*6],
    pages='1')
    if (len(query_2) == 22):
        query_2 = query_2[0:20]+[pd.DataFrame()]+[pd.DataFrame()]+query_2[20:22]
    for i in range(len(query_2)):
        for val in query_2[i].columns.values:
            if val.startswith("Unnamed:") and query_2[i][val].isnull().all():
                query_2[i] = query_2[i].drop(val, axis=1) 
    print("Query 2 done.") if (verbose == True) else 0
    timer.print_interval(time.time()) if (verbose == True) else 0
    
    ## -------- Query 3 ---------- ##
    ## Pen, Ref, Res, Teams
    team_column_size = [11.5, 100, 128.6]

    query_3 = tabula.read_pdf(file, area=[
        #Team 1 [0:3]
        [(ref_team[0][0] + 13.7), (ref_team[0][1] + 0.0), (ref_team[0][0] + 141.8), (ref_team[0][1] + 127.4)],
        [(ref_team[0][0] + 141.1), (ref_team[0][1] + 0.0), (ref_team[0][0] + 169.9), (ref_team[0][1] + 127.4)],
        [(ref_team[0][0] + 169.9), (ref_team[0][1] + 0.0), (ref_team[0][0] + 215.3), (ref_team[0][1] + 127.4)],
        
        #Team 2 [3:6]
        [(ref_team[1][0] + 13.7), (ref_team[1][1] + 0.0), (ref_team[1][0] + 141.8), (ref_team[1][1] + 127.4)],
        [(ref_team[1][0] + 141.1), (ref_team[1][1] + 0.0), (ref_team[1][0] + 169.9), (ref_team[1][1] + 127.4)],
        [(ref_team[1][0] + 169.9), (ref_team[1][1] + 0.0), (ref_team[1][0] + 215.3), (ref_team[1][1] + 127.4)],

        #Pen [6]
        [371, 13.5, 515.5, 128.2],

        #Ref [7]
        [433.4, 129, 504.1, 306.7],

        #Res [8]
        [501 + 2*col, 424, 509.8 + 2*col, 560.9],
        [501 + 1*col, 424, 509.8 + 1*col, 560.9],
        [501 + 0*col, 424, 509.8 + 0*col, 560.9],
        [501 - 1*col, 424, 509.8 - 1*col, 560.9],

        ],
        columns=[
            152, 248, 276.5, 304.7,
            ref_team[0][1] + team_column_size[0], ref_team[0][1] + team_column_size[1], ref_team[0][1] + team_column_size[2],
            ref_team[1][1] + team_column_size[0], ref_team[1][1] + team_column_size[1], ref_team[1][1] + team_column_size[2]
            ],
        pages='1')
    for i in range(len(query_3)):
            for val in query_3[i].columns.values:
                if val.startswith("Unnamed:") and query_3[i][val].isnull().all():
                    query_3[i] = query_3[i].drop(val, axis=1)
    print("Query 3 done.") if (verbose == True) else 0
    return (query_1, query_2, query_3)

def extract_team(title_data, table_data, ref, verbose=False):
    """Extract data of a team

    Args:
        file ([string]): filename of the pdf input file
        ref ([tuple]): top left point (X, Y) of the team area
        verbose (bool, optional): verbose option of the function. Defaults to False.

    Returns:
        [List(pandas.DataFrame)]: list of tables with the Team data
    """
    
    team_data = list()
    for x in title_data:
        team_data.append(x)
    for x in table_data:
        team_data.append(x)
    
    
    for i in range(0, len(team_data)):
        for val in team_data[i].columns.values:
            if val.startswith("Unnamed:") and team_data[i][val].isnull().all():
                team_data[i] = team_data[i].drop(val, axis=1) 
    team_data[0] = team_data[0].columns.values[0]
    if (team_data[0] == "xxxxx"):
        print("Empty team at ("+str(ref[0])+", "+str(ref[1])+") : KO") if (verbose == True) else 0
        return ([0,0,0,0])
    #print(team_data[1].columns)
    #If liberos or staff table is empty, fill with none 
    if (len(team_data[1]) < 6):
        raise FormatInvalidError("Wrong number of players (< 6)")
    if (len(team_data[2].columns) == 1):
        team_data[2]['1'] = None
        team_data[2]['2'] = None
    
    if (len(team_data[3].columns) == 1):
        team_data[3]['1'] = None
        team_data[3]['2'] = None

    team_data[2].columns = team_data[1].columns.values 
    team_data[3].columns = team_data[1].columns.values

    print("Parsing Team at (" + str(ref[0]) + ", " + str(ref[1]) + ") : OK") if (verbose == True) else 0
    return (team_data)

def extract_title(table_data, verbose = False):
    """Extract title of the match, division and match details

    Args:
        file ([string]): filename of the pdf input file
        verbose (bool, optional): verbose option of the function. Defaults to False.

    Returns:
        [classes.Title]: containing gathered data
    """

    
    if (table_data[0].columns[0] == "Poule"):
        print("Title Empty : KO") if (verbose == True) else 0
        raise FormatInvalidError
        return (Title(0,0,0,0,0,0,0,0,0,0,0,0))
    #Flatten and clean
    for i in range(0, len(table_data)):
        for val in table_data[i].columns.values:
            if val.startswith("Unnamed:") and table_data[i][val].isnull().all():
                table_data[i] = table_data[i].drop(val, axis=1)
    div_list = table_data[0].columns.values[0].split('-')
    div_list = [s.strip() for s in div_list]
    if (len(div_list) == 2):
        div_splt = div_list[1].split(' POULE ')
        div_list[1] = div_splt[0]
        div_list.append(str("Poule " + div_splt[1]))
    
    match_list = table_data[1].columns[0].split('-')
    if (len(div_list) < 3):
        div_list.append(" ")
        div_list.append(" ")
    
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
    time_string = table_data[6].columns.values[0][table_data[6].columns.values[0].index(' ') + 1:]
    time_string = translate_month(time_string)
    match_time = parse(time_string)
    title_data = Title(
        div_list[0],
        div_list[1].strip(),
        div_list[2].strip(),
        match_list[0].split(':')[1].strip(),
        match_list[1].split(':')[1].strip(),
        table_data[2].columns.values[0].split(':')[1].strip(),
        table_data[3].columns.values[0].split(':')[1].strip(),
        table_data[4].columns.values[0],
        table_data[5].columns.values[0],
        match_time.strftime("%Y-%m-%d %H:%M:%S"),
        table_data[7].columns.values[0],
        table_data[7].columns.values[1]
    )
    print("Title parsing : OK") if (verbose == True) else 0
    return (title_data)

def extract_result(res_data, verbose=False):
    """Extract result of the match, winner and sets score

    Args:
        file ([string]): filename of the pdf input file
        verbose (bool, optional): verbose option of the function. Defaults to False.

    Returns:
        [classes.Result]: containing winner and score data
    """
    
    winner = 0
    score = "0/0"
    
    if(res_data.columns.values[0].startswith('Vainqueur:')):
        s = " "
        winner = s.join(res_data.columns.values[0].split(' ')[1:-1])
        score = res_data.columns.values[0].split(' ')[-1]
        print("Parsing Results : OK") if (verbose == True) else 0
        return (Results(winner, score))
    else:
        print("Parsing Results : KO") if (verbose == True) else 0
        return (Results(0,0))

def is_penalty(str):
    """Check if string is of the correct penalty format

    Args:
        str ([String]): penalty string to check

    Returns:
        [Bool]: According to the check
    """

    if (str.isdigit() or str == "R" or str == "E" or str == "AE" or str == "S" or str == "M"):
        return (True)
    return (False)

def extract_penalties(pen_data):
    """Extract penalties of the match

    Args:
        file ([string]): filename of the pdf input file

    Returns:
        [List(classes.Penalty)]: list of penalties parsed
    """
    
    pens = list()

    ##Simulating penalties as didnt find samples yet
    #df = pd.DataFrame({"E":["AE"], "A/B":["B"], "Set":["5"], "Score":["15:15"]})
    #data = pd.concat([pen_data[0], df])
    #df = pd.DataFrame({"A":["17"], "A/B":["A"], "Set":["2"], "Score":["21:23"]})
    #data = pd.concat([data, df])
    for i in range(0, len(pen_data)):
        for val in pen_data[i].columns.values:
            if val.startswith("Unnamed:") and pen_data[i][val].isnull().all():
                pen_data[i] = pen_data[i].drop(val, axis=1)
    data = pen_data[0]
    if (data.empty == False):
        for i in range(len(data)):
            for j in range(4):
                if (data.iloc[i, j] != 0 and is_penalty(str(data.iloc[i, j]))):
                    pen = Penalty(data.columns.values[j], data.iloc[i, j], data.iloc[i, 4], data.iloc[i, 5], data.iloc[i, 6])
                    pens.append(pen)
    return (pens)

def extract_match(file, verbose=False):

    # ? Optimisation testing 
    #test = extract_opti_test(file, (73.4, 127.4), 1)
    timer.print_interval(time.time()) if (verbose == True) else 0
    query_1, query_2, query_3 = extract_4calls(file)
    timer.print_interval(time.time()) if (verbose == True) else 0
    
    ## ----------------------  Extract title data  ----------------------------- ##
    table_data = query_1[0:8]
    title = extract_title(table_data, verbose)
    timer.print_interval(time.time()) if (verbose == True) else 0

    ## ----------------------  Extract set data  ------------------------------- ##
    #Set 1 (73.4, 127.4)
    title_data = query_1[8:10] + [query_1[16]]
    table_data = query_2[0:6]

    set1 = extract_set((73.4, 127.4), 1, title_data, table_data, verbose)
    timer.print_interval(time.time()) if (verbose == True) else 0
    
    #Set 2 (73.4, 462.7)
    title_data = query_1[10:12] + [query_1[16]]
    table_data = query_2[6:12]
    set2 = extract_set((73.4, 462.7), 2, title_data, table_data, verbose)
    timer.print_interval(time.time()) if (verbose == True) else 0

    #Set 3 (167, 128.2)
    title_data = query_1[12:14] + [query_1[16]]
    table_data = query_2[12:18]
    set3 = extract_set((167, 128.2), 3, title_data, table_data, verbose)
    timer.print_interval(time.time()) if (verbose == True) else 0

    #Set 4 (167, 461.5)
    title_data = query_1[14:16] + [query_1[16]]
    table_data = query_2[18:24]
    set4 = extract_set((167, 461.5), 4, title_data, table_data, verbose)
    timer.print_interval(time.time()) if (verbose == True) else 0

    #Set 5 (261.4, 28.1)
    table_data = get_set_data(file, (261.4, 28.1), 5)
    if ((not isinstance(query_1[19] , pd.DataFrame)) and query_1[19] == 0):
        title_data = 0
    else:
        title_data = query_1[19:21]+ [query_1[16]]
    set5 = extract_set((261.4, 28.1), 5, title_data, table_data, verbose)
    timer.print_interval(time.time()) if (verbose == True) else 0
 
    # Gathering info into single dataframe
    sets = pd.DataFrame({
        'Index' : ['Set 1', 'Set 2', 'Set 3', 'Set 4', 'Set 5'],
        'Teams':[set1[0], set2[0], set3[0], set4[0], set5[0]],
        'Time':[set1[1], set2[1], set3[1], set4[1], set5[1]],
        'Substitutions 1':[set1[2], set2[3], set3[2], set4[3], set5[2]],
        'Substitutions 2':[set1[3], set2[2], set3[3], set4[2], set5[3]],
        'Serves 1':[set1[4], set2[5], set3[4], set4[5], set5[4]],
        'Serves 2':[set1[5], set2[4], set3[5], set4[4], set5[5]],
        'Timeouts 1':[set1[6], set2[7], set3[6], set4[7], set5[6]],
        'Timeouts 2':[set1[7], set2[6], set3[7], set4[6], set5[7]],
                        }).set_index('Index')
    
    ## ----------------------  Extract team data ------------------------------- ##
    #Team 1 (261, 575.3)
    ref = (261, 575.3)
    column_size = [11.5, 100, 128.2]
    title_data = [query_1[17]]
    table_data = query_3[0:3]
    team1 = extract_team(title_data, table_data, (261, 575.3), verbose)
    timer.print_interval(time.time()) if (verbose == True) else 0
    #Team 2 (261, 702.7)
    ref = (261, 702.7)
    column_size = [11.5, 100, 128.2]
    title_data = [query_1[18]]
    table_data = query_3[3:6]
    team2 = extract_team(title_data, table_data, (261, 702.7), verbose)
    timer.print_interval(time.time()) if (verbose == True) else 0

    #Gathering info into single dataframe
    if (set1[0]['Name'][0] not in team1[0]):
        team_tmp = team1
        team1= team2
        team2 = team_tmp
    teams = pd.DataFrame({
        'Index':['Team 1', 'Team 2'],
        'Name':[team1[0], team2[0]],
        'Players':[team1[1], team2[1]],
        'Liberos':[team1[2], team2[2]],
        'Officials':[team1[3], team2[3]]
    }).set_index('Index')
    #print(teams)

    ## ----------------------  Extract results data ---------------------------- ##
    col = 8
    res_data = [query_3[8]]
    result = extract_result(res_data[0], verbose)
    timer.print_interval(time.time()) if (verbose == True) else 0

    ## ----------------------  Extract penalties data -------------------------- ##
    pen_data = [query_3[6]]
    pens = extract_penalties(pen_data)
    penalties = pd.DataFrame.from_records([p.to_dict() for p in pens])
    print("Parsing Penalties : OK") if (verbose == True) else 0
    timer.print_interval(time.time())if (verbose == True) else 0
    
    ## ----------------------  Extract referees data --------------------------- ##
    refs = query_3[7].set_index('Arbitres')
    print("Parsing Referees : OK") if (verbose == True) else 0
    timer.print_interval(time.time())if (verbose == True) else 0
    
    ## ----------------------  Gather in Match Structure ----------------------- ##
    #Gathering into a Match dataframe
    title_dict = title.__dict__
    result_dict = result.__dict__
    
    match = pd.DataFrame({
        'Index': ['Title', 'Sets', 'Teams', 'Results', 'Referees', 'Penalties'],
        'Match': [title_dict ,sets, teams, result_dict, refs, penalties]
    }).set_index('Index')
    timer.print_interval(time.time())if (verbose == True) else 0

    return (match)

def extract_pdf(file, output_folder, verbose=False):
    """Extract match dataframe from pdf

    Args:
        file (string): path to file
        output_folder (string): path to output folder
        verbose (bool, optional): verbose option of program. Defaults to False.

    Returns:
        pandas.DataFrame = frame containing all interesting data from match sheet
    """
    filename = os.path.split(file)[1].split('.pdf')[0]
    output = os.path.join(output_folder ,filename+".json")
    print("Extracting data from file : " + file) if (verbose == True) else 0
    print("Writing to : " + output) if (verbose == True) else 0

    ## * Trying to open the input file, return empty dataframe upon failure
    try :
        fd = open(file)
        fd.close()
    except IOError:
        print("Input file not accessible :")
        print(file)
        return pd.DataFrame()
    
    match = extract_match(file, verbose)
    ## ---------------------  Exporting data to Json  -------------------------- ##
    json_output = match.to_json(indent=4, force_ascii=True)
    with open(output,'w', encoding='utf-8') as outfile:
        outfile.write(json_output)
        print(output + " saved.") if (verbose == True) else 0
    return (match)

def find(name, path):
    for root, dirs, files in os.walk(path):
        if name in files:
            return os.path.join(root, name)

if __name__ == "__main__":
    """ Main function of extracting data """
    filename = "sample_test.pdf"
    #output_folder = "./parsed_matches/2019-2020/E.Fém.B"
    output_folder = "./extraction/json"
    debug = os.path.join(os.path.dirname(__file__), "../data/2019-2020/N3F.C/3FC022.pdf")
    timer = Timer()
    
    #? opening error_files:
    with open('error_files.csv', newline='\n') as f:
        reader = csv.reader(f)
        data = list(reader)
    files = [find(item, './data') for sublist in data for item in sublist]

    for file in files:
        try :
            #pdf = extract_pdf(debug, output_folder, False)
            #webbrowser.open_new(file)
            pdf = extract_pdf(file, output_folder, False)
        except FormatInvalidError as e:
            print("FormatInvalidError : %s at %s" % (str(e), str(file)))
        