<template>
    <div style="border: solid black 1px; border-radius: 20px ;padding: 10px">
        <router-link :to="formLink">
            <h2>{{this.$props['obj'].name}}</h2>
            <h4>{{this.$props['obj'].description}}</h4>
            Visible from {{this.$props['obj'].start_time}} to {{this.$props['obj'].end_time}}<br>
            {{"Show more..."}}
        </router-link>
        <FormulateInput
            @click="handleDelete"
            label="Delete"
            type="button"
        />
    </div>
</template>

<script>

import Form from "../apis/Form";

export default {
    name: "FormPreview",
    props: ['obj'],
    computed: {
        formLink() {
            return "/form/preview/" + this.$props['obj'].slug;
        }
    },
    methods: {
        handleDelete() {
            this.$emit('itemsChanged', 'loadingOn')
            if (confirm("Are you sure that you want to delete this form?")) {
                Form.deleteForm(this.$props['obj'].slug);
                this.$emit('itemsChanged', 'itemsUpdated')
            }
            else this.$emit('itemsChanged', 'loadingOff')
        },
        handleUpdate() {

        }
    },
    mounted() {
        console.log(this.$props['obj'])
    }
}
</script>

<style scoped>

</style>
