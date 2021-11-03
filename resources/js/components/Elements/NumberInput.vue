<template>
    <div>
        {{ "number" }}
        <FormulateInput
            :name="this.propsId('number')"
            type="number"
            :label="propsLabel"
            :validation="validationRules"
            validationName="Number input"
            validation-behavior="live"
        />
        <FormulateErrors/>
    </div>
</template>

<script>

import {FormElementDefaultComputedProps, FormElementDefaultMethods, FormElementDefaultProps} from "./defaults";

export default {
    name: "NumberInput",
    props: [...FormElementDefaultProps],
    computed: {...FormElementDefaultComputedProps,
        validationRules() {
            let validation = [];
            if (this.propsIsMandatory) validation.push(['required']);

            return validation;
        }},
    methods: {...FormElementDefaultMethods}
}
</script>

<style scoped>

</style>
