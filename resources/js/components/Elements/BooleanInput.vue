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
            :options="{true: 'Yes', false: 'No'}"
        />
        <FormulateErrors/>
    </div>
</template>

<script>

import {FormElementDefaultComputedProps, FormElementDefaultMethods, FormElementDefaultProps} from "./defaults";

export default {
    name: "BooleanInput",
    props: [...FormElementDefaultProps],
    computed: {
        ...FormElementDefaultComputedProps,
        validationRules() {
            let validation = [];
            if (this.propsIsMandatory) validation.push(['required']);

            return validation;
        }
        },
    methods: {...FormElementDefaultMethods}
}
</script>

<style scoped>

</style>
