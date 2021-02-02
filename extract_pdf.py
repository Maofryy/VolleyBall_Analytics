## Import as table (in dataframe)
import tabula
from datetime import datetime

class Set:
    def __init__(self):
        #Basic parsing
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
        self.team1 = df[0].columns[0]
        self.team2 = df[1].columns[0]

        """# Can store in time format but not needed
        self.start = datetime.strptime(df[0].columns[1].split()[1], "%H:%M")
        self.end = datetime.strptime(df[1].columns[1].split()[1], "%H:%M")
        """

        self.start = df[0].columns[1].split()[1]
        self.end = df[1].columns[1].split()[1]

        if (df[0].columns[1].split()[2] == 'S'):
            team_serving = self.team1
        else:
            team_serving = self.team2

        print(self.team1 + " vs " + self.team2)
        print("from " + self.start + " to " + self.end)
        print(team_serving + " to serve.")


class Match:
    def __init__(self, title, sets, teamA, teamB, referees, results, penalization):
        self.title = title
        self.sets = sets
        self.teamA = teamA
        self.teamB = teamB
        self.referees = referees
        self.results = results
        self.penalization = penalization


def extract_set(file, ref):
    #Test to see if set isnt empty
    test_data = tabula.read_pdf(file, area=[[(ref[0] + 0.0), (ref[1] + 0.0), (ref[0] + 13.7), (ref[1] + 154.3)]], pages=1)
    if (test_data[0].columns.size == 1):
        print("Empty set at (" + str(ref[0]) + ", " + str(ref[1]) + ")")
        return
    set_data = tabula.read_pdf(file, area=[
                                            # 0 : Team 1 (left)
                                            [(ref[0] + 0.0), (ref[1] + 0.0), (ref[0] + 13.7), (ref[1] + 154.3)],
                                            # 1 : Team 2
                                            [(ref[0] + 0.8), (ref[1] + 154.5), (ref[0] + 13.7), (ref[1] + 310.6)],
                                            #Area template
                                            #[(ref[0] + ), (ref[1] + ), (ref[0] + ), (ref[1] + )],
                                            # 2 : Substitutions Team 1
                                            [(ref[0] + 15.2), (ref[1] + 0.0), (ref[0] + 57.0), (ref[1] + 118.3)],
                                            # 3 : Substitutions Team 2
                                            [(ref[0] + 15.2), (ref[1] + 155.1), (ref[0] + 57.0), (ref[1] + 274.1)],
                                            # 4 : Serves Team 1
                                            [(ref[0] + 57.6), (ref[1] + 0.0), (ref[0] + 90.8), (ref[1] + 118.3)],
                                            # 5 : Serves Team 2
                                            [(ref[0] + 57.6), (ref[1] + 154.3), (ref[0] + 90.6), (ref[1] + 273.9)],
                                            # 6 : Time outs Team 1
                                            [(ref[0] + 64.6), (ref[1] + 118.3), (ref[0] + 90.8), (ref[1] + 155.1)],
                                            # 7 : Time outs Team 2
                                            [(ref[0] + 64.6), (ref[1] + 273.9), (ref[0] + 90.8), (ref[1] + 310.6)]
                                          ], pages='1')
    #print(set_data)
    set_data[2] = set_data[2].rename(columns={'Unnamed: 0':'I'})
    set_data[3] = set_data[3].rename(columns={'Unnamed: 0':'I'})

    set_data[4] = set_data[4].dropna(axis=1, how="all")
    set_data[5] = set_data[5].dropna(axis=1, how="all")
    #print(set_data)
    obj = Set()
    obj.read_df(set_data)
    return set_data

#Set 2
set_data = extract_set("ffvolley_fdme.php.pdf", (73.4, 462.7))

#Set 4 (empty test case)
#set_data = extract_set("ffvolley_fdme.php.pdf", (167.8, 461.5))