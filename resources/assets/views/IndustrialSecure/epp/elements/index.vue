<template>
  <div>
    <header-module
      title="ELEMENTOS DE PROTECCIÓN PERSONAL"
      subtitle="ADMINISTRAR ELEMENTOS"
      url="industrialsecure-epp"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-header class="with-elements">
          <div class="card-title-elements" v-if="auth.can['elements_c']"> 
            <b-btn :to="{name:'industrialsecure-epps-elements-create'}" variant="primary">Crear Elemento</b-btn>
          </div>
        </b-card-header>
        <b-card-body>
             <vue-table
                configName="industrialsecure-epps-elements"
                v-if="auth.can['elements_r']"
                ></vue-table>
        </b-card-body>
    </b-card>
    </div>
  </div>
</template>

<script>
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'epps-elements',
  metaInfo: {
    title: 'Elementos'
  }
}
</script>
