<template>
    <div class="col-sm m-1 shadow-sm card d-flex justify-content-center">
        <router-link :to="formLink" class="text-decoration-none">
            <div class="p-2">
                <h1>{{this.$props['obj'].name}}</h1>
                <h4>{{this.$props['obj'].description}}</h4>
                Visible from {{this.$props['obj'].start_time}} to {{this.$props['obj'].end_time}}<br>
                {{"Show more..."}}
            </div>
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
