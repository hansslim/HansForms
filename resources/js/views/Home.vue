<template>
  <div class="home container">
      <div v-if="!this.$store.getters['authenticated']">
          <br>
          <h1>Surveys Application</h1>
          <p>Do you want to create your own survey? <router-link to="/login">Log in</router-link> your account to start!<br>
             Don't you have any account? <router-link to="/register">Register here.</router-link></p>

      </div>
      <div v-else>
          <h1>Welcome, {{this.$store.getters['user'].name}}.</h1>
          <hr>
          <FormPreview v-for="form in forms" :key="form.id" :created_at="form.created_at" :description="form.description" :name="form.name" :slug="form.slug"></FormPreview>
      </div>


  </div>
</template>

<script>
import Form from "../apis/Form";
import FormPreview from "../components/FormCard";


export default {
  name: "Home",
    components: {FormPreview},
    data() {
        return {
            forms: {}
        }
    },
    mounted() {
        this.getForms();
    },
    methods: {
        async getForms() {
            if (this.$store.getters['authenticated']) {
                const getforms = await Form.getAllForms();
                this.forms = getforms.data;
            }

        }
    }
};
</script>
