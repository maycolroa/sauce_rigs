<template>
  <div>
    <header-module
			title="CONTRATISTAS"
			subtitle="ADMINISTRAR INFORMES MENSUALES"
			url="legalaspects-contracts"
		/>

    <div class="col-md">
      <b-card no-body>
        <b-card-header class="with-elements">
          <div class="card-title-elements"> 
            <b-btn v-if="auth.can['contracts_informs_c']" :to="{name:'legalaspects-informs-create'}" variant="primary">Crear Informe</b-btn>
            <!--<b-btn v-if="auth.can['contracts_evaluations_report_view']" :to="{name:'legalaspects-evaluations-report'}" variant="primary">Ver Reportes</b-btn>
            <b-btn v-if="auth.can['contracts_typesQualification_r']" :to="{name:'legalaspects-typesrating'}" variant="primary">Administrar Procesos a Evaluar</b-btn>
          </div>
          <div class="card-title-elements ml-md-auto" v-if="auth.can['contracts_evaluations_export']">
            <b-dd variant="default" :right="isRTL">
              <template slot="button-content">
                <span class='fas fa-cogs'></span>
              </template>
              <b-dd-item @click="exportEvaluations()"><i class="fas fa-download"></i> &nbsp;Exportar</b-dd-item>
            </b-dd>-->
          </div>
        </b-card-header>
        <b-card-body>
             <vue-table
                v-if="auth.can['contracts_informs_r']"
                configName="legalaspects-informs"
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
  name: 'informs',
  metaInfo: {
    title: 'Informes'
  },
  data () {
    return {
      filters: []
    }
  },
  methods: {
    setFilters(value)
    {
      this.filters = value
    },
    /*exportEvaluations() {
      axios.post('/legalAspects/evaluation/export', this.filters)
        .then(response => {
          Alerts.warning('Información', 'Se inicio la exportación, se le notificara a su correo electronico cuando finalice el proceso.');
        }).catch(error => {
          Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
        });
    }*/
  }
}
</script>
