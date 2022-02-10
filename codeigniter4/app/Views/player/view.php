<link href="<?= base_url(); ?>/public/css/player.css" rel="stylesheet" type="text/css">
<link href="<?= base_url(); ?>/public/css/dev.css" rel="stylesheet" type="text/css">

<div id="player" class="container-fluid player-grid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-4 text-gray-800">Player page</h1>
    </div>

    <!-- First row -->
    <div class="row">

        <!-- Player Information -->
        <div class="col-lg-4 col-xl-4 ">
            <div class="card shadow border-left-primary mb-4">
                <div class="card-header py-3">
                    <div class="font-weight-bold">
                        Player Information
                    </div>
                </div>
                <div class="card-body player-info player-content container-fluid">
                    <div class="row">
                        <div class="col-sm-6">
                            <img  :src="picture" :alt="`${first_name} ${last_name}`" style="max-width: 10rem;" />
                        </div>
                        <div class="col-sm-6">
                                <div class="row">
                                    <div class="col-xs-12">
                                        {{first_name}} {{last_name}}
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-12">
                                        Club : {{club}}
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-12">
                                        Licence : {{licence}}
                                    </div>
                                </div>
                        </div>
                    </div>
                    <div class="row">
                            <span class="col badge badge-pill tag tag-primary" >
                                <i class="fas fa-circle blue"></i>player</span>
                            <span class="col badge badge-pill tag tag-secondary" >
                                <i class="fas fa-circle blue"></i>coach</span>
                            <span class="col badge badge-pill tag tag-info" >
                                <i class="fas fa-circle blue"></i>referee</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Season Stats -->
        <div class="col-lg-8 col-xl-8 ">
            <div class="card shadow mb-4 ">
                <div class="card-header py-3">
                    <div class="row">

                        <div class="font-weight-bold col-lg-8" style="height:100%;">
                            Season Stats
                        </div>
                        <!-- <label for="season" class="col-lg-1">Season</label> -->
                        <div class="input-group col-lg-4">
                            <div class="input-group-prepend">
                                <label class="input-group-text" for="seasonSelect">Season</label>
                            </div>
                            <select class="custom-select" id="seasonSelect">
                                <option selected value="0">2021-2022</option>
                                <option value="1">2020-2021</option>
                                <option value="2">2019-2020</option>
                                <option value="3">2018-2019</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="card-body container-fluid">
                    <div class="row">

                        <div class="chart-area col-sm-7">
                            Chart of the points of the team this Season ? see chart.js <br>

                            |_________________<br>
                            |____________+--- <br>
                            |___________/<br>
                            |__________/<br>
                            |_____+---+ <br>
                            |____/<br>
                            |___+ <br>
                            ________________________<br>
                            <!-- 
                        <div class="chartjs-size-monitor">
                            <div class="chartjs-size-monitor-expand">
                                <div class=""></div>
                            </div>
                            <div class="chartjs-size-monitor-shrink">
                                <div class=""></div>
                            </div>
                        </div>
                        <canvas id="myAreaChart" style="display: block; height: 320px; width: 443px;" width="886"
                            height="640" class="chartjs-render-monitor"></canvas>
                             -->
                        </div>
                        <div class="col-sm-5">
                            <div>
                                <span>Season stats recap</span>
                                <ul>
                                    <li>Points</li>
                                    <li>Ratio</li>
                                    <li>Breakpoints on serve</li>
                                    <li>Starting matchups</li>
                                    <li>Best partner</li>
                                    <li>......</li>

                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Second row -->
    <div class="row">
        <div class="col-lg-12 col-xl-12 ">
            <div class="card shadow border-bottom-primary mb-4">
                <div class="card-header py-3">
                    <div class="font-weight-bold">
                        Match History
                    </div>
                </div>
                <div class="card-body">
                    <table class="match-history-table">
                        <thead>
                            <tr role="row">
                                <th>Number</th>
                                <th>Result</th>
                                <th>Date</th>
                                <th>Home Team</th>
                                <th>Away Team</th>
                                <th>Score</th>
                                <th><i class="fas fa-external-link-alt"></i></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="match in matches">
                                <td>{{match.number}}</td>
                                <td>
                                    <div v-if="match.result == 'win'"
                                        class="badge badge-pill badge-success text-lg text-white">Win</div>
                                    <div v-if="match.result == 'lose'"
                                        class="badge badge-pill badge-danger text-lg text-white">Lose</div>
                                </td>
                                <td>{{match.date}}</td>
                                <td>{{match.home_team}}</td>
                                <td>{{match.away_team}}</td>
                                <td>{{match.score}}</td>
                                <td>{{match.link}}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    <!-- Third Row -->
    <div class="row">
        <div class="col-lg-7">
            <div class="card shadow border-left-primary mb-4">
                <div class="card-header py-3">
                    <div class="font-weight-bold">
                        Player Stats over seasons
                    </div>
                </div>
                <div class="card-body">
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card shadow border-bottom-primary mb-4">
                <div class="card-header py-3">
                    <div class="font-weight-bold">
                        Teams History
                    </div>
                </div>
                <div class="card-body">
                    <table class="team-history-table">
                        <thead>
                            <tr role="row">
                                <th>Season</th>
                                <th>Division</th>
                                <th>Team</th>
                                <th>Club</th>
                                <th>Place</th>
                                <th><i class="fas fa-external-link-alt"></i></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="team in teams">
                                <td>{{team.season}}</td>
                                <td>{{team.division}}</td>
                                <td>{{team.team}}</td>
                                <td>{{team.club}}</td>
                                <td>{{team.place}}</td>
                                <td>{{team.link}}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>


</div>


<script src="<?= base_url()?>/dist/player-info.js"></script>