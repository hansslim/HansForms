<template>
    <div>
        <div v-if="!this.loading && !this.errored">
            <h1>{{ this.form.name }}</h1>
            <h3>{{ this.form.description }}</h3>
            <h5>Opened from {{ this.form.start_time }}</h5>
            <h5>Closing at {{ this.form.end_time }}</h5>
            <h4 v-if="this.form.is_expired">Expired!</h4>
            <p>Public link:
                <router-link :to="/form/+getSlug()">{{ publicLink }}</router-link>
            </p>
            <div class="d-flex justify-content-center">
                <FormulateInput
                    class="btn"
                    @click="handleDuplicate"
                    label="Duplicate"
                    type="button"
                />
                <FormulateInput
                    class="btn"
                    @click="handleDelete"
                    label="Delete"
                    type="button"
                />
                <FormulateInput
                    v-if="updateButtonVisibility"
                    class="btn"
                    @click="handleUpdate"
                    label="Change"
                    type="button"
                />
                <FormulateInput
                    class="btn"
                    @click="handleResults"
                    label="Results"
                    type="button"
                />
            </div>
            <br>
            <h2>Interactive preview</h2>
            <p>This is how it is shown to users.</p>
            <div style="border: black solid 2px">
                <FormulateForm v-model="formValues">
                    <form-element v-for="item in this.form.form_elements" :obj="item" :key="item.order"></form-element>
                </FormulateForm>
            </div>
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
    name: "FormPreview",
    components: {
        "form-element": FormElement,
    },
    data() {
        return {
            slug: '',
            form: {},
            formValues: {},
            updateButtonVisibility: false,
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
                    this.handleUpdateButtonVisibility();
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
            await Form.getSpecificFormWithAuth(this.slug)
                .then((response) => {
                    if (response && response.data) {
                        if (response.data.error) throw new Error(response.data.error);
                        else {
                            this.form = response.data;
                            this.dataFetched = true;
                        }
                    } else throw new Error();
                })
                .catch(error => {
                    console.log(error.message)
                    this.errored = true;
                    switch (error.message) {
                        case '401':
                            this.errorText = "Requested form doesn't belong to your account!";
                            break;
                        case '404':
                            this.errorText = "Requested form was not found.";
                            break;
                        /*case '410':
                            this.errorText = "Requested form is expired. You cannot answer this form anymore!";
                            break;*/ //???
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
        handleUpdateButtonVisibility() {
            if (this.form.start_time) {
                const startTime = new Date(this.form.start_time);
                const timeNow = Date.now();
                if (timeNow < startTime) {
                    this.updateButtonVisibility = true;
                }
            }
        },
        handleDelete() {
            if (confirm("Are you sure that you want to delete this form?")) {
                this.loading = true;
                Form.deleteForm(this.getSlug()).then(() => {
                    this.$router.push("/");
                    this.loading = false;
                });
            }
        },
        handleDuplicate() {},
        handleUpdate() {},
        handleResults() {}
    },
    computed: {
        publicLink() {
            return `${window.location}`.replace('/preview', '');
        }
    }
}
</script>

<style scoped>

</style>
