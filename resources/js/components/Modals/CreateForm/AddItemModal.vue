<template>
    <div class="container">
        <FormulateForm @submit="handleFormSubmit" v-model="formValues">
            <h2>{{modalHeaderText}}</h2>
            <FormulateInput
                v-model="item.type"
                name="type"
                :options="options"
                type="select"
                placeholder="Select an option"
                label="Select type of question"
            />
            <div v-if="item.type">
                <hr>
                <h2>{{ item.type }}</h2>
                <FormulateInput
                    name="header"
                    label="Question"
                    type="text"
                    v-model="item.question"
                />
                <FormulateInput
                    name="is_mandatory"
                    v-model="item.isMandatory"
                    type="checkbox"
                    label="Mandatory to fill"
                    label-position="before"
                />
                <text-item v-if="item.type==='text'" :obj="this.$props['obj']"/>
                <number-item v-if="item.type==='number'" :obj="this.$props['obj']"/>
                <date-item v-if="item.type==='date'"/>
                <select-item v-if="item.type==='select'" :obj="this.$props['obj']"/>
                <hr>
                <FormulateInput
                    type="submit"
                    :name="submitFormButtonText"
                />
                <FormulateInput
                    v-if="this.$props['purpose']==='update'"
                    type="button"
                    name="Delete item"
                    @click="deleteItem"
                />
            </div>
        </FormulateForm>
    </div>
</template>

<script>
import {createFormStore} from "../../../views/CreateForm"
import TextItem from "./TextItem";
import NumberItem from "./NumberItem";
import DateItem from "./DateItem";
import SelectItem from "./SelectItem";
import { v4 as uuidv4 } from 'uuid';

export default {
    name: "AddItemModal",
    components: {
        "text-item": TextItem,
        "number-item": NumberItem,
        "date-item": DateItem,
        "select-item": SelectItem
    },
    props: ['obj', 'purpose'],
    data() {
        return {
            formValues: {},
            options: [
                {value: 'null', label: 'Choose one...', disabled: true, selected: true},
                {value: 'text', label: 'Text'},
                {value: 'number', label: 'Number'},
                {value: 'boolean', label: 'Yes/No'},
                {value: 'date', label: 'Date'},
                {value: 'select', label: 'Select'},
            ],
            item: {
                type: false,
                question: "",
                isMandatory: true,
                id: -1,
                order: 0
            }
        }
    },
    computed: {
      submitFormButtonText() {
          return this.$props['purpose'] === "add" ? "Add" : "Update";
      },
        modalHeaderText() {
            return this.$props['purpose'] === "add" ? "New question..." : "Change question...";
        }
    },
    mounted() {
        if (this.$props['obj']) {
            try {
                if (this.$props['obj'].type) this.item.type = this.$props['obj'].type;
                if (this.$props['obj'].header) this.item.question = this.$props['obj'].header;
                if (this.$props['obj'].is_mandatory) this.item.isMandatory = this.$props['obj'].is_mandatory;
                if (this.$props['obj'].id) this.item.id = this.$props['obj'].id;
                if (this.$props['obj'].order) this.item.order = this.$props['obj'].order;
            }
            catch (e) {
                console.log(e);
            }
        }
    },
    methods: {
        handleFormSubmit() {
            if (this.$props['purpose'] === "add") this.addNewItem();
            else if (this.$props['purpose'] === "update") this.updateItem();

            this.$modal.hide(this.$parent.name)
        },
        addNewItem() {
            createFormStore.addItem({...this.formValues, id: uuidv4()});
        },
        updateItem() {
            createFormStore.changeItem({...this.formValues, id: this.item.id, order: this.item.order})
        },
        deleteItem() {
            if (confirm("Are you sure that you want to delete this item?")) {
                createFormStore.deleteItem({...this.formValues, id: this.item.id, order: this.item.order});
                this.$modal.hide(this.$parent.name)
            }
        }
    }
}
</script>

<style scoped>

</style>
