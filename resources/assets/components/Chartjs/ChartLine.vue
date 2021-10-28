<script>
import VueChartJs from 'vue-chartjs'

export default {
    extends: VueChartJs.Line,
    props: {
        chartData: {
            type: Object,
            default() {
                return {
                    labels: [],
                    datasets: []
                }
            }
        }
    },
    mounted () {
        this.renderChart(this.chartData, {
            responsive: true,
            maintainAspectRatio: false
        });
    }
  }
</script>