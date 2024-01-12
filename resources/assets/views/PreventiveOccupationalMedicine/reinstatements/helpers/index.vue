<template>
  <div>
    <header-module
      title="REINCORPORACIONES"
      subtitle="VISUALIZAR AYUDAS"
      url="preventiveoccupationalmedicine-reinstatements"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
             <vue-table
                configName="reinstatements-helpers"
                ></vue-table>
        </b-card-body>
    </b-card>
    </div>
  </div>
</template>

<script>
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'reinstatements-customHelpers',
  metaInfo: {
    title: 'Ayudas'
  },
  data () {
    return {}
  },
  methods: {}
}
</script>
