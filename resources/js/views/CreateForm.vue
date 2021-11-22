<template>
    <div>
        <div class="d-flex justify-content-center fixed-bottom bg-info">
            <div class="m-2">
                <FormulateInput
                    type="button"
                    @click="showAddItemModal"
                    label="Add new question..."
                />
            </div>
            <div class="m-2">
                <button @click="submitCreateForm">Create this form</button>
            </div>
        </div>
        <div>
            <form-element  v-for="item in this.items" :key="item.id" :obj="item" @itemChanged="handleItemsChanged"/>
        </div>

    </div>
</template>

<script>
import ItemModal from "../components/Modals/CreateForm/ItemModal";
import FormElementControl from "../components/FormElementControl";
import {createFormChoicesStore, createFormStore} from "../components/Modals/CreateForm/stores";
import Form from "../apis/Form";

export default {
    name: "CreateForm",
    components: {
        'form-element': FormElementControl,
    },
    data() {
        return {
            items: []
        }
    },
    methods: {
        showAddItemModal() {
            createFormChoicesStore.clearStore();
            this.$modal.show(
                ItemModal,
                { purpose: "add"},
                {height: 'auto', width: '60%', adaptive: true, scrollable: true},
                {'before-close': event => this.handleItemsChanged()}
            )
        },
        handleItemsChanged() {
            this.items = createFormStore.getItems()
        },
        submitCreateForm() {
            Form.postCreateForm(this.items)
            /*
            if (this.items.length) {

            }
            else {
                console.log("There are no items in form! Add some, please...")
            }*/
        }
    }
}


</script>

<style scoped>

</style>
