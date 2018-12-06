<template>
    <div>
      <vue-echart :options="lineOptions"
          ref="lineMutiple"
          :auto-resize="true"
          v-show="!displayEmpty"/>
        <b-container v-show="displayEmpty">
            <b-row align-h="center">
                <b-col cols="6">No hay resultados</b-col>
            </b-row>
        </b-container>
    </div>
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
import 'echarts/lib/component/legend'

const colors = ['#c23531','#2f4554', '#61a0a8', '#d48265', '#91c7ae','#749f83',  '#ca8622', '#bda29a','#6e7074', '#546570', '#c4ccd3', '#647c8a', '#3f51b5', '#2196f3', '#00b862', '#afdf0a', '#a7b61a', '#f3e562', '#ff9800', '#ff5722', '#ff4514', '#647c8a', '#3f51b5', '#2196f3', '#00b862', '#afdf0a'];

export default {
  name: 'chart-line',
  metaInfo: {
    title: 'Informes'
  },
  props:{
    chartData: {
        type: Object,
        default() {
            return {
                xAxis: [],
                legend: [],
                datasets: []
            }
        }
    },
    title: {type: String, default:''}
  },
  components: {
    'vue-echart': ECharts
  },
  data:() => ({
    lineOptions:{}
    }),

  watch:{
    chartData(){
        this.showLoading()
        
        this.lineOptions = {
            color: colors,
            title: {
                text: this.title,
            },
            tooltip: {
                trigger: 'axis',
                backgroundColor: '#46494d',
                textStyle: {
                    fontSize: 12
                }
            },
            legend: {
                data: this.chartData.legend
            },
            grid: {
                left: '3%',
                right: '4%',
                bottom: '3%',
                containLabel: true
            },
            xAxis: {
                position: 'top',
                data: this.chartData.xAxis,
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
            series: this.chartData.datasets.data,
            animationDuration: 2000
        };
      
        let line = this.$refs.lineMutiple
        line.hideLoading();
    }
  },
  mounted(){
      
  },
  created(){

  },
  computed: {
    displayEmpty () {
        return (this.chartData.datasets.data != undefined && this.chartData.datasets.data.length > 0) ? false : true
    }
  },
  methods: {
    showLoading()
    {
        let line = this.$refs.lineMutiple
        line.showLoading({
            text: 'Cargando…',
            color: '#4ea397',
            maskColor: 'rgba(255, 255, 255, 0.4)'
        });
    }
  }
}
</script>
