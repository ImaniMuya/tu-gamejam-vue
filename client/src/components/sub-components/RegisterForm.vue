<template>
  <form @submit="handleSubmit" novalidate="true" :class="{submitting}">
    <ul class="errors">
      <li v-for="(error, index) in errors" :key=index>
        {{error}}
      </li>
    </ul>
    <div class="grid">
      <label for="regCode">Code:</label>
      <input type="text" id="regCode" name="regCode" v-model="regCode" />
      <label for="tname">Team Name:</label>
      <input type="text" id="tname" name="tname" v-model="teamName" maxlength="100" />
      <label for="lname">Leader Name:</label>
      <input type="text" id="lname" name="lname" v-model="leaderName" maxlength="100" />
      <label for="email">Leader Taylor Email:</label>
      <input type="text" id="email" name="email" v-model="email" maxlength="100" />
    </div>
    <input class="submitbtn" type="submit" />
    <Loader v-if="submitting" class="absolute-center" />
  </form>
</template>

<script>
import Loader from './Loader.vue';
import { emailRE, serverURL } from "../../constants";
export default {
  name: "RegisterForm",
  data() {
    return {
      regCode: "",
      teamName: "",
      leaderName: "",
      email: "",
      errors: [],
      submitting: false
    }
  },
  components: {Loader},
  methods: {
    handleSubmit(e) {
      e.preventDefault();
      this.errors = [];
      if (!this.regCode) this.errors.push("Registration Code Required.");
      if (!this.teamName) this.errors.push("Team Name Required.");
      if (!this.leaderName) this.errors.push("Leader Name Required.");
      if (!this.email) this.errors.push("Leader Email Required.");
      else if (!emailRE.test(this.email.toLowerCase())) {
        this.errors.push("Email is Invalid!");
      }

      if (this.errors.length > 0) return;

      const formData = {
        regCode: this.regCode,
        teamName: this.teamName,
        leaderName: this.leaderName,
        email: this.email
      };
      
      const url = serverURL + "/register.php";
      this.postData(url, formData).then(resp => {
        this.$router.push({ name: 'Home'});
        this.$emit("toast", resp)
      }).catch( (err) => {
        this.errors.push(err);
      }).finally(() => this.submitting = false);
    },

    postData(url, data) {
      this.submitting = true;
      return new Promise( (resolve, reject) => {
        fetch(url, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify(data)
        }).then( response => {
          if (response.status == 201) response.text().then(text => resolve(text));
          else response.text().then(text => reject(text, response.status));
        });
      });
    }
  },
};
</script>

<style scoped>
.grid {
  display: grid;
  grid-template-columns: auto auto;
  gap: 10px 20px;
  margin: 0 20px;
  align-items: center;
}

label {
  text-align: right;
}

@media screen and (max-width: 500px) {
  .grid {
    grid-template-columns: auto;
  }
  
  label {
    text-align: left;
  }
}

form {
  padding-top: 40px;
  flex: 1;
  position: relative;
}

form.submitting {
  opacity: 0.5;
  background-color: #777;
}

.submitbtn {
  display: block;
  margin: 10px auto;
}

.absolute-center {
  margin: auto;
  position: absolute;
  top: 0; left: 0; bottom: 0; right: 0;
}

ul.errors {
  font-size: 13px;
  color: red;
}

</style>
