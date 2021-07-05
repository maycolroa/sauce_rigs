<template>
  <div>
    <header-module
      title="MATRIZ DE RIESGOS"
      subtitle="MACROPROCESOS"
      url="industrialsecure-riskmatrix"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
          <template>
              <vue-table
              configName="industrialsecure-riskmatrix-macroprocesses"
              v-if="auth.can['riskMatrix_r']" 
              ></vue-table>
          </template>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>
<script>
import Alerts from '@/utils/Alerts.js';
import Loading from "@/components/Inputs/Loading.vue";

export default {
  name: 'riskmatrix',
  metaInfo: {
    title: 'Macroprocesos'
  },
  components:{
    Loading
  }
}
</script>
