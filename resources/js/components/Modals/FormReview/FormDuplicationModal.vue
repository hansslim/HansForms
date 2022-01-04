<template>
    <div class="container">
        <FormulateForm
            @submit="handleFormSubmit"
            name="duplModalForm"
        >
            <h2>Duplicate form...</h2>
            <FormulateInput
                type="text"
                v-model="name"
                label="Form header"
                :validation="[['required']]"
            />
            <FormulateInput
                v-model="description"
                label="Form description"
                type="textarea"
            />
            <hr>
            <FormulateInput
                v-model="start_time"
                label="Form publication start time"
                type="datetime-local"
                :validation="[['required']]"
            />
            <FormulateInput
                v-model="end_time"
                label="Form publication end time"
                type="datetime-local"
                :validation="[['required']]"
            />
            <FormulateInput
                v-model="has_private_token"
                label="Form with private access "
                type="checkbox"
            />
            <FormulateInput
                v-if="has_private_token"
                label="Private emails (Web validation limited! Please, separate emails by enter. Invalid data will be ignored.)"
                type="textarea"
                name="emails"
                validation="required"
                v-model="private_emails"
            />
            <FormulateErrors/>
            <FormulateInput
                type="submit"
                name="Duplicate"
            />
        </FormulateForm>
    </div>
</template>

<script>
import {duplicateFormStore} from "../../../store";

export default {
    name: "FormDuplicationModal",
    props: ['obj'],
    data() {
        return {
            name: "",
            description: "",
            start_time: "",
            end_time: "",
            private_emails: "",
            has_private_token: false
        }
    },
    methods: {
        handleFormSubmit() {
            duplicateFormStore.setData({
                name: this.name,
                description: this.description,
                start_time: this.start_time,
                end_time: this.end_time,
                private_emails: this.private_emails,
                has_private_token: this.has_private_token,
            })

            let error = duplicateFormStore.validateData();
            if (error) {
                this.trivialFormulateErrorHandler(error)
            }
            else this.$modal.hide(this.$parent.name)
        },
        trivialFormulateErrorHandler(error = null) {
            if (error) {
                this.$formulate.handle({
                    formErrors: [error.toString()]
                }, 'duplModalForm');
            } else {
                this.$formulate.handle({
                    formErrors: []
                }, 'duplModalForm');
            }
        }
    },
    mounted() {
        console.log(this.$props['obj'])
        if (this.$props['obj']) {
            this.name = this.$props['obj'].name;
            this.description = this.$props['obj'].description;
            this.start_time = this.$props['obj'].start_time.replace(" ", "T");
            this.end_time = this.$props['obj'].end_time.replace(" ", "T");
            this.has_private_token = this.$props['obj'].has_private_token;
            this.private_emails = this.$props['obj'].private_emails.map((x)=>x.email).join("\n");
        }
        else console.log("error - empty obj")
    }
}
</script>

<style scoped>

</style>
