<template>
  <div>
    <header-module
      title="SISTEMA"
      subtitle="ADMINISTRAR AYUDAS"
      url="system"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-header class="with-elements">
          <div class="card-title-elements" v-if="auth.can['helpers_c']">
            <b-btn :to="{name:'system-helpers-create'}" variant="primary">Crear Ayuda</b-btn>
          </div>
        </b-card-header>
        <b-card-body>
             <vue-table
                configName="system-helpers"
                v-if="auth.can['helpers_r']"
                ></vue-table>
        </b-card-body>
    </b-card>
    </div>
  </div>
</template>

<script>
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'helpers',
  metaInfo: {
    title: 'Ayudas'
  },
  data () {
    return {}
  },
  methods: {}
}
</script>
