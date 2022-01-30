<DOCTYPE html>
<html>
<head>
  <title>Vue Demo #1: The Box App</title>
  <meta charset='utf-8' />
  <script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
</head>
<body>
  <h1>Vue Demo #1</h1>
  <div id="vapp">
      <colored-box class="red" v-show="display == 'redbox'"></colored-box>
      <colored-box class="green" v-show="display == 'greenbox'"></colored-box>
  </div>
<!-- Our View App goes at the end of the document -->
<script type="module" src="<?= base_url().'/assets/js/test.js';?>"></script>
<script  type="module">
/*Vue.component('ColoredBox', {
  template: "<div class=\"box\"><button v-on:click=\"toggleMe()\">Toggle Now</button></div>",
  methods: {
    toggleMe() {
      this.$root.toggleBox()
    }
  }
})*/
import ColoredBox from '../components/ColoredBox.vue'

const vueApp = new Vue({
  el: '#vapp',
  data: { 
   display: 'redbox' 
  },
  components: {
    ColoredBox: ColoredBox
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