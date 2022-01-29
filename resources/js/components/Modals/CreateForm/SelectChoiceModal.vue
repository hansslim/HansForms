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
                  validation="required|max:63,length"
                  error-behavior="value"
              />
              <FormulateInput
                  v-if="showHiddenLabel"
                  name="hidden_label"
                  type="number"
                  placeholder="Write a hidden label."
                  validation="required"
                  error-behavior="value"
                  v-model="hiddenLabel"
              />
              <FormulateInput v-if="this.$props['purpose'] === 'add'" type="submit" @click="handleAddItem" label="Add"/>
              <FormulateInput v-if="this.$props['purpose'] === 'update'" type="submit" @click="handleUpdateItem" label="Update"/>
              <FormulateInput v-if="this.$props['purpose'] === 'update'" type="submit" @click="handleDeleteItem" label="Delete"/>
          </div>
          <FormulateErrors/>
      </FormulateForm>
  </div>
</template>

<script>
import {v4 as uuidv4} from 'uuid';
import {createFormChoicesStore} from "../../../store";

export default {
    name: "SelectChoiceModal",
    props: ['obj', 'purpose', 'hasHiddenLabel'],
    data() {
        return {
            showHiddenLabel: false,
            choiceText: "",
            hiddenLabel: "",
            order: 0,
            id: "",
            formErrors: []
        }
    },
    methods: {
        handleAddItem() {
            let isValid = true;
            if (this.$props['hasHiddenLabel']) if (this.hiddenLabel === "") isValid = false;
            if (this.choiceText === "") isValid = false;
            if (this.choiceText.length > 63) isValid = false;
            if (isValid) {
                createFormChoicesStore.addItem({
                    id: uuidv4(),
                    text: this.choiceText,
                    hidden_label: this.hiddenLabel
                })
                this.$modal.hide(this.$parent.name)
            }
        },
        handleUpdateItem() {
            let isValid = true;
            if (this.$props['hasHiddenLabel']) if (this.hiddenLabel === "") isValid = false;
            if (this.choiceText === "") isValid = false;
            if (this.choiceText.length > 63) isValid = false;
            if (isValid) {
                    createFormChoicesStore.changeItem({
                    id: this.id,
                    text: this.choiceText,
                    hidden_label: this.hiddenLabel,
                    order: this.order
                })
                this.$modal.hide(this.$parent.name)
            }
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
    },
    mounted() {
        if (this.$props['purpose'] === 'update') {
            console.log(this.$props['obj'])
            if (this.$props['obj']) {
                this.choiceText = this.$props['obj'].text;
                this.hiddenLabel = this.$props['obj'].hidden_label;
                this.order = this.$props['obj'].order;
                this.id = this.$props['obj'].id;
            }
        }
        if (this.$props['hasHiddenLabel']) {
            this.showHiddenLabel = true;
        }
    }
}
</script>

<style scoped>

</style>
