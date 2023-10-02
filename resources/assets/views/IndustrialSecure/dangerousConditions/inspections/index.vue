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
            <b-btn v-if="auth.can['ph_inspections_c']" :to="{name:'dangerousconditions-inspections-create'}" variant="primary">Crear Formato Inspección</b-btn>
            <b-btn v-if="auth.can['ph_inspections_report_view']" :to="{name:'dangerousconditions-inspection-report-menu'}" variant="primary">Ver Informes</b-btn>
            <b-btn v-if="auth.can['ph_inspections_export']" variant="primary" @click="exportData()" v-b-tooltip.top title="Exportar"><i class="fas fa-download"></i></b-btn>
            <b-btn v-if="auth.can['ph_inspections_c']" variant="primary" href="/templates/inspectionsimport" target="blank" v-b-tooltip.top title="Generar Plantilla"><i class="fas fa-file-alt"></i></b-btn>
            <div class="card-title-elements" v-if="auth.can['users_c']">
              <b-btn :to="{name:'dangerousconditions-inspections-import'}" variant="primary">Importar</b-btn>
            </div>
            <b-btn v-if="auth.can['ph_inspections_c']" variant="primary" :to="{name:'dangerousconditions-inspection-qualification-masive'}" v-b-tooltip.top title="Configurar opciones para la calificación másiva"><i class="oi oi-list"></i></b-btn>
            <b-btn v-if="auth.can['ph_inspections_r']" variant="primary" :to="{name:'dangerousconditions-inspections-request-firm'}" v-b-tooltip.top title="Ver Solicitudes de Firmas"><i class="ion ion-md-create"></i></b-btn>
          </div>
        </b-card-header>
        <b-card-body>
          <template v-if="showMessage">
            <p><b>{{message}}</b></p>
          </template>
          <template v-else>
             <vue-table
                configName="dangerousconditions-inspections"
                :customColumnsName="true" 
                v-if="auth.can['ph_inspections_r']"
                @filtersUpdate="setFilters"
                ></vue-table>
          </template>
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
      filters: [],
      showMessage: false,
      message: ''
    }
  },
  created() {
    this.getMessage()
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
    },
    getMessage()
    {
      axios.post('/industrialSecurity/dangerousConditions/inspection/getFiltersUsers')
      .then(response => {
        console.log(response)
        this.showMessage = response.data.data.value
        this.message = response.data.data.message
      }).catch(error => {
        Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
      });
    }
  }
}
</script>
