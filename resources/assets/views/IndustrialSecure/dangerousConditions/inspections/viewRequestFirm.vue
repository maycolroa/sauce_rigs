<template>
  <div>
    <header-module
      title="INSPECCIONES PLANEADAS"
      subtitle="SOLICITUD FIRMA"
      url="dangerousconditions-inspections"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
             <vue-table
                configName="dangerousconditions-inspections-request-firm"
                :customColumnsName="true"
                :configNameFilterDefault="auth.can['ph_inspections_c'] ? 'dangerousconditions-inspections-request-firm-admin' : 'dangerousconditions-inspections-request-firm'"
                v-if="auth.can['ph_inspections_r']"
                @filtersUpdate="setFilters"
                ></vue-table>
        </b-card-body>
    </b-card>
    </div>
  </div>
</template>

<script>
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'inspections-request-firm',
  metaInfo: {
    title: 'Solicitud de firmas'
  },
  data () {
    return {
      filters: []
    }
  },
  methods: {
    setFilters(value)
    {
      this.filters = value
    }
  }
}
</script>
