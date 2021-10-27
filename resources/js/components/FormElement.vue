<template>
    <div>
        {{
            this.element
        }}
    </div>
</template>

<script>


export default {
    name: "FormElement",
    props: ['obj'],
    data() {
      return {
          element: {}
      }
    },
    mounted() {
        this.element = this.$props["obj"];
    }
}
</script>

<style scoped>

</style>
