<template>
  <div>test</div>
</template>

<script>
export default {
  name: 'ColoredBox',
  props: {
    msg: String
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
