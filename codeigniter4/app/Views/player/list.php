<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Joueurs</h1>
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Liste des joueurs</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="players" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Licence</th>
                            <th>Numéro</th>
                            <th>Equipe</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($players as $player): ?>
                            <tr>
                                <td><a href="<?= base_url() . '/public/Players/view/' . $player->licence;?>"><?= $player->first_name . ' ' . $player->last_name; ?></a></td>
                                <td><?= $player->licence; ?></td>
                                <td><?= $player->number; ?></td>
                                <td><?= $player->team_name; ?></td>
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