import json
import tabula

import pandas as pd
import datetime

class Set:
    def __init__(self):
        """ Init with zeros"""
        #Basics        
        self.team1 = ""
        self.team2 = ""
        self.subs1 = 0
        self.subs2 = 0
        self.serves1 = 0
        self.serves2 = 0
        self.timeout1 = 0
        self.timeout2 = 0

        #Analysed
        self.team_serving = 0
        self.start = 0.0
        self.end = 0.0

    def read_df(self, df):
        """ read the datafram in arg into class parameters  """

        self.team1 = df[0].columns[0]
        self.team2 = df[1].columns[0]

        """# Can store in time format but not needed
        self.start = datetime.strptime(df[0].columns[1].split()[1], "%H:%M")
        self.end = datetime.strptime(df[1].columns[1].split()[1], "%H:%M")
        """

        self.start = df[0].columns[1].split()[1]
        self.end = df[1].columns[1].split()[1]

        #if (df[0].columns[1].split()[2] == 'S'):
        #    team_serving = self.team1
        #else:
        #    team_serving = self.team2        

        self.subs1 = df[2]
        self.subs2 = df[3]

        self.serves1 = df[4]
        self.serves2 = df[5]

        self.timeout1 = df[6]
        self.timeout2 = df[7] 
    
    def export_json(self, file):
        """ export object as a json into the file name passed """
        jsonStr = json.dumps(self.__dict__)
        print(jsonStr)



class Match:
    def __init__(self, title, sets, teamA, teamB, referees, results, penalization):
        """ Basic constructor """
        self.title = title
        self.sets = sets
        self.teamA = teamA
        self.teamB = teamB
        self.referees = referees
        self.results = results
        self.penalization = penalization


