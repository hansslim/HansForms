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
import {createFormChoicesStore, createFormStore} from "../components/Modals/CreateForm/stores";

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
            createFormChoicesStore.clearStore();
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


</script>

<style scoped>

</style>
