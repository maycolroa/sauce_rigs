<template>
  <div>
    <header-module
      title="SISTEMA"
      subtitle="REASIGNAR LICENCIAS"
      url="system"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
             <vue-table
                configName="system-licenses-reasignacion"
                v-if="auth.can['licenses_r']"
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
  name: 'licenses',
  metaInfo: {
    title: 'Licencias'
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
