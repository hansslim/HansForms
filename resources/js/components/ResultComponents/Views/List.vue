<template>
    <div class="d-flex justify-content-center">
        <table>
            <tr>
                <th>Completion ID</th>
                <th>Answer</th>
            </tr>
            <tr v-for="item in this.$props['parsedData']" :key="item.id">
                <td>{{ item.id }}</td>
                <td>{{ item.value }}</td>
            </tr>
        </table>
    </div>
</template>

<script>
export default {
    name: "List",
    props: ['parsedData'],
}
</script>

<style scoped>

</style>
