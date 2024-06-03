<template>
  <div>
    <header-module
      title="SISTEMA"
      subtitle="ADMINISTRAR EPS"
      url="system"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-header class="with-elements">
          <div class="card-title-elements">
            <b-btn :to="{name:'system-eps-create'}" variant="primary">Crear EPS</b-btn>
          </div>
        </b-card-header>
        <b-card-body>
             <vue-table
                configName="system-eps"
                ></vue-table>
        </b-card-body>
    </b-card>
    </div>
  </div>
</template>

<script>
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'eps',
  metaInfo: {
    title: 'EPS'
  },
  data () {
    return {}
  },
  methods: {}
}
</script>
