<template>
    <div>
        <div v-if="range">
            <FormulateInput
                @input="showWantedInputs"
                name="min_length"
                label="Minimal length"
                type="number"
                min="0"
                v-model="min_length"
            />
            <FormulateInput
                @input="showWantedInputs"
                name="max_length"
                label="Maximal length"
                type="number"
                min="0"
                v-model="max_length"
            />
        </div>
        <div v-if="strict">
            <FormulateInput
                @input="showWantedInputs"
                name="strict_length"
                label="Strict length"
                type="number"
                v-model="strict_length"
            />
        </div>
    </div>
</template>

<script>
export default {
    name: "TextItem",
    data() {
        return {
            min_length: "",
            max_length: "",
            strict_length: "",
            strict: true,
            range: true
        }
    },
    methods: {
        showWantedInputs() {
            if(this.max_length||this.min_length) {
                this.strict = false;
                this.range = true;
            }
            else this.strict = true;
            if (this.strict_length) {
                this.range = false;
                this.strict = true;
            }
            else this.range = true;
        }
    }
}
</script>

<style scoped>

</style>
