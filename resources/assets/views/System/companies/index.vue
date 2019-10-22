<template>
  <div>
    <header-module
      title="SISTEMA"
      subtitle="COMPAÑIAS"
      url="system"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-header class="with-elements">
          <div class="card-title-elements" v-if="auth.can['companies_c']">
            <b-btn :to="{name:'system-companies-create'}" variant="primary">Crear Compañia</b-btn>
          </div>
        </b-card-header>
        <b-card-body>
             <vue-table
                configName="system-companies"
                v-if="auth.can['companies_r']"
                ></vue-table>
        </b-card-body>
    </b-card>
    </div>
  </div>
</template>

<script>
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'companies',
  metaInfo: {
    title: 'Compañias'
  }
}
</script>
