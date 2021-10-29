<template>
    <div>
        <div v-if="!this.loading">
            <h1>{{this.form.name}}</h1>
            <h3>{{this.form.description}}</h3>
            <FormulateForm v-model="formValues" @submit="submitForm">
                <form-element v-for="item in this.form.form_elements" :obj="item" :key="item.order"></form-element>
                <FormulateInput
                    type="submit"
                    name="Submit this form!"
                />
            </FormulateForm>
        </div>
        <div v-else>
            {{"loading"}}
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
            loading: true
        }
    },
    async mounted() {
        this.slug = this.getSlug();
        await this.getThisForm().then(()=> {
            this.sortElements();
            this.loading = false;
            console.log(this.form);
        });

    },
    methods: {
        async getThisForm() {
            const specificForm = await Form.getSpecificForm(this.slug);
            this.form = specificForm.data;
        },
        getSlug() {
            return this.$route.params['slug'] ?? '';
        },
        sortElements() {
            this.form.form_elements.sort(( a, b ) => {
                if ( a.order < b.order ){
                    return -1;
                }
                if ( a.order > b.order ){
                    return 1;
                }
                return 0;
            });
        },
        submitForm() {
            console.log(this.formValues)
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
