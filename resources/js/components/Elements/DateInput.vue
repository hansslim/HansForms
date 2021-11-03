<template>
    <div>
        {{ "date" }}
        <FormulateInput
            :name="this.propsId('date')"
            type="date"
            :label="propsLabel"
            :validation="validationRules"
            validationName="Date input"
            validation-behavior="live"
        />
        <FormulateErrors/>
    </div>
</template>

<script>

import {FormElementDefaultComputedProps, FormElementDefaultMethods, FormElementDefaultProps} from "./defaults";

export default {
    name: "DateInput",
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
