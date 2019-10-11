<template>
  <div>
    <header-module
      title="ADMINNISTRATIVO"
      subtitle="ADMINISTRAR ROLES"
      url="administrative"
    />


    <div class="col-md">
      <b-card no-body>
        <b-card-header class="with-elements">
          <div class="card-title-elements" v-if="auth.can['roles_c']">
            <b-btn :to="{name:'administrative-roles-create'}" variant="primary">Crear Rol</b-btn>
          </div>
        </b-card-header>
        <b-card-body>
             <vue-table
                configName="administrative-roles"
                :customColumnsName="true"
                v-if="auth.can['roles_r']"
                ></vue-table>
        </b-card-body>
    </b-card>
    </div>
  </div>
</template>

<script>
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'roles',
  metaInfo: {
    title: 'Roles'
  }
}
</script>
