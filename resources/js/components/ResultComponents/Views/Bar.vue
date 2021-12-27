<script>
import { Bar } from 'vue-chartjs'
export default {
    name: "Bar",
    extends: Bar,
    props: ['parsedData'],
    mounted() {
        this.renderChart(this.$props['parsedData'], {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true,
                        userCallback: function(label, index, labels) {
                            if (Math.floor(label) === label) {
                                return label;
                            }
                        },
                    }
                }]
            },
            legend: {
                display: false
            },
            tooltips: {
                callbacks: {
                    label: function(tooltipItem) {
                        return tooltipItem.yLabel;
                    }
                }
            }
        })
    }
}
</script>

<style scoped>

</style>
