<template>
    <div>
        <router-link :to="slug">
            <h2>{{name}}</h2>
            <h4>{{description}}</h4><br>
            {{created_at}}<br>
            {{"Show more..."}}
        </router-link>
    </div>
</template>

<script>

export default {
    name: "FormPreview",
    props: ['name', 'description', 'created_at', 'slug'],
}
</script>

<style scoped>

</style>
