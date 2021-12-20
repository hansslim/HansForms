<template>
    <div>
        <div v-if="elementType === 'boolean'">
            <boolean-result :obj="this.element"></boolean-result>
        </div>
        <div v-else-if="elementType === 'date'">
            <date-result :obj="this.element"></date-result>
        </div>
        <div v-else-if="elementType === 'number'">
            <number-result :obj="this.element"></number-result>
        </div>
        <div v-else-if="elementType === 'text'">
            <text-result :obj="this.element"></text-result>
        </div>
        <div v-else-if="elementType === 'select'">
            <select-result :obj="this.element"></select-result>
        </div>
    </div>
</template>

<script>
import BooleanResult from "./BooleanResult";
import DateResult from "./DateResult";
import NumberResult from "./NumberResult";
import TextResult from "./TextResult";
import SelectResult from "./SelectResult";

export default {
    name: "ResultComponent",
    components: {
        'select-result': SelectResult,
        'text-result': TextResult,
        'number-result': NumberResult,
        'date-result': DateResult,
        'boolean-result': BooleanResult
    },
    props: ["formElement"],
    data() {
        return {
            elementType: null,
            element: null
        }
    },
    mounted() {
        const element = this.$props['obj'];
        if (element.new_page === null) {
            if (element.input_element !== null) {
                if (element.input_element.boolean_input !== null) {
                    this.elementType = "boolean";
                    //this.element = element.input_element.boolean_input;
                } else if (element.input_element.date_input !== null) {
                    this.elementType = "date";
                    //this.element = element.input_element.date_input;
                } else if (element.input_element.number_input !== null) {
                    this.elementType = "number";
                    //this.element = element.input_element.number_input;
                } else if (element.input_element.text_input !== null) {
                    this.elementType = "text";
                    //this.element = element.input_element.text_input;
                } else if (element.input_element.select_input !== null) {
                    this.elementType = "select";
                    //this.element = element.input_element.select_input;
                } else throw new Error("Invalid type of input element");
                this.element = element.input_element.header;
            }
        } else return "new_page";
    }
}
</script>

<style scoped>

</style>
