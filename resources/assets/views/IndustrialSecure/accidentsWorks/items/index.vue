<template>
  <div>
    <header-module
      title="ACCIDENTES E INCIDENTES DE TRABAJO"
      subtitle="ADMINISTRAR ACCIDENTES"
      url="industrialsecure-dangerousconditions"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-header class="with-elements">
          <div class="card-title-elements" v-if="auth.can['accidentsWork_c']">
            <b-btn :to="{name:'industrialsecure-accidentswork-create'}" variant="primary">Crear Reporte</b-btn><b-btn v-if="auth.can['ph_inspections_export']" variant="primary" @click="exportData()" v-b-tooltip.top title="Exportar"><i class="fas fa-download"></i></b-btn>
            <b-btn v-if="auth.can['accidentsWork_r']" :to="{name:'industrialsecure-accidentswork-report'}" variant="primary">Reportes</b-btn>
            <b-btn v-if="auth.can['accidentsWork_c']" :to="{name:'industrialsecure-accidentswork-index-causes'}" variant="primary">Administrar Causas</b-btn>
          </div>
        </b-card-header>
        <b-card-body>
             <vue-table
                configName="industrialsecure-accidents"
                v-if="auth.can['accidentsWork_r']"
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
  name: 'accidentsWork',
  metaInfo: {
    title: 'Accidentes e incidentes'
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
    exportData()
    {
      axios.post('/industrialSecurity/accidents/export', this.filters)
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
    },
  }
}
</script>
