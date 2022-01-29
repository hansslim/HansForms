<template>
    <div>
        <div v-if="range">
            <FormulateInput
                @input="showWantedInputs"
                name="min_length"
                label="Minimal length"
                type="number"
                min="0"
                max="1022"
                validation="min:0|max:1022"
                v-model="min_length"
            />
            <FormulateInput
                @input="showWantedInputs"
                name="max_length"
                label="Maximal length"
                type="number"
                min="0"
                max="1023"
                validation="min:0|max:1023"
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
                max="1023"
                min="0"
                validation="min:0|max:1023"
            />
        </div>
    </div>
</template>

<script>
export default {
    name: "TextItem",
    props: ['obj'],
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
    },
    mounted() {
        if (this.$props['obj']) {
            let strict = false;
            if (!isNaN(this.$props['obj'].min_length)) {
                strict = true;
                this.min_length = this.$props['obj'].min_length;
            }
            if (!isNaN(this.$props['obj'].max_length)) {
                strict = true;
                this.max_length = this.$props['obj'].max_length;
            }
            if (!strict) {
                if (!isNaN(this.$props['obj'].strict_length)) {
                    this.strict_length = this.$props['obj'].strict_length;
                }
            }
        }
    }
}
</script>

<style scoped>

</style>
