<template>
  <div>
    <header-module
      title="SISTEMA"
      subtitle="USUARIOS - COMPAÑIAS"
      url="system"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-header class="with-elements">
          <div class="card-title-elements">
            <b-btn v-if="auth.can['usersCompanies_r']" variant="primary" @click="exportUsers()" v-b-tooltip.top title="Exportar"><i class="fas fa-download"></i></b-btn>
          </div>
        </b-card-header>
        <b-card-body>
             <vue-table
                configName="system-users-companies"
                @filtersUpdate="setFilters"
                v-if="auth.can['usersCompanies_r']"
                ></vue-table>
        </b-card-body>
    </b-card>
    </div>
  </div>
</template>

<script>
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'users-companies',
  metaInfo: {
    title: 'Usuarios - Compañias'
  },
  methods: {
    setFilters(value)
    {
      this.filters = value
    },
    exportUsers(){
      axios.post('/system/usersCompanies/export', this.filters)
      .then(response => {
        Alerts.warning('Información', 'Se inicio la exportación, se le notificara a su correo electronico cuando finalice el proceso.');
      }).catch(error => {
        Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
      });
    }
  }
}
</script>
