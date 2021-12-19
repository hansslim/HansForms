<template>
    <div>
        <div v-if="!this.loading && !this.errored" class="container">
            <div class="row">
                <div class="col">
                    <h1>{{ this.form.name }}</h1>
                    <h3>{{ this.form.description }}</h3>
                    <h5>Opened from {{ this.form.start_time }}</h5>
                    <h5>Closing at {{ this.form.end_time }}</h5>

                    <h4 v-if="this.form.is_expired">Expired!</h4>
                    <h4 v-else-if="this.form.is_opened">Opened to fill in.</h4>
                    <h4 v-else>Waiting for publication.</h4>

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
                            v-if="!this.form.was_already_published"
                            class="btn"
                            @click="handleUpdate"
                            label="Change"
                            type="button"
                        />
                        <FormulateInput
                            v-if="this.form.was_already_published"
                            class="btn"
                            @click="handleResults"
                            label="Results"
                            type="button"
                        />
                    </div>
                    <br>
                </div>
                <div class="col">
                    <h2>Interactive preview</h2>
                    <p>This is how it is shown to users.</p>
                    <div style="border: black solid 2px">
                        <FormulateForm v-model="formValues">
                            <form-element v-for="item in this.form.form_elements" :obj="item"
                                          :key="item.order"></form-element>
                        </FormulateForm>
                    </div>
                </div>
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
            loading: true,
            errored: false,
            errorText: "Bad Request",
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
            let errorCode = -1;
            await Form.getSpecificFormWithAuth(this.slug)
                .then((res) => {
                    if (res.status === 200) {
                        this.form = res.data;
                        this.dataFetched = true;
                    } else {
                        console.log(res.status)
                        errorCode = res.status;
                        throw new Error();
                    }
                })
                .catch((error) => {
                    this.errored = true;
                    switch (errorCode) {
                        case 401:
                            this.errorText = "Requested form doesn't belong to your account!";
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
        async handleDelete() {
            if (confirm("Are you sure that you want to delete this form?")) {
                this.loading = true;
                await Form.deleteForm(this.getSlug()).then(() => {
                    this.$router.push("/");
                    this.loading = false;
                });
            }
        },
        async handleDuplicate() {
            if (confirm("Are you sure that you want to duplicate this form?")){
                this.loading = true;
                await Form.postDuplicateForm(this.getSlug()).then((res) => {
                    if (res.status === 200) {
                        this.$router.push("/");
                        this.loading = false;
                    }
                    else throw new Error(res.data.toString());
                }).catch((error) => {
                    alert("Form duplication was not successful.")
                })
            }
        },
        handleUpdate() {
        },
        handleResults() {
        }
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
