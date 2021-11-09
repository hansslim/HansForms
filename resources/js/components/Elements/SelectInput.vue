<template>
    <div>
        {{ this.type }}
        <FormulateInput
            :name="this.propsId('select')"
            :type="this.type"
            :label="propsLabel"
            :validation="validationRules"
            validationName="Select input"
            validation-behavior="live"
            :options="this.choices"
        />
        <FormulateErrors/>
    </div>
</template>

<script>

import {FormElementDefaultComputedProps, FormElementDefaultMethods, FormElementDefaultProps} from "./defaults";

export default {
    name: "SelectInput",
    data() {
        return {
            choices: [],
            type: "radio"
        }
    },
    props: [...FormElementDefaultProps],
    computed: {
        ...FormElementDefaultComputedProps,
        validationRules() {
            let validation = [];

            if (this.propsIsMandatory) validation.push(['required']);
            if (this.type === "checkbox") {
                if (this.propsIsMandatory) {
                    if (this.propsObj.strict_amount_of_answers) {
                        validation.push(['min', this.propsObj.strict_amount_of_answers]);
                        validation.push(['max', this.propsObj.strict_amount_of_answers]);
                    } else {
                        if (this.propsObj.min_amount_of_answers) validation.push(['min', this.propsObj.min_amount_of_answers]);
                        if (this.propsObj.max_amount_of_answers) validation.push(['max', this.propsObj.max_amount_of_answers]);
                    }
                }
                else {
                    if (this.propsObj.max_amount_of_answers) validation.push(['max', this.propsObj.max_amount_of_answers]);
                }

            }
            else if (this.type === "radio" && !this.propsIsMandatory){
                this.choices = [...this.choices, {value: `choice-null`, label: `I don't want to answer.`}];
            }

            return validation;
        },

    },

    mounted() {
        this.selectChoices();
        this.selectType();
    },
    methods: {
        ...FormElementDefaultMethods,
        selectChoices() {
            const options = this.propsObj.select_input_choices.sort((a, b) => {
                if (a.order < b.order) {
                    return -1;
                }
                if (a.order > b.order) {
                    return 1;
                }
                return 0;
            });
            let selectChoices = [];
            options.forEach((option) => {
                selectChoices.push({value: `choice-${option.id}`, label: `${option.text}`})
            })
            this.choices = selectChoices;
        },
        selectType() {
            if (this.propsObj.is_multiselect) this.type = "checkbox";
            else this.type = "radio";
        },
    }
}
</script>

<style scoped>

</style>
