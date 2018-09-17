<template>
      <vue-echart :options="lineOptions"
          ref="line"
          :auto-resize="true"/>
</template>

<style>
.echarts {
  height: 300px !important;
  width: 100% !important;
}
</style>

<script>
import ECharts from 'vue-echarts/components/ECharts.vue'

import 'echarts/lib/chart/line'
import 'echarts/lib/component/tooltip'

const colorsBlue = ['#647c8a', '#3f51b5', '#2196f3', '#00b862', '#afdf0a', '#a7b61a', '#f3e562', '#ff9800', '#ff5722', '#ff4514', '#647c8a', '#3f51b5', '#2196f3', '#00b862', '#afdf0a']
const colorsRed = ['#c23531','#2f4554', '#61a0a8', '#d48265', '#91c7ae','#749f83',  '#ca8622', '#bda29a','#6e7074', '#546570', '#c4ccd3'];

export default {
  name: 'chart-line-audiometry',
  metaInfo: {
    title: 'Audiometria - Reporte'
  },
  props:{
    data: {type: Array, required: true},
    dataAxis: {type: Array, required: true},
    title: {type: String, default:''},
    colorLine: {type: String, default:'red'}
  },
  components: {
    'vue-echart': ECharts
  },
  data:() => ({
    lineOptions:{}
    }),

  watch:{
    data(){
      this.lineOptions = {
        color: this.colorLine == 'red' ? colorsRed : colorsBlue,
        title: {
          text: this.title
        },
        tooltip: {
          trigger: 'axis',
          backgroundColor: '#46494d',
          textStyle: {
            fontSize: 12
          }
        },
        xAxis: {
          position: 'top',
          data: this.dataAxis,
          axisLine: {
            lineStyle: { color: 'rgba(0, 0, 0, .08)' }
          },
          axisLabel: { color: 'rgba(0, 0, 0, .5)' }
        },
        yAxis: {
          inverse: true,
          
          splitLine: { show: true},
          axisLine: {
            lineStyle: { color: 'rgba(0, 0, 0, .08)' }
          },
          axisLabel: { color: 'rgba(0, 0, 0, .5)' }
        },
        series: [{
          label: {
            show: true,
            position: 'bottom'
          },
          type: 'line',
          showSymbol: true,
          data: this.data
        }],
        animationDuration: 2000
      };
      
      let line = this.$refs.line
      line.hideLoading();
    }
  },
  mounted(){
    let line = this.$refs.line
      line.showLoading({
        text: 'Cargando…',
        color: '#4ea397',
        maskColor: 'rgba(255, 255, 255, 0.4)'
      });
  }
}
</script>
