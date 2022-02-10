import Vuetable from 'vuetable-2'
import Vue from 'vue/dist/vue.js';
import VuetablePagination  from 'vue-pagination-2'

let app = new Vue({
  el: '#vapp',
  data: { 
    /*fields: [
      {
        title: 'Prénom',
        name: 'first_name',
        sortField: 'first_name'
      },
      {
        title: 'Nom',
        name: 'last_name',
        sortField: 'last_name'
      },
      {
        name: 'licence',
        title: 'licence',
        sortField: 'licence'
      }
    ],*/
    fields:['name', 'email', 'birthdate', 'gender'],
    links: {
      "pagination": {
        "total": 2,
        "num_results": 2,
        "per_page": 5,
        "num_pages": 1,
        "current_page": 1,
        "skip": 0,
        "from": 1,
        "to": 10,
        "last_page": 1
      }
    },
  },
  components: {
    Vuetable,
    VuetablePagination
  },
  methods: {
    onPaginationData (paginationData) {
      //this.$refs.pagination.setPaginationData(paginationData)
    },
    onChangePage (page) {
      //this.$refs.vuetable.changePage(page)
    }
  }
})