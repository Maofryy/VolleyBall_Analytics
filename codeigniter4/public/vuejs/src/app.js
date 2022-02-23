import 'jquery/dist/jquery.min.js';
//import 'popper.js/dist/umd/popper.min.js';
//import 'bootstrap/dist/js/bootstrap.min.js';

import Vue from 'vue/dist/vue.js';
import ToDoItem from './components/ToDoItem.vue';

let app = new Vue({
   el: '#app',
   components :{
      ToDoItem
   },
   data: {
      firstname : "Jabrane",
      lastname  : "Jabri"
   }
})