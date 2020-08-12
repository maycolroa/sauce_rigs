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
            :modelId="`${this.$route.params.id}`"
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
  name: 'dangerousconditions-inspections-qualification',
  metaInfo: {
    title: 'Inspecciones - Calificadas'
  },
  data () {
    return {
      filters: [],
      inspection_id: this.$route.params.id
    }
  },
  methods: {
    setFilters(value)
    {
      this.filters = value
    },
    exportData()
    {
      console.log(this.filters.dateRange)
      if (this.filters.dateRange) {

        this.postData = Object.assign({}, {id: this.inspection_id}, this.filters);

        axios.post('/industrialSecurity/dangerousConditions/inspection/exportQualify', this.postData)
        .then(response => {
          Alerts.warning('Información', 'Se inicio la exportación, se le notificara a su correo electronico cuando finalice el proceso.');
        }).catch(error => {
          if (error.response.status == 500 && error.response.data.error != 'Internal Error')
          {
            Alerts.error('Error', error.response.data.error);
          }
          else 
          {
            Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
          }
        });
      }
      else {
        Alerts.error('Error', 'Debe elegir un rango de fecha no mayor a 6 meses');
      }
    },
  }
}
</script>
