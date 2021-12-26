<script>
import { Pie } from 'vue-chartjs'
export default {
    name: "Pie",
    extends: Pie,
    props: ['parsedData'],
    mounted() {
        this.renderChart(this.$props['parsedData'], {
            responsive: true,
            maintainAspectRatio: false
        })
    }
}
</script>

<style scoped>

</style>
