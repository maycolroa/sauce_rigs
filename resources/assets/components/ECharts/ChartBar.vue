<template>
    <div>
        <div style="height: 300px; overflow:auto;">
            <vue-echart :options="barOptions"
                ref="bar"
                :auto-resize="true"
                v-show="!displayEmpty"
                :style="{ height: barHeight + 'px !important' }"/>
                <b-container v-show="displayEmpty">
                    <b-row align-h="center">
                        <b-col cols="6">No hay resultados</b-col>
                    </b-row>
                </b-container>
        </div>
    </div>
</template>

<style>
.echarts {
  width: 100% !important;
}
</style>

<script>
import ECharts from 'vue-echarts/components/ECharts.vue'

import 'echarts/lib/chart/bar'
import 'echarts/lib/component/tooltip'
import 'echarts/lib/component/legend'

const colors = ['#f0635f','#2f3337', '#61a0a8', '#d48265', '#91c7ae','#749f83',  '#ca8622', '#bda29a','#6e7074', '#546570', '#c4ccd3', '#647c8a', '#3f51b5', '#2196f3', '#00b862', '#afdf0a', '#a7b61a', '#f3e562', '#ff9800', '#ff5722', '#ff4514', '#647c8a', '#3f51b5', '#2196f3', '#00b862', '#afdf0a'];

export default {
  name: 'chart-bar',    
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
    title: {type: String, default:''}
  },
  components: {
    'vue-echart': ECharts
  },
  data:() => ({
    barOptions:{},
    height: 300
    }),

  watch:{
    chartData(){
        this.showLoading()
        
        this.barOptions = {
            color: colors,
            backgroundColor: '#fff',
            title: {
                text: this.title,
            },
            tooltip: {
                trigger: 'axis',
                axisPointer : {            
                    type : 'shadow'        
                }
            },
            legend: {
                data: ['bar']
            },
            grid: {
                containLabel: true
            },
            yAxis: {
                data: this.chartData.labels,
                axisLine: {
                    lineStyle: { color: 'rgba(0, 0, 0, .08)' }
                },
                axisLabel: { color: 'rgba(0, 0, 0, .5)', interval: 0 }
            },
            xAxis: {
                splitLine: { show: false },
                axisLine: {
                    lineStyle: { color: 'rgba(0, 0, 0, .08)' }
                },
                axisLabel: { color: 'rgba(0, 0, 0, .5)' }
            },
            series: [{
                type: 'bar',
                showSymbol: false,
                data: this.chartData.datasets.data,
                barCategoryGap: '60%',
                label: {
                    normal: {
                        show: true,
                        position: 'right',
                        color: 'black',
                        formatter: this.formatter,
                    }
                },
                itemStyle: {
                    normal: {
                        color: function (params) {
                            var num = colors.length
                            return colors[params.dataIndex % num]
                        },
                        opacity: 0.5
                    },
                    emphasis: {
                        opacity: 1,
                        shadowBlur: 50,
                        shadowColor: 'rgba(0, 0, 0, 0.1)'
                    }
                }
            }],
            animationDuration: 2000
        };
      
        let bar = this.$refs.bar
        bar.hideLoading();

        if ((this.chartData.datasets.data.length * 30) > 300)
            this.height = this.chartData.datasets.data.length * 30
        else 
            this.height = 300
    }
  },
  mounted(){
      
  },
  created(){

  },
  computed: {
    displayEmpty () {
        return (this.chartData.datasets.data != undefined && this.chartData.datasets.data.length > 0) ? false : true
    },
    barHeight() {
        return this.height
    },
    formatter() {
        return this.chartData.datasets.type ? (this.chartData.datasets.type == 'percentage' ? '{c}%' : '{c}') : '{c}';
    }
  },
  methods: {
    showLoading()
    {
        let bar = this.$refs.bar
        bar.showLoading({
            text: 'Cargando…',
            color: '#4ea397',
            maskColor: 'rgba(255, 255, 255, 0.4)'
        });
    }
  }
}
</script>
