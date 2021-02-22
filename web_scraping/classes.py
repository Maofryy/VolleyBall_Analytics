

class Match_line:
    def __init__(self, match_code=0, date=0, team1=0, team2=0, match_score=0, sets_score=0, total_points=0, referees=0, match_sheet=0, gym_name=0):
        self.match_code = match_code
        self.date = date
        self.team1 = team1
        self.team2 = team2
        self.match_score = match_score
        self.sets_score = sets_score
        self.total_points = total_points
        self.referees = referees
        self.match_sheet = match_sheet
        self.gym_name = gym_name

    def to_line(self):
        return ([self.match_code, self.date, self.team1, self.team2, self.match_score, self.sets_score, self.total_points, self.referees, self.match_sheet, self.gym_name])
