<template>
  <div>
    <page-header msg="Cast Your Vote!">Theme Voting</page-header>
    <loader id="page-loader" v-if="loading" :circlesNum="5"/>
  </div>
</template>

<script>
import PageHeader from './sub-components/PageHeader.vue';
import Loader from './sub-components/Loader.vue';

export default {
  name: "Vote",
  components: { PageHeader, Loader },
  data() {
    return {
      loading: true,

    }
  }

}
</script>

<style scoped>
#page-loader {
  margin: 20px auto;
}
</style>