<template>
  <div>
    <header-module
      title="SISTEMA"
      subtitle="ADMINISTRAR LICENCIAS"
      url="system"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-header class="with-elements">
          <div class="card-title-elements" v-if="auth.can['licenses_c']">
            <b-btn :to="{name:'system-licenses-create'}" variant="primary">Crear Licencia</b-btn>
            <b-btn v-if="auth.can['licenses_r']" variant="primary" @click="exportData()" v-b-tooltip.top title="Exportar"><i class="fas fa-download"></i></b-btn>
            <b-btn :to="{name:'system-licenses-report'}" variant="primary">Reportes</b-btn>
            <b-btn v-if="auth.can['licenses_c'] || auth.can['licenses_u']" :to="{name:'system-licenses-reasignacion-index'}" variant="primary">Reasignar licencias</b-btn>
          </div>
        </b-card-header>
        <b-card-body>
             <vue-table
                configName="system-licenses"
                v-if="auth.can['licenses_r']"
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
  name: 'licenses',
  metaInfo: {
    title: 'Licencias'
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
      axios.post('/system/license/export', this.filters)
      .then(response => {
        Alerts.warning('Información', 'Se inicio la exportación, se le notificara a su correo electronico cuando finalice el proceso.');
      }).catch(error => {
        Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
      });
    }
  }
}
</script>
