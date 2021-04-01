<template>
  <div>
    <header-module
      title="INSPECCIONES"
      subtitle="ADMINISTRAR INSPECCIONES NO PLANEADAS"
      url="industrialsecure-dangerousconditions"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-header class="with-elements">
          <div class="card-title-elements">
            <b-btn v-if="auth.can['ph_reports_c']" :to="{name:'dangerousconditions-reports-create'}" variant="primary">Crear Inspección no planeada</b-btn>
            <b-btn v-if="auth.can['ph_reports_informs_view']" :to="{name:'dangerousconditions-reports-informs'}" variant="primary">Ver Informes</b-btn>
            <b-btn v-if="auth.can['ph_reports_export']" variant="primary" @click="exportData()" v-b-tooltip.top title="Exportar"><i class="fas fa-download"></i></b-btn>
          </div>
        </b-card-header>
        <b-card-body>
             <vue-table
                configName="dangerousconditions-report"
                v-if="auth.can['ph_reports_r']"
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
  name: 'dangerousconditions-reports',
  metaInfo: {
    title: 'Inspecciones - Inspecciones no planeadas'
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
    exportData(){
      axios.post('/industrialSecurity/dangerousConditions/report/export', this.filters)
      .then(response => {
        Alerts.warning('Información', 'Se inicio la exportación, se le notificara a su correo electronico cuando finalice el proceso.');
      }).catch(error => {
        Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
      });
    },
  }
}
</script>