<template>
  <div>
    <header-module
      title="SISTEMA"
      subtitle="ADMINISTRAR ARL"
      url="system"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-header class="with-elements">
          <div class="card-title-elements">
            <b-btn :to="{name:'system-arl-create'}" variant="primary">Crear ARL</b-btn>
          </div>
        </b-card-header>
        <b-card-body>
             <vue-table
                configName="system-arl"
                ></vue-table>
        </b-card-body>
    </b-card>
    </div>
  </div>
</template>

<script>
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'arl',
  metaInfo: {
    title: 'ARL'
  },
  data () {
    return {}
  },
  methods: {}
}
</script>
