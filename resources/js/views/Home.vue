<template>
    <div class="home container">
        <div v-if="!this.$store.getters['authenticated']">
            <br>
            <h1>Surveys Application</h1>
            <p>Do you want to create your own survey?
                <router-link to="/login">Log in</router-link>
                your account to start!<br>
                Don't you have any account?
                <router-link to="/register">Register here.</router-link>
            </p>

        </div>
        <div v-else>
            <h1>Welcome, {{ loggedUserName }}.</h1>
            <hr>
            <FormPreview v-for="form in forms" :key="form.id" :created_at="form.created_at"
                         :description="form.description" :name="form.name" :slug="getSlugPath(form)"></FormPreview>
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
    computed: {
        loggedUserName() {
            if (this.$store.getters['user']) {
                return this.$store.getters['user'].name;
            } else return "";
        }
    },
    methods: {
        async getForms() {
            if (this.$store.getters['authenticated']) {
                try {
                    const getForms = await Form.getAllForms();
                    if (getForms) {
                        this.forms = getForms.data;
                    }
                } catch (e) {
                    console.log(e);
                }
            }

        },
        getSlugPath(form) {
            if (this.forms !== {} && form !== null) {
                return "/form/" + form.slug;
            }
        }

    }
};
</script>
