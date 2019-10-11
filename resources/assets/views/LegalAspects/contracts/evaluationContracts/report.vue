<template>
  <div>
    <header-module
        title="CONTRATISTAS"
        subtitle="REPORTE DE EVALUACIONES REALIZADAS"
        url="legalaspects-evaluations"
    />

    <div class="col-md">
        <b-card no-body>
            <b-card-header class="with-elements">
                <div class="card-title-elements ml-md-auto" v-if="auth.can['contracts_evaluations_report_export']">
                    <b-dd variant="default" :right="isRTL">
                        <template slot="button-content">
                            <span class='fas fa-cogs'></span>
                        </template>
                        <b-dd-item @click="exportReport()"><i class="fas fa-download"></i> &nbsp;Exportar</b-dd-item>
                    </b-dd>
                </div>
            </b-card-header>
            <b-card-body>
                <div style="padding-top: ">
                    <b-card bg-variant="transparent" border-variant="dark" title="Totales" class="mb-3 box-shadow-none">
                        <b-row>
                            <b-col>
                                <div><b>Evaluaciones:</b> {{information.evaluations}}</div>
                                <div><b># Cumplimientos:</b> {{information.t_cumple}}</div>
                                <div><b># No Cumplimientos:</b> {{information.t_no_cumple}}</div>
                            </b-col>
                            <b-col>
                                <div><b>% Cumplimientos:</b> {{information.p_cumple}}</div>
                                <div><b>% No Cumplimientos:</b> {{information.p_no_cumple}}</div>
                            </b-col>
                        </b-row>
                    </b-card>
                </div>
                <vue-table
                    configName="legalaspects-evaluations-reports"
                    @filtersUpdate="setFilters"
                    ></vue-table>
            </b-card-body>
        </b-card>
    </div>
  </div>
</template>

<script>
import Alerts from '@/utils/Alerts.js';

export default {
    name: 'legalaspects-evaluations-report',
    metaInfo: {
        title: 'Evaluaciones - Reportes'
    },
    components:{
    },
    data () {
        return {
            filters: [],
            information: {
                evaluations: 0,
                t_cumple: 0,
                t_no_cumple: 0,
                p_cumple: '0%',
                p_no_cumple: '0%'
            }
        }
    },
    created() {
        this.updateTotales()
    },
    methods: {
        setFilters(value)
        { 
            this.filters = value
            this.updateTotales()
        },
        exportReport() {
            axios.post('/legalAspects/evaluationContract/exportReport', this.filters)
                .then(response => {
                    Alerts.warning('Información', 'Se inicio la exportación, se le notificara a su correo electronico cuando finalice el proceso.');
                }).catch(error => {
                    Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
                });
        },
        updateTotales()
        {
            axios.post('/legalAspects/evaluationContract/getTotales', this.filters)
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