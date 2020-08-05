<template>
  <div>
    <header-module
      title="CONDICIONES PELIGROSAS"
      subtitle="INSPECCIONES CALIFICADAS"
      url="dangerousconditions-inspections"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-header class="with-elements">
          <div class="card-title-elements">
            <b-btn v-if="auth.can['ph_inspections_export']" variant="primary" @click="exportData()" v-b-tooltip.top title="Exportar"><i class="fas fa-download"></i></b-btn>
          </div>
        </b-card-header>
        <b-card-body>
              <vue-table
                configName="dangerousconditions-inspections-qualification"
                :customColumnsName="true" 
                v-if="auth.can['ph_inspections_r']"
                :params="{inspectionId: `${this.$route.params.id}`}"
                ></vue-table>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'dangerousconditions-inspections-qualification',
  metaInfo: {
    title: 'Inspecciones - Calificadas'
  }
}
 methods: {
    exportData(){
      axios.post('/industrialSecurity/dangerousConditions/inspection/exportQualify', this.filters)
      .then(response => {
        Alerts.warning('Información', 'Se inicio la exportación, se le notificara a su correo electronico cuando finalice el proceso.');
      }).catch(error => {
        Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
      });
    },
  }
</script>
