<template>
    <div class="container">
        <div v-if="!this.loading && !this.errored" class="card">
            <h1>{{ this.form.name }}</h1>
            <h3>{{ this.form.description }}</h3>
            <h4>Opened to {{ this.form.end_time }}</h4>
            <FormulateForm v-model="formValues" @submit="submitForm">
                <form-element v-for="item in this.form.form_elements" :obj="item" :key="item.order"/>
                <FormulateInput
                    type="submit"
                    name="Submit this form!"
                    wrapper-class="text-center"
                    input-class="form-control w-50 m-auto"
                />
            </FormulateForm>
        </div>
        <div v-if="this.loading">
            {{ "loading" }}
        </div>
        <div v-if="this.errored">
            <h1>{{ this.errorText }}</h1>
            <router-link to="/"><h3>Go home</h3></router-link>
        </div>
    </div>
</template>

<script>
import Form from "../apis/Form";
import FormElement from "../components/FormElement";

export default {
    name: "Form",
    components: {
        "form-element": FormElement,
    },
    data() {
        return {
            slug: '',
            token: '',
            form: {},
            formValues: {},
            loading: true,
            errored: false,
            errorText: "Bad Request (400)",
            dataFetched: false
        }
    },
    async mounted() {
        if (this.$route.path.includes('/form/')) {
            this.slug = this.getSlug();
            await this.getThisForm().then(() => {
                try {
                    if (this.dataFetched) {
                        this.sortElements();
                        this.loading = false;
                    }
                } catch (error) {
                    console.log(error.message)
                    this.errored = true;
                    this.errorText = `Unhandled error - ${error}`;
                } finally {
                    this.loading = false;
                }
            });
        }
        else if (this.$route.path.includes('/private_form/')) {
            this.token = this.getToken();
            await this.getThisPrivateForm().then(() => {
                try {
                    if (this.dataFetched) {
                        this.sortElements();
                        this.loading = false;
                    }
                } catch (error) {
                    console.log(error.message)
                    this.errored = true;
                    this.errorText = `Unhandled error - ${error}`;
                } finally {
                    this.loading = false;
                }
            });
        }
        else {
            this.errored = true;
            this.loading = false;
        }
    },
    methods: {
        async getThisForm() {
            let errorCode = -1;
            await Form.getSpecificForm(this.slug).then((res) => {
                if (res.status === 200) {
                    this.form = res.data;
                    this.dataFetched = true;
                } else {
                    console.log(res.status)
                    errorCode = res.status;
                    throw new Error();
                }
            }).catch(error => {
                this.errored = true;
                switch (errorCode) {
                    case 423:
                        this.errorText = "Requested form is not available at this moment. Try it later.";
                        break;
                    case 410:
                        this.errorText = "Requested form is expired. You cannot answer this form anymore!";
                        break;
                    case 400:
                        this.errorText = "Bad request. Check your link to the form.";
                        break;
                    case 404:
                        this.errorText = "Requested form was not found.";
                        break;
                    default:
                        this.errorText = `Unhandled error - ${error}`;
                        break; //dev only
                }
                this.dataFetched = false;
            })
        },
        async getThisPrivateForm() {
            let errorCode = -1;
            await Form.getSpecificPrivateForm(this.token).then((res) => {
                if (res.status === 200) {
                    this.form = res.data;
                    this.dataFetched = true;
                } else {
                    console.log(res.status)
                    errorCode = res.status;
                    throw new Error();
                }
            }).catch(error => {
                this.errored = true;
                switch (errorCode) {
                    case 423:
                        this.errorText = "Requested form is not available at this moment. Try it later.";
                        break;
                    case 410:
                        this.errorText = "Requested form or access token is expired. You cannot answer this form anymore!";
                        break;
                    case 400:
                        this.errorText = "Bad request. Check your link to the form.";
                        break;
                    case 404:
                        this.errorText = "Requested form was not found.";
                        break;
                    default:
                        this.errorText = `Unhandled error - ${error}`;
                        break; //dev only
                }
                this.dataFetched = false;
            })
        },
        getSlug() {
            return this.$route.params['slug'] ?? '';
        },
        getToken() {
            return this.$route.params['token'] ?? '';
        },
        sortElements() {
            this.form.form_elements.sort((a, b) => {
                if (a.order < b.order) {
                    return -1;
                }
                if (a.order > b.order) {
                    return 1;
                }
                return 0;
            });
        },
        async submitForm() {
            this.loading = true;
            if (this.slug) {
                await Form.postFormCompletion(this.formValues, this.slug).then((res) => {
                    if (res.status === 200) {
                        alert("Answer has been proceeded successfully.");
                        this.$router.push("/");
                    } else {
                        alert(`Form completion is invalid (${res.data}).`);
                        console.log(res.data);
                    }
                })
            }
            else if (this.token) {
                await Form.postPrivateFormCompletion(this.formValues, this.token).then((res) => {
                    if (res.status === 200) {
                        alert("Answer has been proceeded successfully.");
                        this.$router.push("/");
                    } else {
                        alert(`Form completion is invalid (${res.data}).`);
                        console.log(res.data);
                    }
                })
            }

            this.loading = false;
        }
    },
    computed: {
        authenticated() {
            return this.$store.getters['authenticated'];
        }
    }
}
</script>

<style scoped>

</style>
