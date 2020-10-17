<template>
  <header class="outer">
    <h1 class="head" :class="{'p-0': msg}"><slot></slot></h1>
    <h2 class="subhead" v-if="msg">{{msg}}</h2>
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
.head, .subhead {
  text-align: center;
  font-variant: small-caps;
}

.head {
  font-weight: bold;
  font-size: 70px;
  margin: 0;
  padding: 60px 0 45px;
}

.subhead {
  overflow-wrap: break-word;
  margin: 0;
  padding: 20px 0;
}

.p-0 {
  padding-bottom: 0;
}

.outer {
  box-sizing: border-box;
  background-color: var(--seccolor);
  min-height: 160px;
  color: var(--tercolor);
}
</style>
