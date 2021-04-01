<template>
  <div>
    <header-module
      title="LISTA DE ESTÁNDARES MÍNIMOS"
      subtitle="ADMINISTRAR CALIFICACIONES"
      :url="(this.$route.params.id ? 'legalaspects-contractor' : 'legalaspects-contracts')"
    />


    <div class="col-md">
      <b-card no-body>
        <b-card-header class="with-elements">
          <div class="card-title-elements" v-if="auth.can['contracts_list_standards_qualification_c']">
            <b-btn :to="{name:'legalaspects-list-check-qualification-create'}" variant="primary">Crear Calificación</b-btn>
          </div>
        </b-card-header>
        <b-card-body>
             <vue-table
                configName="legalaspects-contractor-list-check-qualification"
                v-if="auth.can['contracts_list_standards_qualification_r']"
                ></vue-table>
        </b-card-body>
    </b-card>
    </div>
  </div>
</template>

<script>
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'list-check-qualification',
  metaInfo() {
    return {
      title: 'Calificaciones'
    }
  }
}
</script>
