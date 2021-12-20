<template>
  <div>
    {{results}}
  </div>
</template>

<script>
import Form from "../apis/Form";

export default {
    name: "FormResults",
    data() {
      return {
          results: []
      }
    },
    async mounted() {
        await Form.getFormResults(this.getSlug()).then((res)=>{
            if (res.status === 200) {
                this.results = res.data;
            }
        })
    },
    methods: {
        getSlug() {
            return this.$route.params['slug'] ?? '';
        },
    }
}
</script>

<style scoped>

</style>
