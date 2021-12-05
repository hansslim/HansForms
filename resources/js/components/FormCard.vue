<template>
    <div>
        <router-link :to="formLink">
            <h2>{{this.$props['obj'].name}}</h2>
            <h4>{{this.$props['obj'].description}}</h4>
            {{"Show more..."}}
        </router-link>
        <FormulateInput
            @click="handleDelete"
            label="Delete"
            type="button"
        />
        <hr>
    </div>
</template>

<script>

import Form from "../apis/Form";

export default {
    name: "FormPreview",
    props: ['obj'],
    computed: {
        formLink() {
            return "/form/" + this.$props['obj'].slug;
        }
    },
    methods: {
        handleDelete() {
            this.$emit('itemsChanged', 'loadingOn')
            if (confirm("Are you sure that you want to delete this form?")) {
                Form.deleteForm(this.$props['obj'].slug).then(()=>{});
                this.$emit('itemsChanged', 'itemsUpdated')
            }
            else this.$emit('itemsChanged', 'loadingOff')
        },
        handleUpdate() {

        }
    }
}
</script>

<style scoped>

</style>
