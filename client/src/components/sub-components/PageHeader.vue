<template>
  <header class="outer">
    <h1 class="head" :class="{'mb-0': msg}"><slot></slot></h1>
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
}

.subhead {
  overflow-wrap: break-word;
}

.mb-0 {
  margin-bottom: 0;
}

.outer {
  box-sizing: border-box;
  background-color: var(--seccolor);
  padding: 10px 50px;
  min-height: 160px;
  color: var(--tercolor);
}
</style>
