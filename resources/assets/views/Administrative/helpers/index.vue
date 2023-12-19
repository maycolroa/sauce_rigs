<template>
  <div>
    <header-module
      title="ADMINISTRATIVO"
      subtitle="VISUALIZAR AYUDAS"
      url="administrative"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
             <vue-table
                configName="administrative-helpers"
                ></vue-table>
        </b-card-body>
    </b-card>
    </div>
  </div>
</template>

<script>
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'administrative-customHelpers',
  metaInfo: {
    title: 'Ayudas'
  },
  data () {
    return {}
  },
  methods: {}
}
</script>
