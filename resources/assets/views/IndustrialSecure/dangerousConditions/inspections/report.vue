<template>
  <div>
    <h4 class="font-weight-bold mb-4">
       <span class="text-muted font-weight-light">Inspecciones /</span> Reportes
    </h4>

    <div class="col-md">
        <b-card no-body>
            <b-card-header class="with-elements">
                <div class="card-title-elements"> 
                    <b-btn :to="{name:'dangerousconditions-inspections'}" variant="secondary">Regresar</b-btn>
                    <b-btn variant="primary" @click="exportReport()" v-b-tooltip.top title="Exportar"><i class="fas fa-download"></i></b-btn>
                </div>
            </b-card-header>
            <b-card-body>
                 <div style="padding-top: ">
                    <b-card bg-variant="transparent" border-variant="dark" title="Totales" class="mb-3 box-shadow-none">
                        <b-row>
                            <b-col>
                                <div><b>Inspecciones:</b> {{ information.inspections }}</div>
                                <div><b># Cumplimientos:</b> {{information.t_cumple}}</div>
                                <div><b># No Cumplimientos:</b> {{information.t_no_cumple}}</div>
                            </b-col>
                            <b-col>
                                <div><b>% Cumplimientos:</b> {{information.p_cumple}}</div>
                                <div><b>% No Cumplimientos:</b> {{information.p_no_cumple}}</div>
                            </b-col>
                            <b-col>
                                <div><b># Planes de acción realizados:</b> {{ information.pa_realizados }}</div>
                                <div><b># Planes de acción no realizados:</b> {{ information.pa_no_realizados }}</div>
                            </b-col>
                        </b-row>
                    </b-card>
                </div>
                <div>
                    <vue-advanced-select class="col-md-6" v-model="table" :multiple="false" :options="options" :hide-selected="false" @input="refreshData" name="table" label="Tabla" placeholder="Seleccione una opción">
                    </vue-advanced-select>
                </div>
                <vue-table
                    ref="tableReport"
                    configName="dangerousconditions-inspections-report"
                    @filtersUpdate="setFilters"
                    :params="{table: table}"
                ></vue-table>
            </b-card-body>
        </b-card>
    </div>
  </div>
</template>

<script>
import Alerts from '@/utils/Alerts.js';
import VueAdvancedSelect from "@/components/Inputs/VueAdvancedSelect.vue";

export default {
    name: 'dangerousconditions-inspections-report',
    metaInfo: {
        title: 'Inspecciones - Reportes'
    },
    components:{
        VueAdvancedSelect
    },
    data () {
        return {
            filters: [],
            information: {
                inspections: 0,
                t_cumple: 0,
                t_no_cumple: 0,
                p_cumple: '0%',
                p_no_cumple: '0%',
                pa_realizados: 0,
                pa_no_realizados: 0
            },
            table: 'without_theme',
            options: [
					{ name:'Con Tema', value:'with_theme'},
					{ name:'Sin Tema', value:'without_theme'}
				]
        }
    },
    created() {
        this.updateTotales()
    },
    methods: {
        refreshData()
        {
            this.$refs.tableReport.refresh()
            //this.updateTotales()
        },
        setFilters(value)
        { 
            this.filters = value
            this.updateTotales()
        },
        exportReport() {
            let postData = Object.assign({}, {table: this.table}, this.filters);
            axios.post('/industrialSecurity/dangerousConditions/inspection/exportReport', postData)
                .then(response => {
                    Alerts.warning('Información', 'Se inicio la exportación, se le notificara a su correo electronico cuando finalice el proceso.');
                }).catch(error => {
                    Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
                });
        },
        updateTotales()
        {
            let postData = Object.assign({}, {table: this.table}, this.filters);
            axios.post('/industrialSecurity/dangerousConditions/inspection/report/getTotals', postData)
                .then(response => {
                    this.information = response.data.data;
                })
                .catch(error => {
                    Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
                    this.$router.go(-1);
                });
        }
    }
}
</script>