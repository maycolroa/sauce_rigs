<template>
  <div>
    <header-module
      title="MATRIZ DE PELIGROS"
      subtitle="ADMINISTRAR MATRICES"
      url="industrialsecure-dangermatrix"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
             <vue-table
                configName="industrialsecure-dangermatrix-log-qualifications"
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
  name: 'dangermatrix',
  metaInfo: {
    title: 'Log Calificaciones'
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
    },
  }
}
</script>
