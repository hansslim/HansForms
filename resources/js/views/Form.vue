<template>
    <div>
        <div v-if="!this.loading && !this.errored">
            <h1>{{ this.form.name }}</h1>
            <h3>{{ this.form.description }}</h3>
            <h4>Opened to {{this.form.end_time}}</h4>
            <FormulateForm v-model="formValues" @submit="submitForm">
                <form-element v-for="item in this.form.form_elements" :obj="item" :key="item.order"></form-element>
                <FormulateInput
                    type="submit"
                    name="Submit this form!"
                />
            </FormulateForm>
        </div>
        <div v-if="this.loading">
            {{ "loading" }}
        </div>
        <div v-if="this.errored">
            <h1>{{this.errorText}}</h1>
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
            form: {},
            formValues: {},
            loading: true,
            errored: false,
            errorText: "Bad Request (400)",
            dataFetched: false
        }
    },
    async mounted() {
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

    },
    methods: {
        async getThisForm() {
            await Form.getSpecificForm(this.slug)
                .then((response)=>{
                    if (response && response.data) {
                        if (response.data.error) throw new Error(response.data.error);
                        else {
                            this.form = response.data;
                            this.dataFetched = true;
                        }
                    }
                    else throw new Error();
                })
                .catch(error => {
                    console.log(error.message)
                    this.errored = true;
                    switch (error.message) {
                        case '423': this.errorText = "Requested form is not available at this moment. Try it later."; break;
                        case '410': this.errorText = "Requested form is expired. You cannot answer this form anymore!"; break;
                        case '404': this.errorText = "Requested form was not found."; break;
                        default: this.errorText = `Unhandled error - ${error}`; break; //dev only
                    }
                    this.dataFetched = false;
                })
        },
        getSlug() {
            return this.$route.params['slug'] ?? '';
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
            try {
                await Form.postFormCompletion(this.formValues, this.slug).then(()=> {
                    alert("Answer has been proceeded successfully."); //todo: handle errors
                    this.$router.push("/");
                });

            }
            catch (error) {
                console.log(error);
                alert(error);
            }
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
