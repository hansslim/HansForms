<template>
    <div class="container">
        <h2>Publication</h2>
        <h4>Current status: {{this.$props['publicResults'] === true ? "public" : "private"}}</h4>
        <div v-if="this.$props['publicResults']">
            Public link: <a :href="this.publicLink">{{this.publicLink}}</a>
            <hr>
        </div>
        <FormulateForm v-model="formValues" @submit="handleSubmit">
            <FormulateInput
                class="form-check form-switch"
                label="Set results public"
                name="has_public_results"
                type="checkbox"
                :checked="this.$props['publicResults']"
                element-class="form-check-input"
                label-class="form-check-label"
            />
            <hr>
            <h5>Mark questions that you want to show to the public...</h5>
            <div v-for="item in this.$props['questions']" :key="item.id">
                <FormulateInput
                    wrapper-class="form-check"
                    :label="item.header"
                    :name="item.id"
                    type="checkbox"
                    :checked="item.public"
                    element-class="form-check-input"
                    label-class="form-check-label"
                /><br>
            </div>

            <FormulateInput
                label="Change restrictions"
                type="submit"
            />
        </FormulateForm>

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
            publicLink: ""
        }
    },
    mounted() {
        this.publicLink = `${window.location}`.replace('/results/', '/public_results/');
    },
    methods: {
        async handleSubmit() {
            //todo: select at least 1
            console.log(this.formValues);
            await Form.postPublishFormResults(this.formValues, this.$props['slug']).then((res)=>{
                if (res.status === 200) {
                    this.$modal.hide(this.$parent.name)
                    window.location.reload()
                }
            });
        }
    }
}
</script>

<style scoped>

</style>
