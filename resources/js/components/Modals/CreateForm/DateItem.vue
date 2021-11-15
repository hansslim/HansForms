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
    data() {
        return {
            min: "",
            max: ""
        }
    }
}
</script>

<style scoped>

</style>
