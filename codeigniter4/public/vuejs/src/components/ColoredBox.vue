<template>
  <div class="box"><button v-on:click="toggleMe()">Toggle Now</button></div>
</template>

<script>
export default {
  name: 'ColoredBox',
  props: {
    msg: String
  },
  methods: {
    toggleMe() {
      this.$root.toggleBox()
    }
  }
}
</script>

<style scoped>
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
