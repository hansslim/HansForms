<template>
    <div class="container">
        <FormulateForm
            @submit="handleFormSubmit"
            v-model="formValues"
            name="modalForm"
        >
            <h2>{{ modalHeaderText }}</h2>
            <FormulateInput
                v-model="item.type"
                name="type"
                :options="options"
                type="select"
                placeholder="Select an option"
                label="Select type of question"
                :validation="[['required']]"
                @change="handleTypeChanged"
            />
            <div v-if="item.type">
                <hr>
                <FormulateInput
                    name="header"
                    label="Question"
                    type="text"
                    v-model="item.question"
                    :validation="[['required']]"
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
                <date-item v-if="item.type==='date'" :obj="this.$props['obj']"/>
                <select-item v-if="item.type==='select'" :obj="this.$props['obj']"/>
                <hr>
                <FormulateErrors/>
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
import {v4 as uuidv4} from 'uuid';
import {createFormChoicesStore} from "./SelectChoicesComponent";

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
        try {
            if (this.$props['obj']) {
                if (this.$props['obj'].type) this.item.type = this.$props['obj'].type;
                if (this.$props['obj'].header) this.item.question = this.$props['obj'].header;
                if (this.$props['obj'].is_mandatory) this.item.isMandatory = this.$props['obj'].is_mandatory;
                if (this.$props['obj'].id) this.item.id = this.$props['obj'].id;
                if (this.$props['obj'].order) this.item.order = this.$props['obj'].order;
            }
        } catch (e) {
            console.log(e);
        }
    },
    methods: {
        handleTypeChanged() {
            this.trivialFormulateErrorHandler()
        },
        handleFormSubmit(data) {
            if (this.validateSpecificFormData()) {
                if (this.$props['purpose'] === "add") this.addNewItem(data);
                else if (this.$props['purpose'] === "update") this.updateItem();
                this.$modal.hide(this.$parent.name)
            }
            else console.log("error")
        }
        ,
        addNewItem(data) {
            if (this.item.type==="select") {
                createFormStore.addItem({
                    ...this.formValues,
                    id: uuidv4(),
                    choices: createFormChoicesStore.choices
                })
            }
            else createFormStore.addItem({...this.formValues, id: uuidv4()});
        }
        ,
        updateItem() {
            if (this.item.type==="select") {
                createFormStore.changeItem({
                    ...this.formValues,
                    order: this.item.order,
                    id: this.item.id,
                    choices: createFormChoicesStore.choices
                })
            }
            else createFormStore.changeItem({...this.formValues, id: this.item.id, order: this.item.order})
        }
        ,
        deleteItem() {
            if (confirm("Are you sure that you want to delete this item?")) {
                createFormStore.deleteItem({...this.formValues, id: this.item.id, order: this.item.order});
                this.$modal.hide(this.$parent.name)
            }
        }
        ,
        validateSpecificFormData() {
            switch (this.item.type) {
                case "text": {
                    try {
                        if (this.formValues.min_length !== "" && this.formValues.min_length !== undefined) {
                            if (isNaN(parseInt(this.formValues.min_length))) {
                                this.trivialFormulateErrorHandler("Invalid minimal length value.");
                                return false;
                            }
                        }
                        if (this.formValues.max_length !== "" && this.formValues.max_length !== undefined) {
                            if (isNaN(parseInt(this.formValues.max_length))) {
                                this.trivialFormulateErrorHandler("Invalid maximal length value.");
                                return false;
                            }
                        }
                        if (this.formValues.strict_length !== "" && this.formValues.strict_length !== undefined) {
                            if (isNaN(parseInt(this.formValues.strict_length))) {
                                this.trivialFormulateErrorHandler("Invalid strict length value.");
                                return false;
                            }
                        }
                        if (this.formValues.min_length && this.formValues.max_length) {
                            if (parseInt(this.formValues.max_length) <= (parseInt(this.formValues.min_length))) {
                                this.trivialFormulateErrorHandler('Minimal length value is higher than maximal length value.');
                                return false;
                            }
                        }
                        this.trivialFormulateErrorHandler();
                        return true;
                    } catch (e) {
                        this.trivialFormulateErrorHandler(e);
                        return false;
                    }

                }
                case "number": {
                    try {
                        if (this.formValues.min !== "" && this.formValues.min !== undefined) {
                            if (isNaN(parseInt(this.formValues.min))) {
                                this.trivialFormulateErrorHandler("Invalid minimal value.");
                                return false;
                            }
                        }
                        if (this.formValues.max !== "" && this.formValues.max !== undefined) {
                            if (isNaN(parseInt(this.formValues.max))) {
                                this.trivialFormulateErrorHandler("Invalid maximal value.");
                                return false;
                            }
                        }
                        if (this.formValues.min && this.formValues.max) {
                            if (parseInt(this.formValues.max) <= (parseInt(this.formValues.min))) {
                                this.trivialFormulateErrorHandler('Minimal value is higher than maximal value.');
                                return false;
                            }
                        }
                        this.trivialFormulateErrorHandler();
                        return true;
                    } catch (e) {
                        this.trivialFormulateErrorHandler(e);
                        return false;
                    }
                }
                case "date": {
                    try {
                        if (this.formValues.min !== "") {
                            const min = new Date(this.formValues.min).getTime();
                            const max = new Date(this.formValues.max).getTime();

                            if (min && max) {
                                if (parseInt(max) <= (parseInt(min))) {
                                    this.trivialFormulateErrorHandler('Minimal date value is higher than maximal date value.');
                                    return false;
                                }
                            }
                        }

                        this.trivialFormulateErrorHandler();
                        return true;
                    } catch (e) {
                        this.trivialFormulateErrorHandler(e);
                        return false;
                    }
                }
                case "boolean": {
                    return true;
                }
                case "select": {
                    try {
                        /*if (this.formValues.choices) {
                            let choicesArray = this.formValues.choices.split("\n");

                            this.formValues.choices = choicesArray;
                        }
                        else {
                            this.trivialFormulateErrorHandler("You have to add at least 2 choices.");
                            return false;
                        }*/
                        if (this.formValues.min_amount_of_answers !== "" && this.formValues.min_amount_of_answers !== undefined) {
                            if (isNaN(parseInt(this.formValues.min_amount_of_answers))) {
                                this.trivialFormulateErrorHandler("Invalid minimal amount of choices value.");
                                return false;
                            }
                        }
                        if (this.formValues.max_amount_of_answers !== "" && this.formValues.max_amount_of_answers !== undefined) {
                            if (isNaN(parseInt(this.formValues.max_amount_of_answers))) {
                                this.trivialFormulateErrorHandler("Invalid maximal amount of choices value.");
                                return false;
                            }
                        }
                        if (this.formValues.strict_amount_of_answers !== "" && this.formValues.strict_amount_of_answers !== undefined) {
                            if (isNaN(parseInt(this.formValues.strict_amount_of_answers))) {
                                this.trivialFormulateErrorHandler("Invalid strict amount of choices value.");
                                return false;
                            }
                        }
                        if (this.formValues.min_amount_of_answers && this.formValues.max_amount_of_answers) {
                            if (parseInt(this.formValues.max_amount_of_answers) <= (parseInt(this.formValues.min_amount_of_answers))) {
                                this.trivialFormulateErrorHandler('Minimal amount of choices value is higher than maximal amount of choices value.');
                                return false;
                            }
                        }
                        this.trivialFormulateErrorHandler();
                        return true;
                    } catch (e) {
                        this.trivialFormulateErrorHandler(e);
                        return false;
                    }
                }
                default: {
                    this.trivialFormulateErrorHandler("Unhandled input error.");
                    return false;
                }
            }
        }
        ,
        trivialFormulateErrorHandler(error = null) {
            if (error) {
                this.$formulate.handle({
                    formErrors: [error.toString()]
                }, 'modalForm');
            } else {
                this.$formulate.handle({
                    formErrors: []
                }, 'modalForm');
            }
        }
    }
}
</script>

<style scoped>

</style>
