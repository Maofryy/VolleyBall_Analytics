import { createApp } from 'vue'
import App from './App.vue'
import ColoredBox from './App.vue'

createApp(App).mount('#app')

export default {
    name: 'App',
    components: {
      ColoredBox
    }
  }