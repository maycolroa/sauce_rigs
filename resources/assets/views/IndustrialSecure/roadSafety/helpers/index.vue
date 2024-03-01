<template>
  <div>
    <header-module
      title="SEGURIDAD VIAL"
      subtitle="VISUALIZAR AYUDAS"
      url="industrialsecure-roadsafety"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
             <vue-table
                configName="roadsafety-helpers"
                ></vue-table>
        </b-card-body>
    </b-card>
    </div>
  </div>
</template>

<script>
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'industrialsecure-roadsafety-customHelpers',
  metaInfo: {
    title: 'Ayudas'
  },
  data () {
    return {}
  },
  methods: {}
}
</script>
