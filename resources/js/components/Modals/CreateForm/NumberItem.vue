<template>
    <div>
        <FormulateInput
            name="min"
            label="Minimal value"
            type="number"
        />
        <FormulateInput
            name="max"
            label="Maximal value"
            type="number"
        />
        <FormulateInput
            name="can_be_decimal"
            type="checkbox"
            label="Can be decimal"
            label-position="before"
        />
    </div>
</template>

<script>
export default {
    name: "NumberItem"
}
</script>

<style scoped>

</style>
