<template>
  <div>
    <h4 class="font-weight-bold mb-4">
      Roles
    </h4>


    <div class="col-md">
      <b-card no-body>
        <b-card-header class="with-elements">
          <div class="card-title-elements">
            <b-btn :to="{name:'administrative-roles-create'}" variant="primary">Crear Rol</b-btn>
          </div>
        </b-card-header>
        <b-card-body>
             <vue-table
                configName="administrative-roles"
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
