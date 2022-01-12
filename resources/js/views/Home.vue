<template>
    <div>
        <div v-if="!loading">
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
                    <p v-if="notEmpty">Create new forms
                        <router-link to="/create_form">here...</router-link>
                    </p>

                    <div class="container-fluid">
                        <div v-for="forms in groupedForms" class="row row-eq-height">
                            <form-card
                                v-for="form in forms"
                                :key="form.id"
                                :obj="form"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div v-else>
            loading
        </div>
    </div>
</template>

<script>
import Form from "../apis/Form";
import FormCard from "../components/FormCard";

export default {
    name: "Home",
    components: {"form-card": FormCard},
    data() {
        return {
            forms: {},
            loading: true,
            notEmpty: false,
        }
    },
    async mounted() {
        await this.getForms().then(() => {
            this.isFormsObjectEmpty();
            this.loading = false;
        });
    },
    computed: {
        loggedUserName() {
            if (this.$store.getters['user']) {
                return this.$store.getters['user'].name;
            } else return "";
        },
        groupedForms() {
            return _.chunk(this.forms, 3)
        }
    },
    methods: {
        isFormsObjectEmpty() {
            this.notEmpty = Object.keys(this.forms).length === 0;
        },
        async getForms() {
            if (this.$store.getters['authenticated']) {
                try {
                    const getForms = await Form.getAllForms();
                    if (getForms && getForms.status === 200) {
                        this.forms = getForms.data;
                    } else throw new Error(getForms.status.toString())
                } catch (e) {
                    console.log(e);
                }
            }

        },
    }
};
</script>
