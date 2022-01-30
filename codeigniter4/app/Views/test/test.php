<DOCTYPE html>
<html>
<head>
  <title>Vue Demo #1: The Box App</title>
  <meta charset='utf-8' />
  <script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
  <style type="text/css">
    .box {
      height: 200px;
      width: 200px; 
      text-align: center;
    }
    .red {
      background-color: red;
    }
    .green {
      background-color: green;
    }
  </style>  
</head>
<body>
  <h1>Vue Demo #1</h1>
  <div id="vapp">
      <colored-box class="red" v-show="display == 'redbox'"></colored-box>
      <colored-box class="green" v-show="display == 'greenbox'"></colored-box>
  </div>
  <script type="module" src="<?= base_url().'/public/js/test.js';?>"></script>
<script type="module">
Vue.component('ColoredBox', {
  template: "<div class=\"box\"><button v-on:click=\"toggleMe()\">Toggle Now</button></div>",
  methods: {
    toggleMe() {
      this.$root.toggleBox()
    }
  }
})

const vueApp = new Vue({
  el: '#vapp',
  data: { 
   display: 'redbox' 
  },
  methods: {
    toggleBox() {
      this.display == 'redbox' ? this.display = 'greenbox' : this.display = 'redbox'
    }
  }
})
</script>
</body>
</html>