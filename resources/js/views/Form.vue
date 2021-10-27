<template>
    <div>
        <div v-if="!this.loading">
            <div>{{this.form.name}}</div>
            <div>{{this.form.description}}</div>
            <form-element v-for="item in this.form.form_elements" :obj="item"></form-element>
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
    components: {"form-element": FormElement},
    data() {
        return {
            slug: '',
            form: {},
            loading: true
        }
    },
    async mounted() {
        this.slug = this.getSlug();
        await this.getThisForm().then(()=> {
            this.loading = false;
        });

    },
    methods: {
        async getThisForm() {
            const specificForm = await Form.getSpecificForm(this.slug);
            this.form = specificForm.data;
        },
        getSlug() {
            return this.$route.params['slug'] ?? '';
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
