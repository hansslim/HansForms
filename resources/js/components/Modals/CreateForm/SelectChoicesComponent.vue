<template>
    <div>
        <FormulateInput type="button" @click="addChoice" label="Add choice..."/>
        <choice-item v-for="item in choices" :key="item.id" :obj="item" @choiceChanged="handleItemsChanged"/>
    </div>
</template>

<script>
import SelectChoiceItem from "./SelectChoiceItem";
import SelectChoiceModal from "./SelectChoiceModal";
import {createFormChoicesStore} from "../../../store";


export default {
    name: "SelectChoicesComponent",
    props: ["obj", "hasHiddenLabel"],
    components: {
        'choice-item': SelectChoiceItem
    },
    data() {
        return {
            choices: [],
            tryDataLoad: false
        }
    },
    watch: {
        hasHiddenLabel: {
            immediate: false,
            handler() {
                if (this.tryDataLoad) {
                    if (this.$props['hasHiddenLabel']) {
                        //rewrite hidden labels to 0,1,2,...
                        let i = 1;
                        this.choices.forEach((item) => {
                            createFormChoicesStore.changeItem({
                                ...item,
                                hidden_label: `${i++}`
                            })
                        })
                        this.choices = createFormChoicesStore.getItems();
                    } else {
                        //remove hidden labels
                        this.choices.forEach((item) => {
                            createFormChoicesStore.changeItem({
                                ...item,
                                hidden_label: "",
                            })
                        })
                        this.choices = createFormChoicesStore.getItems();
                    }
                }
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
        },
    },
    mounted() {
        if (this.$props['obj']) {
            this.choices = [...this.$props['obj'].choices];
            createFormChoicesStore.setItems([...this.$props['obj'].choices]);
            this.$nextTick(() => {
                this.tryDataLoad = true;
            })
        } else this.tryDataLoad = true;

    }
}


</script>

<style scoped>

</style>
