<template>
  <div>
    <header-module
      title="SISTEMA"
      subtitle="GRUṔO DE COMPAÑIAS"
      url="system"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-header class="with-elements">
          <div class="card-title-elements" v-if="auth.can['companies_c']">
            <b-btn :to="{name:'system-companygroup-create'}" variant="primary">Crear Grupo</b-btn>
          </div>
        </b-card-header>
        <b-card-body>
             <vue-table
                configName="system-companygroup"
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
  name: 'companygroup',
  metaInfo: {
    title: 'Grupo de Compañias'
  }
}
</script>
