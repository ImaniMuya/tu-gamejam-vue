<template>
  <div>Attempting Login...</div>
</template>

<script>
export default {
  name: "Login",
  methods: {
    getFutureTimestamp(days) {
      days = days || 1;
      let date = new Date();
      date.setTime(date.getTime()+(days*24*60*60*1000));
      return date.toGMTString();
    }
  },

  created() {
    const teamId = this.$route.query.t;
    const secret = this.$route.query.s;

    this.message = "Attempting Login...";
    this.$router.push({ name: 'Home'});
    if (teamId == null || secret == null) {
      this.$emit("toast", "Please use the emailed link to log in.");
      return;
    }

    document.cookie = `gjt=${teamId}; expires=${this.getFutureTimestamp(3)}`;
    document.cookie = `gjs=${secret}; expires=${this.getFutureTimestamp(3)}`;
    this.$emit("login");
  }
}
</script>

<style scoped>
div {
  text-align: center;
  padding: 50px;
}
</style>
