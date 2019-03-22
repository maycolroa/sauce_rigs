<template>
  <div>
    <h4 class="font-weight-bold mb-4">
      Empleados
    </h4>


    <div class="col-md">
      <b-card no-body>
        <b-card-header class="with-elements">
          <div class="card-title-elements" v-if="auth.can['employees_c']">
            <b-btn :to="{name:'administrative-employees-create'}" variant="primary">Crear Empleado</b-btn>
          </div>
        </b-card-header>
        <b-card-body>
             <vue-table
                configName="administrative-employees"
                v-if="auth.can['employees_r']"
                ></vue-table>
        </b-card-body>
    </b-card>
    </div>
  </div>
</template>

<script>
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'employees',
  metaInfo: {
    title: 'Empleados'
  }
}
</script>
