<template>
    <div>
        <FormulateInput type="button" @click="addChoice" label="Add choice..."/>
        <choice-item v-for="item in choices" :key="item.id" :obj="item" @choiceChanged="handleItemsChanged"/>
    </div>
</template>

<script>
import SelectChoiceItem from "./SelectChoiceItem";
import SelectChoiceModal from "./SelectChoiceModal";


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
            createFormChoicesStore.data.choices = this.$props['obj'].choices
        }
    }
}

export let createFormChoicesStore = {
    data: {
        choices: [],
    },
    getItems() {
        return this.data.choices;
    },
    addItem(item) {
        this.data.choices = [...this.data.choices, item];
        this.refreshItemsOrder();
    },
    refreshItemsOrder() {
        let i = 0;
        this.data.choices = this.data.choices.map((x)=>{
            let obj = x;
            obj.order = i++;
            return obj;
        });
    },
    sortItemsByOrder() {
        this.data.choices = this.data.choices.sort((a, b) => {
            if (a.order < b.order) {
                return -1;
            }
            if (a.order > b.order) {
                return 1;
            }
            return 0;
        });
    },
    changeItem(item) {
        this.data.choices = this.data.choices.filter((x)=>x.id!==item.id);
        this.data.choices = [...this.data.choices, item];
        this.sortItemsByOrder();
    },
    deleteItem(item) {
        this.data.choices = this.data.choices.filter((x)=>x.id!==item.id);
        this.refreshItemsOrder();
    },
    clearStore() {
        this.data.choices = []
    }
}
</script>

<style scoped>

</style>
