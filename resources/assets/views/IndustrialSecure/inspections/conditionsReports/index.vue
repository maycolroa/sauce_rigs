<template>
  <div>
    <h4 class="font-weight-bold mb-4">
       <span class="text-muted font-weight-light">Inspecciones y seguridad /</span> Condiciones Peligrosas
    </h4>


    <div class="col-md">
      <b-card no-body>
        <b-card-header class="with-elements">
          <!--<div class="card-title-elements" v-if="auth.can['inspec_conditionsReport_r']">
            <b-btn v-if="auth.can['reinc_checks_export']" variant="primary" @click="exportData()" v-b-tooltip.top title="Exportar Excel"><i class="fas fa-download"></i></b-btn>
          </div>-->
        </b-card-header>
        <b-card-body>
             <vue-table
                configName="inspections-conditionsReports"
                v-if="auth.can['inspect_conditionsReports_r']"
                ></vue-table>
        </b-card-body>
    </b-card>
    </div>
  </div>
</template>

<script>
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'inspections-conditionsReports',
  metaInfo: {
    title: 'Inspecciones y seguridad - Reportes Condiciones'
  },
  methods: {
    fetch()
    {
      this.$refs.tableCheck.refresh()
    },
    exportData(){
      axios.post('/industrialSecurity/inspections/conditionsReports/export', this.filters)
      .then(response => {
        Alerts.warning('Información', 'Se inicio la exportación, se le notificara a su correo electronico cuando finalice el proceso.');
      }).catch(error => {
        Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
      });
    },
  }
}
</script>