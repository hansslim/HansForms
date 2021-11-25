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
    props: ["obj", "hasHiddenLabel"],
    components: {
        'choice-item': SelectChoiceItem
    },
    data() {
        return {
            choices: [],
        }
    },
    watch: {
        hasHiddenLabel: function () {
            if (this.$props['hasHiddenLabel']) {
                //rewrite hidden labels to 0,1,2,...
                let i = 1;
                this.choices = this.choices.map((x) => {
                    let object = x;
                    if (!object.hidden_label) object.hidden_label = i++;
                    return object;
                    /*if (!x.hidden_label) return x.hidden_label = i++;*/
                })
                //this.choices = createFormChoicesStore.getItems();

            } else {
                //remove hidden labels
                this.choices = this.choices.map((x) => {
                    let object = x;
                    x.hidden_label = "";
                    return object;
                })
                //this.choices = createFormChoicesStore.getItems();

            }
        }
    },
    methods: {
        addChoice() {
            this.$modal.show(
                SelectChoiceModal,
                {purpose: "add", hasHiddenLabel: this.$props['hasHiddenLabel']},
                {height: 'auto', width: '60%', adaptive: true},
                {'before-close': event => this.handleItemsChanged()}
            )
        },
        handleItemsChanged() {
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
