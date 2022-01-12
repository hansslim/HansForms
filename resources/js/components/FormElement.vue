<template>
    <div class="m-2">
        <hr>
        <div v-if="inputType==='text'">
            <text-input
                :isMandatory="this.$props['obj'].input_element.is_mandatory"
                :label="label"
                :obj="this.$props['obj'].input_element.text_input"
            />
        </div>
        <div v-if="inputType==='number'">
            <number-input
                :label="label"
                :obj="this.$props['obj'].input_element.number_input"
                :isMandatory="isMandatory"
            />
        </div>
        <div v-if="inputType==='date'">
            <date-input
                :label="label"
                :obj="this.$props['obj'].input_element.date_input"
                :isMandatory="isMandatory"
            />
        </div>
        <div v-if="inputType==='boolean'">
            <boolean-input
                :label="label"
                :obj="this.$props['obj'].input_element.boolean_input"
                :isMandatory="isMandatory"
            />
        </div>
        <div v-if="inputType==='select'">
            <select-input
                :label="label"
                :obj="this.$props['obj'].input_element.select_input"
                :isMandatory="isMandatory"
            />
        </div>
    </div>

</template>

<script>
import TextInput from "./Elements/TextInput";
import SelectInput from "./Elements/SelectInput";
import NumberInput from "./Elements/NumberInput";
import BooleanInput from "./Elements/BooleanInput";
import DateInput from "./Elements/DateInput";

export default {
    name: "FormElement",
    props: ['obj'],
    components: {
        'text-input': TextInput,
        'select-input': SelectInput,
        'number-input': NumberInput,
        'boolean-input': BooleanInput,
        'date-input': DateInput
    },
    data() {
        return {
            inputType: '',
        }
    },
    mounted() {
        this.inputType = this.getType(this.$props["obj"]);
    },
    computed: {
        label() {
            return this.$props["obj"].input_element.header;
        },
        isMandatory() {
            return this.$props["obj"].input_element.is_mandatory;
        }
    },
    methods: {
        getType(element) {
            //console.log(element);
            if (element.new_page === null) {
                if (element.input_element !== null) {
                    if (element.input_element.boolean_input !== null) return "boolean";
                    else if (element.input_element.date_input !== null) return "date";
                    else if (element.input_element.number_input !== null) return "number";
                    else if (element.input_element.text_input !== null) return "text";
                    else if (element.input_element.select_input !== null) return "select";
                    else throw new Error("Invalid type of input element");
                }
            } else return "new_page";
        }
    },
}
</script>

<style scoped>

</style>
