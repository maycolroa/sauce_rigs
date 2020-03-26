<template>
  <div>
    <header-module
      title="CONTRATISTAS"
      subtitle="ADMINISTRAR EMPLEADOS"
      :url="(this.$route.params.id ? 'legalaspects-contractor' : 'legalaspects-contracts')"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-header class="with-elements">
          <div class="card-title-elements" v-if="auth.can['contracts_employee_c']">
            <b-btn :to="{name:'legalaspects-contracts-employees-create'}" variant="primary">Crear Empleado</b-btn>
          </div>
        </b-card-header>
        <b-card-body>
             <vue-table
                configName="legalaspects-contracts-employees"
                v-if="auth.can['contracts_employee_r']"
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
