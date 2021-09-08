<template>
  <div>
    <h4 class="font-weight-bold py-3 mb-4">
      <span class="text-muted font-weight-light">Charts /</span> Vue Chartjs
    </h4>

    <hr class="border-light container-m--x mt-0 mb-5">

    <div class="demo-vertical-spacing">
      <vue-chartjs-radar-example :height="250" /><br>
    </div>

  </div>
</template>

<script>
import Vue from 'vue'
import VueChartJs from 'vue-chartjs'

const options = {
  responsive: false,
  maintainAspectRatio: false
}

Vue.component('vue-chartjs-radar-example', {
  extends: VueChartJs.Radar,
  mounted () {
    this.renderChart({
      labels: ['Eating', 'Drinking', 'Sleeping', 'Designing', 'Coding', 'Cycling', 'Running'],
      datasets: [{
        label: 'My First dataset',
        backgroundColor: 'rgba(76, 175, 80, 0.3)',
        borderColor: '#4CAF50',
        pointBackgroundColor: '#4CAF50',
        pointBorderColor: '#fff',
        pointHoverBackgroundColor: '#fff',
        pointHoverBorderColor: '#4CAF50',
        data: [39, 99, 77, 38, 52, 24, 89],
        borderWidth: 1
      }, {
        label: 'My Second dataset',
        backgroundColor: 'rgba(0, 150, 136, 0.3)',
        borderColor: '#009688',
        pointBackgroundColor: '#009688',
        pointBorderColor: '#fff',
        pointHoverBackgroundColor: '#fff',
        pointHoverBorderColor: '#009688',
        data: [6, 33, 14, 70, 58, 90, 26],
        borderWidth: 1
      }]
    }, options)
  }
})

export default {
    name: 'charts-vue-chartjs',
    metaInfo: {
      title: 'Vue Chartjs - Charts'
    },
    mounted () {
      const charts = this.$children.filter(component => /^vue-chartjs-/.test(component.$options._componentTag))
  
      const resizeCharts = () => charts.forEach(chart => chart._data._chart.resize())
  
      // Initial resize
      resizeCharts()
  
      // For performance reasons resize charts on delayed resize event
      this.layoutHelpers.on('resize.charts-demo', resizeCharts)
    },
    beforeDestroy () {
      this.layoutHelpers.off('resize.charts-demo')
    }
  }
</script>