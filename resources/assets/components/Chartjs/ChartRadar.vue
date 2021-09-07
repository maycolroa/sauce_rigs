<script>
import VueChartJs from 'vue-chartjs'

export default {
    extends: VueChartJs.Radar,
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
            responsive: false,
            maintainAspectRatio: false
        });
    }
  }
</script>