<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Joueurs</h1>

       <!-- DataTales Example -->
       <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Liste des joueurs</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="players" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Code Match</th>
                            <th>Equipe 1</th>
                            <th>Equipe 2</th>
                            <th>Ville</th>
                            <th>Gymnase</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($matchs as $match): ?>
                            <tr>
                                <td><a href="<?php echo base_url() . "/public/matchs/view/" . $match->match_number;?>"><?= $match->match_number; ?></a></td>
                                <td><?= $match->team1name; ?></td>
                                <td><?= $match->team2name; ?></td>
                                <td><?= $match->city; ?></td>
                                <td><?= $match->gym; ?></td>
                                <td><?= $match->date_match; ?></td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Page level plugins -->
<script src="../../template/vendor/datatables/jquery.dataTables.min.js"></script>
<script src="../../template/vendor/datatables/dataTables.bootstrap4.min.js"></script>

<script type="text/javascript">
    $(document).ready(function() {
        $('#players').DataTable();
    });
</script>