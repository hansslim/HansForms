<template>
    <div>
        {{ "select" }}
        <FormulateInput
            :name="this.propsId('select')"
            type="radio"
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
            choices: {}
        }
    },
    props: [...FormElementDefaultProps],
    computed: {...FormElementDefaultComputedProps,
        validationRules() {
            let validation = [];
            if (this.propsIsMandatory) validation.push(['required']);

            return validation;
        }},
    mounted() {
        this.selectChoices();
    },
    methods: {
        ...FormElementDefaultMethods,
        selectChoices() {
            const options = this.$props["obj"].select_input_choices.sort((a, b) => {
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
        }
    }
}
</script>

<style scoped>

</style>
