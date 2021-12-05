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
                    <p v-if="notEmpty">Create new forms <router-link to="/create_form">here...</router-link></p>

                    <form-card
                        v-for="form in forms"
                        :key="form.id"
                        :obj="form"
                        @itemsChanged="handleItemsChanged"
                    />

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
        }
    },
    methods: {
        isFormsObjectEmpty() {
            if (Object.keys(this.forms).length === 0) this.notEmpty = true;
            else this.notEmpty = false;
        },
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
        async handleItemsChanged(arg) {
            switch (arg)
            {
                case "loadingOn": {
                    //console.log("loadingOn")
                    this.loading = true;
                    break;
                }
                case "loadingOff": {
                    //console.log("loadingOff")
                    this.loading = false;
                    break;
                }
                case "itemsUpdated": {
                    //console.log("itemsUpdated")
                    await this.getForms().then(() => {
                        this.isFormsObjectEmpty();
                        this.loading = false;
                    })
                    break;
                }
                default: {
                    console.log("unhandled purpose");
                    break;
                }
            }


        },
    }
};
</script>
