<template>
    <div class="container">
        <h2>Publication</h2>
        <div v-if="!loading">
            <h4>Current status: {{this.$props['publicResults'] === true ? "public" : "private"}}</h4>
            <div v-if="this.$props['publicResults'] && this.formValues.has_public_results">
                Public link: <a :href="this.publicLink">{{this.publicLink}}</a>
                <hr>
            </div>
            <FormulateForm v-model="formValues" @submit="handleSubmit" name="modalForm" @input="handleFormInputChange">
                <FormulateInput
                    class="form-check form-switch"
                    label="Set results public"
                    name="has_public_results"
                    type="checkbox"
                    :checked="this.$props['publicResults']"
                    :value="this.$props['publicResults']"
                    element-class="form-check-input"
                    label-class="form-check-label"
                />
                <hr>
                <div v-if="!this.disabledCheckboxes">
                    <h5>Mark questions that you want to show to the public...</h5>
                    <div v-for="item in this.$props['questions']" :key="item.id">
                        <FormulateInput
                            :label="item.header"
                            :name="item.id"
                            type="checkbox"
                            :checked="item.public"
                            :value="item.public"
                            wrapper-class="form-check"
                            element-class="form-check-input"
                            label-class="form-check-label"
                        />
                        <br>
                    </div>
                </div>

                <FormulateErrors/>
                <FormulateInput
                    label="Change restrictions"
                    type="submit"
                />
            </FormulateForm>
        </div>
        <div v-else>
            <loading/>
        </div>

    </div>
</template>

<script>
import Form from "../../../apis/Form";

export default {
    name: "FormResultsPublicationModal",
    props: ['questions', 'publicResults', 'slug'],
    data() {
        return {
            formValues: {},
            publicLink: "",
            errored: false,
            disabledCheckboxes: true,
            loading: true
        }
    },
    mounted() {
        this.publicLink = `${window.location}`.replace('/results/', '/public_results/');
        this.disabledCheckboxes = !this.$props['publicResults'];
        this.loading = false;
    },
    methods: {
        async handleSubmit() {
            this.handleFormInputChange(false);
            if (!this.errored) {
                this.loading = true;
                await Form.postPublishFormResults(this.formValues, this.$props['slug']).then((res) => {
                    if (res.status === 200) {
                        this.$toasted.success('Form results publication changed successfully.')
                        this.loading = false;
                        this.$modal.hide(this.$parent.name)
                        window.location.reload()
                    }
                });
            }

        },
        handleFormInputChange(show) {
            this.trivialFormulateErrorHandler();
            if (this.formValues.has_public_results) {
                this.disabledCheckboxes = false;
                let trueCount = 0;
                Object.values(this.formValues).forEach((x) => {
                    if (x) trueCount++;
                });
                if (trueCount < 2) {
                    if (!show) this.trivialFormulateErrorHandler("You have to choose at least one question to have public results.");
                    this.errored = true;
                } else {
                    if (!show) this.trivialFormulateErrorHandler();
                    this.errored = false;
                }
            } else {
                this.disabledCheckboxes = true;
                this.errored = false;
            }
        },
        trivialFormulateErrorHandler(error = null) {
            if (error) {
                this.$formulate.handle({
                    formErrors: [error.toString()]
                }, 'modalForm');
            } else {
                this.$formulate.handle({
                    formErrors: []
                }, 'modalForm');
            }
        }
    }
}
</script>

<style scoped>

</style>
