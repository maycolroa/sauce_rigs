<template>
  <div>
    <header-module
      title="CONTRATISTAS"
      subtitle="ADMINISTRAR EMPLEADOS"
      url="legalaspects-contractor"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
             <vue-table
                configName="legalaspects-contracts-employees-view-contractor"
                :customColumnsName="true" 
                :modelId="this.$route.params.id"
                ></vue-table>
        </b-card-body>      
    </b-card>
    </div>
  </div>
</template>

<script>
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'employees',
  metaInfo: {
    title: 'Empleados'
  }
}
</script>
