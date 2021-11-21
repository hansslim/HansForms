<template>
  <div class="container">
      <h1>Choice</h1>
      <FormulateForm name="selectChoiceModalForm">
          <div class="d-flex">
              <FormulateInput
                  name="text"
                  type="text"
                  placeholder="Write a choice."
                  v-model="choiceText"
              />
              <FormulateInput
                  name="hidden_label"
                  type="number"
                  placeholder="Write a hidden label."
                  v-model="hiddenLabel"
              />
              <FormulateInput v-if="this.$props['purpose'] === 'add'" type="button" @click="handleAddItem" label="Add"/>
              <FormulateInput v-if="this.$props['purpose'] === 'update'" type="button" @click="handleUpdateItem" label="Update"/>
              <FormulateInput v-if="this.$props['purpose'] === 'update'" type="button" @click="handleDeleteItem" label="Delete"/>
          </div>
          <FormulateErrors/>
      </FormulateForm>
  </div>
</template>

<script>
import {createFormChoicesStore} from "./SelectChoicesComponent";
import {v4 as uuidv4} from 'uuid';

export default {
    name: "SelectChoiceModal",
    props: ["obj", 'purpose'],
    data() {
        return {
            choiceText: "",
            hiddenLabel: "",
            order: 0,
            id: "",
            formErrors: []
        }
    },
    methods: {
        handleAddItem() {
            console.log(this.formErrors)
            if (this.choiceText) {
                createFormChoicesStore.addItem({
                    id: uuidv4(),
                    text: this.choiceText,
                    hidden_label: this.hiddenLabel
                })
                this.$modal.hide(this.$parent.name)
            }
            else this.trivialFormulateErrorHandler("Choice is required.")
        },
        handleUpdateItem() {
            if (this.choiceText) {
                createFormChoicesStore.changeItem({
                    id: this.id,
                    text: this.choiceText,
                    hidden_label: this.hiddenLabel,
                    order: this.order
                })
                this.$modal.hide(this.$parent.name)
            }
            else this.trivialFormulateErrorHandler("Choice is required.")
        },
        handleDeleteItem() {
            createFormChoicesStore.deleteItem({
                id: this.id,
                text: this.choiceText,
                hidden_label: this.hiddenLabel,
                order: this.order
            })
            this.$modal.hide(this.$parent.name)
        },
        trivialFormulateErrorHandler(error = null) {
            if (error) {
                this.$formulate.handle({
                    formErrors: [error.toString()]
                }, 'selectChoiceModalForm');
            } else {
                this.$formulate.handle({
                    formErrors: []
                }, 'selectChoiceModalForm');
            }
        }
    },
    mounted() {
        if (this.$props['purpose'] === 'update') {
            if (this.$props['obj']) {
                this.choiceText = this.$props['obj'].text;
                this.hiddenLabel = this.$props['obj'].hidden_label;
                this.order = this.$props['obj'].order;
                this.id = this.$props['obj'].id;
            }
        }
    }
}
</script>

<style scoped>

</style>
