<template>
  <div class="col-fill">
    <page-header msg="Create Your Team">Register</page-header>
    <register-form></register-form>
  </div>
</template>

<script>
import PageHeader from './sub-components/PageHeader.vue';
import RegisterForm from './sub-components/RegisterForm.vue';
export default {
  name: "Register",
  components: { PageHeader, RegisterForm }
}
</script>

<style>
.col-fill {
  min-height: 100vh; /* assumes parent's min-height */
  display: flex;
  flex-flow: column;
}
</style>
