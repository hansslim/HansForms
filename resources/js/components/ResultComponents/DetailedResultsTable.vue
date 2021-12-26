<template>
    <div class="table-responsive">
        <table class="table">
            <tr class="table-success">
                <th v-for="firstRowItem in  this.$props['obj'][1]" scope="col">{{firstRowItem}}</th>
            </tr>
            <tr v-for="otherRow in this.$props['obj'][2]">
                <td v-for="data in otherRow">{{ returnDashOnNull(data) }}</td>
            </tr>
        </table>
    </div>
</template>

<script>
export default {
    name: "DetailedResultsTable",
    props: ['obj'],
    methods: {
        returnDashOnNull(data) {
            if (data) return data;
            else return "-";
        }
    }
}
</script>

<style scoped>

</style>
