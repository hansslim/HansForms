<template>
    <div>
        <FormulateInput
            name="min"
            label="Minimal value"
            type="number"
            v-model="min"
        />
        <FormulateInput
            name="max"
            label="Maximal value"
            type="number"
            v-model="max"
        />
        <FormulateInput
            name="can_be_decimal"
            type="checkbox"
            label="Can be decimal"
            label-position="before"
            v-model="can_be_decimal"
        />
    </div>
</template>

<script>
export default {
    name: "NumberItem",
    data() {
        return {
            min: "",
            max: "",
            can_be_decimal: ""
        }
    }
}
</script>

<style scoped>

</style>
