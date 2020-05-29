<template>
  <header>
    <h1 :class="{'mb-0': msg}"><slot></slot></h1>
    <h2 v-if="msg">{{msg}}</h2>
  </header>
</template>

<script>
export default {
  name: "PageHeader",
  props: {
    msg: String
  }
}
</script>

<style scoped>
h1, h2{
  text-align: center;
  font-variant: small-caps;
}

h1 {
  font-weight: bold;
  font-size: 70px;
}

.mb-0 {
  margin-bottom: 0;
}

header {
  box-sizing: border-box;
  background-color: var(--seccolor);
  padding: 10px 50px;
  min-height: 160px;
  color: var(--tercolor);
}
</style>
