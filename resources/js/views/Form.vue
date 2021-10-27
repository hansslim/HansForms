<template>
    <div>
        {{ this.form }}
    </div>
</template>

<script>
import Form from "../apis/Form";

export default {
    name: "Form",
    data() {
        return {
            slug: '',
            form: {}
        }
    },
    mounted() {
        this.slug = this.getSlug();
        this.getThisForm();
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
