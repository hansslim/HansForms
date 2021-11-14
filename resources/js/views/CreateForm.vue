<template>
    <div>
        <div>
            <button @click="showAddItemModal">Add</button>
        </div>
        <div>
            <form-element  v-for="item in this.items" :key="item.id" :obj="item" @itemChanged="handleItemsChanged"/>
        </div>
    </div>
</template>

<script>
import AddItemModal from "../components/Modals/CreateForm/AddItemModal";
import FormElementControl from "../components/FormElementControl";

export default {
    name: "CreateForm",
    components: {
        'form-element': FormElementControl,
    },
    data() {
        return {
            items: createFormStore.getItems()
        }
    },
    methods: {
        showAddItemModal() {
            this.$modal.show(
                AddItemModal,
                { purpose: "add"},
                {height: 'auto', width: '60%', adaptive: true},
                {'before-close': event => this.handleItemsChanged()}
            )
        },
        handleItemsChanged() {
            this.items = createFormStore.getItems()
        },

    }
}

export let createFormStore = {
    data: {
        items: [],
    },
    getItems() {
        return this.data.items;
    },
    addItem(item) {
        this.data.items = [...this.data.items, item];
        this.refreshItemsOrder();
    },
    refreshItemsOrder() {
        let i = 0;
        this.data.items = this.data.items.map((x)=>{
            let obj = x;
            obj.order = i++;
            return obj;
        });
    },
    sortItemsByOrder() {
        this.data.items = this.data.items.sort((a, b) => {
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
        this.data.items = this.data.items.filter((x)=>x.id!==item.id);
        this.data.items = [...this.data.items, item];
        this.sortItemsByOrder();
    },
    deleteItem(item) {
        this.data.items = this.data.items.filter((x)=>x.id!==item.id);
        this.refreshItemsOrder();
    }
}
</script>

<style scoped>

</style>
