<template>
    <div>
        <FormulateInput
            name="is_multiselect"
            type="checkbox"
            label="Multiselect"
            label-position="before"
            v-model="is_multiselect"
        />
        <FormulateInput
            name="has_hidden_label"
            type="checkbox"
            label="Scale (has hidden label to fill)"
            label-position="before"
            v-model="has_hidden_label"
        />
        <div v-if="is_multiselect">
            <FormulateInput
                v-if="range"
                @input="showWantedInputs"
                label="Minimal amount of answers"
                name="min_amount_of_answers"
                type="number"
                v-model="min_amount_of_answers"
            />
            <FormulateInput
                v-if="range"
                @input="showWantedInputs"
                label="Maximal amount of answers"
                name="max_amount_of_answers"
                type="number"
                v-model="max_amount_of_answers"
            />
            <FormulateInput
                v-if="strict"
                @input="showWantedInputs"
                label="Strict amount of answers"
                name="strict_amount_of_answers"
                type="number"
                v-model="strict_amount_of_answers"
            />
        </div>
        <select-choices-component :obj="this.$props['obj']" :hasHiddenLabel="has_hidden_label"/>
    </div>
</template>

<script>
import SelectChoicesComponent from "./SelectChoicesComponent";


export default {
    name: "SelectItem",
    components: {
        "select-choices-component": SelectChoicesComponent
    },
    props: ['obj'],
    data() {
        return {
            is_multiselect: false,
            has_hidden_label: false,
            min_amount_of_answers: "",
            max_amount_of_answers: "",
            strict_amount_of_answers: "",
            choices: [],
            strict: true,
            range: true
        }
    },
    methods: {
        showWantedInputs() {
            if (this.max_amount_of_answers || this.min_amount_of_answers) {
                this.strict = false;
                this.range = true;
            } else this.strict = true;
            if (this.strict_amount_of_answers) {
                this.range = false;
                this.strict = true;
            } else this.range = true;
        }
    },
    mounted() {
        if (this.$props['obj']) {
            if (this.$props['obj'].is_multiselect) {
                this.is_multiselect = true;
                if (!isNaN(this.$props['obj'].strict_amount_of_answers)) {
                    this.strict_amount_of_answers = this.$props['obj'].strict_amount_of_answers;
                } else {
                    if (!isNaN(this.$props['obj'].min_amount_of_answers)) {
                        this.min_amount_of_answers = this.$props['obj'].min_amount_of_answers;
                    }
                    if (!isNaN(this.$props['obj'].max_amount_of_answers)) {
                        this.max_amount_of_answers = this.$props['obj'].max_amount_of_answers;
                    }
                }
            }
            if (this.$props['obj'].choices) {
                this.choices = this.$props['obj'].choices;
            }
            if (this.$props['obj'].has_hidden_label) {
                this.has_hidden_label = this.$props['obj'].has_hidden_label;
            }
        }
        this.showWantedInputs()
    }
}
</script>

<style scoped>

</style>
