<template>
  <div>
    <header-module
      title="INSPECCIONES"
      subtitle="VISUALIZAR AYUDAS"
      url="industrialsecure-dangerousconditions"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
             <vue-table
                configName="inspections-helpers"
                ></vue-table>
        </b-card-body>
    </b-card>
    </div>
  </div>
</template>

<script>
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'dangerousconditions-inspections-customHelpers',
  metaInfo: {
    title: 'Ayudas'
  },
  data () {
    return {}
  },
  methods: {}
}
</script>
