<template>
  <div>
      <FormulateInput
          name="is_multiselect"
          type="checkbox"
          label="Multiselect"
          label-position="before"
      />
      <FormulateInput
          label="Minimal amount of answers"
          name="min_amount_of_answers"
          type="number"
      />
      <FormulateInput
          label="Maximal amount of answers"
          name="max_amount_of_answers"
          type="number"
      />
      <FormulateInput
          label="Strict amount of answers"
          name="strict_amount_of_answers"
          type="number"
      />
      <FormulateInput
        type="textarea"
        label="Add choices (they are separated by new line)"
        name="choices"
      />
  </div>
</template>

<script>
export default {
    name: "SelectItem"
}
</script>

<style scoped>

</style>
