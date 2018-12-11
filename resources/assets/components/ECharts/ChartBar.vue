<template>
    <div>
        <perfect-scrollbar :options="{ wheelPropagation: true }" class="mb-4" style="height: 300px;">
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
        </perfect-scrollbar>
    </div>
</template>

<style>
.echarts {
  width: 100% !important;
}
</style>

<script>
import ECharts from 'vue-echarts/components/ECharts.vue'
import PerfectScrollbar from '@/vendor/libs/perfect-scrollbar/PerfectScrollbar'

import 'echarts/lib/chart/bar'
import 'echarts/lib/component/tooltip'
import 'echarts/lib/component/legend'

const colors = ['#c23531','#2f4554', '#61a0a8', '#d48265', '#91c7ae','#749f83',  '#ca8622', '#bda29a','#6e7074', '#546570', '#c4ccd3', '#647c8a', '#3f51b5', '#2196f3', '#00b862', '#afdf0a', '#a7b61a', '#f3e562', '#ff9800', '#ff5722', '#ff4514', '#647c8a', '#3f51b5', '#2196f3', '#00b862', '#afdf0a'];

export default {
  name: 'chart-bar',
  metaInfo: {
    title: 'Informes'
  },
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
    'vue-echart': ECharts,
    PerfectScrollbar
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
                left: '3%',
                right: '4%',
                bottom: '3%',
                top: '2%',
                containLabel: true
            },
            yAxis: {
                data: this.chartData.labels,
                axisLine: {
                    lineStyle: { color: 'rgba(0, 0, 0, .08)' }
                },
                axisLabel: { color: 'rgba(0, 0, 0, .5)' }
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
                        color: 'black'
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

        this.height = this.chartData.datasets.data.length * 30
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
