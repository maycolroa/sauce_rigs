<template>
      <vue-echart :options="pieOptions"
          ref="pie"
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

import 'echarts/lib/chart/pie'
import 'echarts/lib/component/tooltip'
import 'echarts/lib/component/legend'

const colorsBlue = ['#647c8a', '#3f51b5', '#2196f3', '#00b862', '#afdf0a', '#a7b61a', '#f3e562', '#ff9800', '#ff5722', '#ff4514', '#647c8a', '#3f51b5', '#2196f3', '#00b862', '#afdf0a']
const colorsRed = ['#c23531','#2f4554', '#61a0a8', '#d48265', '#91c7ae','#749f83',  '#ca8622', '#bda29a','#6e7074', '#546570', '#c4ccd3'];

export default {
  name: 'chart-pie-audiometry',
  metaInfo: {
    title: 'Audiometria - Informes'
  },
  props:{
    data: {type: Array, required: true},
    legend: {type: Array, required: true},
    title: {type: String, default:''},
    colorLine: {type: String, default:'red'}
  },
  components: {
    'vue-echart': ECharts
  },
  data:() => ({
    pieOptions:{}
    }),

  watch:{
    data(){
        this.showLoading()
        
        this.pieOptions = {
            color: this.colorLine == 'red' ? colorsRed : colorsBlue,
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
                orient: 'vertical',
                left: 'left',
                data: this.legend,
            },
            series: [{
                name: 'Informes PTA',
                type: 'pie',
                radius: '55%',
                data: this.data,
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
