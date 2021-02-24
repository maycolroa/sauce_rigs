<template>
  <div>
    <header-module
      title="SISTEMA"
      subtitle="MONITOREO DE CLIENTES - CONTRATISTAS"
      url="system-customermonitoring"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
             <vue-table
                configName="system-customermonitoring-contract"
                v-if="auth.can['customerMonitoring_r']"
                ></vue-table>
        </b-card-body>
    </b-card>
    </div>
  </div>
</template>

<script>
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'customermonitoring-contract',
  metaInfo: {
    title: 'Monitoreo de clientes- Contratistas'
  }
}
</script>
