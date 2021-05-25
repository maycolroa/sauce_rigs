<template>
  <div>
    <header-module
      title="INSPECCIONES"
      subtitle="ADMINISTRAR INSPECCIONES PLANEADAS"
      url="industrialsecure-dangerousconditions"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-header class="with-elements">
          <div class="card-title-elements">
            <b-btn v-if="auth.can['ph_inspections_c']" :to="{name:'dangerousconditions-inspections-create'}" variant="primary">Crear Inspección</b-btn>
            <b-btn v-if="auth.can['ph_inspections_report_view']" :to="{name:'dangerousconditions-inspection-report'}" variant="primary">Ver Informes</b-btn>
            <b-btn v-if="auth.can['ph_inspections_export']" variant="primary" @click="exportData()" v-b-tooltip.top title="Exportar"><i class="fas fa-download"></i></b-btn>
            <b-btn v-if="auth.can['ph_inspections_c']" variant="primary" href="/templates/inspectionsimport" target="blank" v-b-tooltip.top title="Generar Plantilla"><i class="fas fa-file-alt"></i></b-btn>
            <div class="card-title-elements" v-if="auth.can['users_c']">
              <b-btn :to="{name:'dangerousconditions-inspections-import'}" variant="primary">Importar</b-btn>
            </div>-
          </div>
        </b-card-header>
        <b-card-body>
             <vue-table
                configName="dangerousconditions-inspections"
                :customColumnsName="true" 
                v-if="auth.can['ph_inspections_r']"
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
  name: 'dangerousConditions-inspections',
  metaInfo: {
    title: 'Inspecciones Planeadas'
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
      axios.post('/industrialSecurity/dangerousConditions/inspection/export', this.filters)
      .then(response => {
        Alerts.warning('Información', 'Se inicio la exportación, se le notificara a su correo electronico cuando finalice el proceso.');
      }).catch(error => {
        Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
      });
    }
  }
}
</script>
