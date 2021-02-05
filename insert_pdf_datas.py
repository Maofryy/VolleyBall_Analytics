import sqlite3
import json

# Get Json
datas = open("datas.json", "r").read()
datas_dict = json.loads(datas)

# check if datas exist
try:
    datas_dict['match_date_start']
    datas_dict['match_date_end']
    datas_dict['team1_name']
    datas_dict['team2_name']
    datas_dict['match_actions']
    datas_dict['team1_players']
    datas_dict['team2_players']
    datas_dict['season']
except NameError:
    print("Error during reading Json")
else:
    print("JSON Ok")

#todo Connexion bdd
conn = sqlite3.connect('ma_base.db')
cursor = conn.cursor()

# Check if match already insert
cursor.execute("""
    SELECT * FROM match
    WHERE date_start = :match_date_start
    AND date_end = :match_date_end
    AND team_home_id = :team1_name
    AND team_out_id = :team2_name
""", datas_dict)
res = cursor.fetchone()

if not res:
    return false # match already exist

#todo insert match
db.close()


