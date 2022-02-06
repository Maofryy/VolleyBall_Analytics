import ColoredBox from '../components/ColoredBox.vue'

let app = new Vue({
  el: '#vapp',
  data: { 
   display: 'redbox' 
  },
  components: {
    ColoredBox
  },
  methods: {
    toggleBox() {
      this.display == 'redbox' ? this.display = 'greenbox' : this.display = 'redbox'
    }
  }
})