<template>
  <div>
    <header-module
      title="EPP Y EQUIPOS"
      subtitle="VISUALIZAR AYUDAS"
      url="industrialsecure-epps"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
             <vue-table
                configName="epp-helpers"
                ></vue-table>
        </b-card-body>
    </b-card>
    </div>
  </div>
</template>

<script>
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'd+angermatrix-customHelpers',
  metaInfo: {
    title: 'Ayudas'
  },
  data () {
    return {}
  },
  methods: {}
}
</script>
