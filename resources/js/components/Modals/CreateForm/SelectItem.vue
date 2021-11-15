<template>
  <div>
      <FormulateInput
          name="is_multiselect"
          type="checkbox"
          label="Multiselect"
          label-position="before"
          v-model="is_multiselect"
      />
      <div v-if="is_multiselect">
          <FormulateInput
              v-if="range"
              @input="showWantedInputs"
              label="Minimal amount of answers"
              name="min_amount_of_answers"
              type="number"
              v-model="min_amount_of_answers"
          />
          <FormulateInput
              v-if="range"
              @input="showWantedInputs"
              label="Maximal amount of answers"
              name="max_amount_of_answers"
              type="number"
              v-model="max_amount_of_answers"
          />
          <FormulateInput
              v-if="strict"
              @input="showWantedInputs"
              label="Strict amount of answers"
              name="strict_amount_of_answers"
              type="number"
              v-model="strict_amount_of_answers"
          />
      </div>

      <FormulateInput
        type="textarea"
        label="Add choices (they are separated by new line)"
        name="choices"
      />
  </div>
</template>

<script>
export default {
    name: "SelectItem",
    data() {
        return {
            is_multiselect: false,
            min_amount_of_answers: "",
            max_amount_of_answers: "",
            strict_amount_of_answers: "",
            strict: true,
            range: true
        }
    },
    methods: {
        showWantedInputs() {
            if(this.max_amount_of_answers||this.min_amount_of_answers) {
                this.strict = false;
                this.range = true;
            }
            else this.strict = true;
            if (this.strict_amount_of_answers) {
                this.range = false;
                this.strict = true;
            }
            else this.range = true;
        }
    }
}
</script>

<style scoped>

</style>
