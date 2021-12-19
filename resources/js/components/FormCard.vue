<template>
    <div style="border: solid black 1px; border-radius: 5px ;padding: 10px; margin: 5px">
        <router-link :to="formLink">
            <h2>{{this.$props['obj'].name}}</h2>
            <h4>{{this.$props['obj'].description}}</h4>
            Visible from {{this.$props['obj'].start_time}} to {{this.$props['obj'].end_time}}<br>
            {{"Show more..."}}
        </router-link>
    </div>
</template>

<script>
export default {
    name: "FormPreview",
    props: ['obj'],
    computed: {
        formLink() {
            return "/form/preview/" + this.$props['obj'].slug;
        }
    }
}
</script>

<style scoped>

</style>
