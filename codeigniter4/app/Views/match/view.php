<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800"><?= $title ?></h1>
    <!-- Actions -->
    <div id="actions" class="d-flex justify-content-around col-12">
        <a href="<?= $pathpdf;?>" target="_blank" class="btn btn-primary btn-icon-split">
            <span class="icon text-white-50">
                <i class="fas fa-flag"></i>
            </span>
            <span class="text">Feuille de match</span>
        </a>

        <a href="#" class="btn btn-warning btn-icon-split">
            <span class="icon text-white-50">
                <i class="fas fa-flag"></i>
            </span>
            <span class="text">Modifier le match</span>
        </a>
        <a href="#" class="btn btn-success btn-icon-split">
            <span class="icon text-white-50">
                <i class="fas fa-flag"></i>
            </span>
            <span class="text">Ajouter un nouveau match</span>
        </a>
    </div>

    <!-- Match -->
    <div class="row">
        <div class="card mb-4 mt-4 col-12">
            <div class="card-body d-flex justify-content-between">
                <div>
                    <?= 'Ville : ' . $match->city;?></br>
                    <?= 'Salle : ' . $match->gym;?>
                </div>
                <div>
                    <strong><?= $match->ligue;?></strong>
                </div>
                <div>
                    <?= 'Match : ' . $match->match_number . ' - Jour: ' . $match->match_day;?></br>
                    <?= $match->date_match;?>
                </div>
            </div>
        </div>
    </div>

    <div class="row d-flex justify-content-around">
        <div class="row col-4">
            <div class="card mb-4">
                <div class="card-header">
                    <?= $match->team1name;?>
                </div>
                <div class="card-body">
                    <table>
                    <?php foreach ($team1 as $team) :?>
                    <tr>
                        <?= '<td>' . $team->number . '</td>'?>
                        <?= '<td>' . $team->first_name . '</td>'?>
                        <?= '<td>' . $team->last_name . '</td>'?>
                        <?= '<td>' . $team->licence . '</td>'?>
                    </tr>
                    <?php endforeach;?>
                    </table>
                </div>
            </div>
        </div>

        <div>SCORE</div>

        <div class="row col-4">
            <div class="card mb-4">
                <div class="card-header">
                    <?= $match->team2name;?>
                </div>
                <div class="card-body">
                    <table>
                    <?php foreach ($team2 as $team) :?>
                    <tr>
                        <?= '<td>' . $team->number . '</td>'?>
                        <?= '<td>' . $team->first_name . '</td>'?>
                        <?= '<td>' . $team->last_name . '</td>'?>
                        <?= '<td>' . $team->licence . '</td>'?>
                    </tr>
                    <?php endforeach;?>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Bootstrap core JavaScript-->
<script src="<?= substr(base_url(),0 ,-12) . "template/vendor/jquery/jquery.min.js"?>"></script>
<script src="<?= substr(base_url(),0 ,-12) . "template/vendor/bootstrap/js/bootstrap.bundle.min.js"?>"></script>

<!-- Core plugin JavaScript-->
<script src="<?= substr(base_url(),0 ,-12) . "template/vendor/jquery-easing/jquery.easing.min.js"?>"></script>

<script type="text/javascript">
/*var url = "<?= substr(base_url(),0 ,-12) . 'codeigniter4/public/Team/getTeams/';?>"
var teams_id = <?php echo json_encode(array($match->t1id, $match->t2id));?>;
console.log(teams_id);
    $(document).ready(function() {
        $.ajax({
            url: url,
            type: 'POST',
            data: {
                teams : teams_id
            },
            success: function (data, status, xhr) {
               console.log(data, status, xhr)
            },
            error: function (jqXhr, textStatus, errorMessage) {
                   console.log(jqXhr, textStatus, errorMessage)
            }
        });
    });*/
</script>