<template>
    <div>
        <FormulateInput type="button" @click="addChoice" label="Add choice..."/>
        <choice-item v-for="item in choices" :key="item.id" :obj="item" @choiceChanged="handleItemsChanged"/>
    </div>
</template>

<script>
import SelectChoiceItem from "./SelectChoiceItem";
import SelectChoiceModal from "./SelectChoiceModal";
import {createFormChoicesStore} from "./stores";


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
        addChoice() {
            this.$modal.show(
                SelectChoiceModal,
                { purpose: "add"},
                {height: 'auto', width: '60%', adaptive: true},
                {'before-close': event => this.handleItemsChanged()}
            )
        },
        handleItemsChanged(){
            this.choices = createFormChoicesStore.getItems();
        }
    },
    mounted() {
        if (this.$props['obj']) {
            this.choices = this.$props['obj'].choices;
            createFormChoicesStore.setItems(this.$props['obj'].choices);
        }
    }
}


</script>

<style scoped>

</style>
