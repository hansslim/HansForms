<template>
    <div>
        <FormulateInput type="button" @click="addBlankChoice"  label="Add choice..."/>
        <FormulateInput type="group" name="choices">
            <choice-item v-for="item in choices" :key="item.id" :obj="item" @delete="handleDeleteChoice" @itemChange="handleItemChange"/>
        </FormulateInput>
    </div>
</template>

<script>
import SelectChoiceItem from "./SelectChoiceItem";
import {v4 as uuidv4} from 'uuid';
import AddItemModal from "./AddItemModal";

export default {
    name: "SelectChoicesComponent",
    props: ["obj"],
    components: {
        'choice-item': SelectChoiceItem
    },
    data() {
        return {
            choices: [],
        }
    },
    methods: {
        addBlankChoice() {
            this.choices = [...this.choices, {id: uuidv4(), text: "", hidden_label: ""}];
            this.updateChoicesStore()
        },
        handleDeleteChoice(value) {
            this.choices = [...this.choices.filter((x) => x.id !== value.id)];
            this.updateChoicesStore()
        },
        updateChoicesStore() {
            createFormChoicesStore.choices = [...this.choices]
        },
        handleItemChange(value) {
            const choice = this.choices.findIndex((obj => obj.id === value.id))
            this.choices[choice].text = value.text;
            this.choices[choice].hidden_label = value.hidden_label;
        }
    },
    mounted() {
        if (this.$props['obj']) {
            this.choices = this.$props['obj'].choices
        }
    }
}

export let createFormChoicesStore = {
    choices: []
}
</script>

<style scoped>

</style>
