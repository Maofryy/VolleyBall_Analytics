## Import as table (in dataframe)
import tabula
import json
import pandas as pd
#from datetime import datetime

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

        self.subs1 = df[2]
        self.subs2 = df[3]

        self.serves1 = df[4]
        self.serves2 = df[5]

        self.timeout1 = df[6]
        self.timeout2 = df[7] 
        
        """print(self.team1 + " vs " + self.team2)
        print("from " + self.start + " to " + self.end)
        print(team_serving + " to serve.")
        print(self.subs1)
        print(self.subs2)
        print(self.serves1)
        print(self.serves2)
        print(self.timeout1)
        print(self.timeout2)"""
    
    def export_json(self, file):
        jsonStr = json.dumps(self.__dict__)
        print(jsonStr)



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
    if ((not test_data) or (test_data[0].columns.size == 1)):
        print("Empty set at (" + str(ref[0]) + ", " + str(ref[1]) + ")")
        return pd.DataFrame({label: []})
    set_data = list()
    # 0 : Team 1 (left)
    set_data.append(tabula.read_pdf(file, area=[(ref[0] + 0.0), (ref[1] + 0.0), (ref[0] + 13.7), (ref[1] + 154.3)], pages='1'))
    # 1 : Team 2
    set_data.append(tabula.read_pdf(file, area=[(ref[0] + 0.8), (ref[1] + 154.5), (ref[0] + 13.7), (ref[1] + 310.6)], pages='1'))
    #Area template
    #[(ref[0] + ), (ref[1] + ), (ref[0] + ), (ref[1] + )],
    #Size of columns in the sub and serve table
    column_size = 20
    #Diff between the two teams tables in the first (y) axis
    team_diff = 155.1
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
    set_data[0] = set_data[0][0]
    set_data[1] = set_data[1][0]

    set_data[2] = set_data[2][0]
    set_data[3] = set_data[3][0]

    set_data[4] = set_data[4][0]
    set_data[5] = set_data[5][0]
    
    set_data[6] = set_data[6][0]
    set_data[7] = set_data[7][0]
    
    
    """for dt in set_data:
        print(dt)"""
    #obj = Set()
    #obj.read_df(set_data)
    
    return set_data

if __name__ == "__main__":

    ## Extract set data
    #Set 1 (73.4, 127.4)
    set1 = extract_set("ffvolley_fdme.php.pdf", (73.4, 127.4))
    #set1 = pd.DataFrame({'Set 1':[ret[0].columns.values, ret[1].columns.values, ret[2], ret[3], ret[4], ret[5], ret[6], ret[7]]})
    #set1 = set1.rename(index={0:'Team 1', 1:'Team 2', 2:'Substitutions 1', 3:'Substitutions 2', 4:'Serves 1', 5:'Serves 2', 6:'Timeouts Team 1', 7:'Timeouts Team 2'})
    #print(set1)

    #Set 2 (73.4, 462.7)
    set2 = extract_set("ffvolley_fdme.php.pdf", (73.4, 127.4))
    #set2 = pd.DataFrame({'Set 2':[ret[0].columns.values, ret[1].columns.values, ret[2], ret[3], ret[4], ret[5], ret[6], ret[7]]})
    #set2 = set1.rename(index={0:'Team 1', 1:'Team 2', 2:'Substitutions 1', 3:'Substitutions 2', 4:'Serves 1', 5:'Serves 2', 6:'Timeouts Team 1', 7:'Timeouts Team 2'})
    #print(set2)

    #Set 3 (167, 128.2)
    set3 = extract_set("ffvolley_fdme.php.pdf", (73.4, 127.4))
    #set3 = pd.DataFrame({'Set 3':[y})
    #set3 = set1.rename(index={0:'Team 1', 1:'Team 2', 2:'Substitutions 1', 3:'Substitutions 2', 4:'Serves 1', 5:'Serves 2', 6:'Timeouts Team 1', 7:'Timeouts Team 2'})
    #print(set3)

    #Set 4 (167, 461.5)
    set4 = extract_set("ffvolley_fdme.php.pdf", (73.4, 127.4))
    #set4 = pd.DataFrame({'Set 4':[ret[0].columns.values, ret[1].columns.values, ret[2], ret[3], ret[4], ret[5], ret[6], ret[7]]})
    #set4 = set1.rename(index={0:'Team 1', 1:'Team 2', 2:'Substitutions 1', 3:'Substitutions 2', 4:'Serves 1', 5:'Serves 2', 6:'Timeouts Team 1', 7:'Timeouts Team 2'})
    #print(set4)

    #Set 5 (261.4, 28.1)
    set5 = extract_set("ffvolley_fdme.php.pdf", (73.4, 127.4))
    #set5 = pd.DataFrame({'Set 5':[ret[0].columns.values, ret[1].columns.values, ret[2], ret[3], ret[4], ret[5], ret[6], ret[7]]})
    #set5 = set1.rename(index={0:'Team 1', 1:'Team 2', 2:'Substitutions 1', 3:'Substitutions 2', 4:'Serves 1', 5:'Serves 2', 6:'Timeouts Team 1', 7:'Timeouts Team 2'})
    #print(set5)


    """df_to_export = pd.DataFrame({'Sets':[[set1[0].columns.values, set1[1].columns.values, set1[2], set1[3], set1[4], set1[5], set1[6], set1[7]], 
                                        [set2[0].columns.values, set2[1].columns.values, set2[2], set2[3], set2[4], set2[5], set2[6], set2[7]], 
                                        [set3[0].columns.values, set3[1].columns.values, set3[2], set3[3], set3[4], set3[5], set3[6], set3[7]], 
                                        [set4[0].columns.values, set4[1].columns.values, set4[2], set4[3], set4[4], set4[5], set4[6], set4[7]], 
                                        [set5[0].columns.values, set5[1].columns.values, set5[2], set5[3], set5[4], set5[5], set5[6], set5[7]]]})"""
    
    sets = pd.DataFrame({
        'Team 1 ':[set1[0].columns.values, set2[0].columns.values, set3[0].columns.values, set4[0].columns.values, set5[0].columns.values],
        'Team 2 ':[set1[1].columns.values, set2[1].columns.values, set3[1].columns.values, set4[1].columns.values, set5[1].columns.values],
        'Substitutions 1 ':[set1[2], set2[2], set3[2], set4[2], set5[2]],
        'Substitutions 2 ':[set1[3], set2[3], set3[3], set4[3], set5[3]],
        'Serves 1 ':[set1[4], set2[4], set3[1], set4[4], set5[4]],
        'Serves 2 ':[set1[5], set2[5], set3[5], set4[5], set5[5]],
        'Timeouts 1 ':[set1[6], set2[6], set3[6], set4[6], set5[6]],
        'Timeouts 2 ':[set1[7], set2[7], set3[7], set4[7], set5[7]],
                        })
    #sets = sets.rename(index={0:'Set 1', 1:'Set 2', 2:'Set 3', 3:'Set 4', 4:'Set 5'}, level=0)
    print(sets)
    #Exporting all sets to Json
    json_output = sets.to_json()
    print(json_output)
    with open("sets.json", 'w') as outfile:
        outfile.write(json_output)

    #Set 4 (empty test case) (167.8, 461.5)
    #set_data = extract_set("ffvolley_fdme.php.pdf", (167.8, 461.5))