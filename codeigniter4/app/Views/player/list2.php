<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Joueurs</h1>
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Liste des joueurs</h6>
        </div>
        <div class="card-body">
            <div id="vapp" class="table-responsive">
                <!--<vuetable ref="vuetable"
                    api-url="<?= base_url() . '/public/Players/playerListAjax'?>"
                    :fields="fields"
                    @vuetable:pagination-data="onPaginationData"
                    class="table table-bordered"
                    pagination-path="links.pagination"
                ></vuetable>-->
                <vuetable ref="vuetable"
                    api-url="https://vuetable.ratiw.net/api/users"
                    :fields="fields"
                    @vuetable:pagination-data="onPaginationData"
                    class="table table-bordered"
                    pagination-path=""
                ></vuetable>
               
                <vuetable-pagination ref="pagination"
                    @vuetable-pagination:change-page="onChangePage"
                ></vuetable-pagination>
                
            </div>
        </div>
    </div>
</div>
<script src="<?= base_url()?>/dist/players-list.js"></script>