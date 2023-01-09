<template>
    <div>
      <vue-echart :options="pieOptions"
          ref="pie"
          :auto-resize="true"
          v-show="!displayEmpty"
          :style="{ height: '300px !important' }"/>
        <b-container v-show="displayEmpty">
            <b-row align-h="center">
                <b-col cols="6">No hay resultados</b-col>
            </b-row>
        </b-container>
    </div>
</template>

<style>
.echarts {
  width: 100% !important;
}
</style>

<script>
import ECharts from 'vue-echarts/components/ECharts.vue'

import 'echarts/lib/chart/pie'
import 'echarts/lib/component/tooltip'
import 'echarts/lib/component/legend'

const colorsBlue = ['#647c8a', '#3f51b5', '#2196f3', '#00b862', '#afdf0a', '#a7b61a', '#f3e562', '#ff9800', '#ff5722', '#ff4514', '#647c8a', '#3f51b5', '#2196f3', '#00b862', '#afdf0a']
const colorsRed = ['#f0635f','#2f3337', '#61a0a8', '#d48265', '#91c7ae','#749f83',  '#ca8622', '#bda29a','#6e7074', '#546570', '#c4ccd3'];

export default {
  name: 'chart-pie',
  props:{
    chartData: {
        type: Object,
        default() {
            return {
                labels: [],
                datasets: []
            }
        }
    },
    colors: {type: Array, default: []},
    title: {type: String, default:''},
    colorLine: {type: String, default:'red'}
  },
  components: {
    'vue-echart': ECharts
  },
  data:() => ({
    pieOptions:{},
    height: '300px !important',
    colorsDefault: ['#FFD950', '#7fafec', '#02BC77', '#f0635f', '#ff9565', '#ca8622', '#d48265']
    }),

  watch:{
    chartData(){
        this.showLoading()
        
        this.pieOptions = {
            color: this.colors.length > 0 ? this.colors : this.colorsDefault,
            backgroundColor: '#fff',
            title: {
                text: 'Pie chart',
                x: 'center'
            },
            tooltip: {
                trigger: 'item',
                formatter: '{a} <br/>{b} : {c} ({d}%)',
                backgroundColor: '#46494d',
                textStyle: {
                    fontSize: 11
                }
            },
            legend: {
                top: 'top',
                data: this.chartData.labels,    
            },
            series: [{
                name: this.title,
                type: 'pie',
                radius: '55%',
                center: ['50%', '60%'],
                data: this.chartData.datasets.data,
                itemStyle: {
                    emphasis: {
                        shadowBlur: 10,
                        shadowOffsetX: 0,
                        shadowColor: 'rgba(0, 0, 0, 0.5)'
                    }
                },
                label: {
                    formatter: '{c} - ({d}%)',
                }
            }],
            animationDuration: 2000
        };
      
        let pie = this.$refs.pie
        pie.hideLoading();
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
        let pie = this.$refs.pie
        pie.showLoading({
            text: 'Cargando…',
            color: '#4ea397',
            maskColor: 'rgba(255, 255, 255, 0.4)'
        });
    }
  }
}
</script>
