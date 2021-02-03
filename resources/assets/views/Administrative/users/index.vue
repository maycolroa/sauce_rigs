<template>
  <div>
    <header-module
      title="ADMINISTRATIVO"
      subtitle="ADMINISTRAR USUARIOS"
      url="administrative"
    />


    <div class="col-md">
      <b-card no-body>
        <b-card-header class="with-elements">
          <div class="card-title-elements" v-if="auth.can['users_c']">
            <b-btn :to="{name:'administrative-users-create'}" variant="primary">Crear Usuario</b-btn>
          </div>
          <div class="card-title-elements" v-if="auth.can['users_add_company']">
            <b-btn :to="{name:'administrative-users-add-other-company'}" variant="primary">Agregar usuarios de otra compañia</b-btn>
          </div>
          <div class="card-title-elements" v-if="auth.can['users_c']">
            <b-btn variant="primary" href="/templates/usersimport" target="blank" v-b-tooltip.top title="Generar Plantilla"><i class="fas fa-file-alt"></i></b-btn>
          </div>
          <div class="card-title-elements" v-if="auth.can['users_c']">
            <b-btn :to="{name:'administrative-users-import'}" variant="primary">Importar</b-btn>
          </div>
          <!-- <div class="card-title-elements ml-md-auto" v-if="auth.can['users_r']">
            <b-dd variant="default" :right="isRTL">
            <template slot="button-content">
              <span class='fas fa-cogs'></span>
            </template>
            <b-dd-item @click="exportUsers()"><i class="fas fa-download"></i> &nbsp;Exportar</b-dd-item>
          </b-dd>
          </div>-->
        </b-card-header>
        <b-card-body>
             <vue-table
                configName="administrative-users"
                :customColumnsName="true"
                v-if="auth.can['users_r']"
                ></vue-table>
        </b-card-body>
    </b-card>
    </div>
  </div>
</template>

<script>
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'users',
  metaInfo: {
    title: 'Usuarios'
  },
  methods: {
    exportUsers(){
      axios.post('/administration/users/export')
      .then(response => {
        Alerts.warning('Información', 'Se inicio la exportación, se le notificara a su correo electronico cuando finalice el proceso.');
      }).catch(error => {
        Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
      });
    }
  }
}
</script>
