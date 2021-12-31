<template>
    <div>
        <FormulateInput
            name="min"
            label="Minimal value"
            type="number"
            v-model="min"
            :step="this.step"
        />
        <FormulateInput
            name="max"
            label="Maximal value"
            type="number"
            v-model="max"
            :step="this.step"
        />
        <FormulateInput
            name="can_be_decimal"
            type="checkbox"
            label="Can be decimal"
            label-position="before"
            v-model="can_be_decimal"
            @input="handleCanBeDecimal"
        />
    </div>
</template>

<script>
export default {
    name: "NumberItem",
    props: ['obj'],
    data() {
        return {
            min: "",
            max: "",
            can_be_decimal: false,
            step: "0",
        }
    },
    mounted() {
        if (this.$props['obj']) {
            if (!isNaN(this.$props['obj'].min)) {
                this.min = this.$props['obj'].min;
            }
            if (!isNaN(this.$props['obj'].max)) {
                this.max = this.$props['obj'].max;
            }
            if (!isNaN(this.$props['obj'].can_be_decimal)) {
                if (this.$props['obj'].can_be_decimal) {
                    this.can_be_decimal = true;
                }
            }
        }
    },
    methods: {
        handleCanBeDecimal() {
            if (this.can_be_decimal) this.step = "any";
            else this.step = "0";
        }
    }
}
</script>

<style scoped>

</style>
