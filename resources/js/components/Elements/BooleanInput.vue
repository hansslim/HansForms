<template>
    <div>
        {{ "boolean" }}
        <FormulateInput
            :name="this.propsId('boolean')"
            type="radio"
            :label="propsLabel"
            :validation="validationRules"
            validationName="Boolean input"
            validation-behavior="live"
            :options="this.options"
        />
        <FormulateErrors/>
    </div>
</template>

<script>

import {FormElementDefaultComputedProps, FormElementDefaultMethods, FormElementDefaultProps} from "./defaults";

export default {
    name: "BooleanInput",
    data() {
        return {
            options: {true: 'Yes', false: 'No'}
        }
    },
    props: [...FormElementDefaultProps],
    computed: {
        ...FormElementDefaultComputedProps,
        validationRules() {
            let validation = [];
            if (!this.propsIsMandatory) this.options = {...this.options, null: "I don't want to answer" }
            else validation.push(['required']);

            return validation;
        }
        },
    methods: {...FormElementDefaultMethods}
}
</script>

<style scoped>

</style>
