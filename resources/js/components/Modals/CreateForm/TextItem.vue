<template>
    <div>
        <FormulateInput
            name="min_length"
            label="Minimal length"
            type="number"
        />
        <FormulateInput
            name="max_length"
            label="Maximal length"
            type="number"
        />
        <FormulateInput
            name="strict_length"
            label="Strict length"
            type="number"
        />
    </div>
</template>

<script>
export default {
    name: "TextItem"
}
</script>

<style scoped>

</style>
