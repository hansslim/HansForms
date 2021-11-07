<template>
    <div>
        {{ "text" }}
        <FormulateInput
            :name="this.propsId('text')"
            type="text"
            :label="propsLabel"
            :validation="validationRules"
            validationName="Text input"
            validation-behavior="live"
        />
        <FormulateErrors/>
    </div>
</template>

<script>

import {FormElementDefaultComputedProps, FormElementDefaultMethods, FormElementDefaultProps} from "./defaults";

export default {
    name: "TextInput",
    props: [...FormElementDefaultProps],
    computed: {
        ...FormElementDefaultComputedProps,
        validationRules() {
            let validation = [];

/*
            if (this.propsIsMandatory === true) validation.push(['required']);
            if (this.propsObj.strict_length) {
                validation.push(['min', this.propsObj.strict_length]);
                validation.push(['max', this.propsObj.strict_length]);
            }
            else {
                if (this.propsObj.min_length) validation.push(['min', this.propsObj.min_length]);
                if (this.propsObj.max_length) validation.push(['max', this.propsObj.max_length]);
            }
*/

            return validation;
        },
    },
    methods: {
        ...FormElementDefaultMethods,
    }
}
</script>

<style scoped>

</style>
