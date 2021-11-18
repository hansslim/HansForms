<template>
  <div>
      <FormulateInput
          name="min"
          label="Minimal date"
          type="date"
          v-model="min"
      />
      <FormulateInput
          name="max"
          label="Maximal date"
          type="date"
          v-model="max"
      />
  </div>
</template>

<script>
export default {
    name: "DateItem",
    props: ['obj'],
    data() {
        return {
            min: "",
            max: ""
        }
    },
    mounted() {
        if (this.$props['obj']) {
            if (this.$props['obj'].min) {
                const minDate = new Date(this.$props['obj'].min);
                if (minDate instanceof Date && !isNaN(minDate.valueOf())) {
                    this.min = this.$props['obj'].min;
                }
            }
            if (this.$props['obj'].max) {
                const maxDate = new Date(this.$props['obj'].max);
                if (maxDate instanceof Date && !isNaN(maxDate.valueOf())) {
                    this.max = this.$props['obj'].max;
                }
            }
        }
        else console.log(this.$props['obj'])
    }
}
</script>

<style scoped>

</style>
