<template>
    <div>
        <hr>
        <div v-if="inputType==='text'">
            <text-input
                :isMandatory="this.$props['obj'].input_elements.is_mandatory"
                :label="label"
                :obj="this.$props['obj'].input_elements.text_input"
            />
        </div>
        <div v-if="inputType==='number'">
            <number-input
                :label="label"
                :obj="this.$props['obj'].input_elements.number_input"
                :isMandatory="isMandatory"
            />
        </div>
        <div v-if="inputType==='date'">
            <date-input
                :label="label"
                :obj="this.$props['obj'].input_elements.date_input"
                :isMandatory="isMandatory"
            />
        </div>
        <div v-if="inputType==='boolean'">
            <boolean-input
                :label="label"
                :obj="this.$props['obj'].input_elements.boolean_input"
                :isMandatory="isMandatory"
            />
        </div>
        <div v-if="inputType==='select'">
            <select-input
                :label="label"
                :obj="this.$props['obj'].input_elements.select_input"
                :isMandatory="isMandatory"
            />
        </div>
        <hr>
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
            return this.$props["obj"].input_elements.header;
        },
        isMandatory() {
            return this.$props["obj"].input_elements.is_mandatory;
        }
    },
    methods: {
        getType(element) {
            /*console.log(element);*/
            if (element.new_pages === null) {
                if (element.input_elements !== null) {
                    if (element.input_elements.boolean_input !== null) return "boolean";
                    else if (element.input_elements.date_input !== null) return "date";
                    else if (element.input_elements.number_input !== null) return "number";
                    else if (element.input_elements.text_input !== null) return "text";
                    else if (element.input_elements.select_input !== null) return "select";
                    else throw new Error("Invalid type of input element");
                }
            } else return "new_page";
        }
    },
}
</script>

<style scoped>

</style>
