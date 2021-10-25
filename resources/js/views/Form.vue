<template>

</template>

<script>
export default {
    name: "Form",
    mounted() {

    },
    methods: {

    },
    computed: {
        accountText() {
            if (this.$store.getters['user'] !== null) {
                return this.$store.getters['user'].name;
            }
            else return "Account";
        },
        authenticated() {
            return this.$store.getters['authenticated'];
        }
    }
}
</script>

<style scoped>

</style>
