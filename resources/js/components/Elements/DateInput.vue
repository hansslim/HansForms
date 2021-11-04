<template>
    <div>
        <FormulateInput
            :name="this.propsId('date')"
            type="date"
            :label="propsLabel"
            :validation="validationRules"
            validationName="Date input"
            validation-behavior="live"
            :min="min"
            :max="max"
        />
        <FormulateErrors/>
    </div>
</template>

<script>

import {FormElementDefaultComputedProps, FormElementDefaultMethods, FormElementDefaultProps} from "./defaults";

export default {
    name: "DateInput",
    data() {
        return {
            min: "",
            max: "",
        }
    },
    props: [...FormElementDefaultProps],
    computed: {...FormElementDefaultComputedProps,
        validationRules() {
            let validation = [];

            if (this.propsIsMandatory) validation.push(['required']);
            if (this.propsObj.min) {
                validation.push(['after', this.propsObj.min]);
                this.min = this.propsObj.min;
            }
            if (this.propsObj.max) {
                validation.push(['before', this.propsObj.max]);
                this.max = this.propsObj.max;
            }

            return validation;
        }},
    methods: {...FormElementDefaultMethods}
}
</script>

<style scoped>

</style>
