<template>
  <div>
    <header-module
      title="SISTEMA"
      subtitle="MONITOREO DE CLIENTES - REINCORPORACIONES"
      url="system-customermonitoring"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
             <vue-table
                configName="system-customermonitoring-reinstatements"
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
  name: 'customermonitoring-reinstatements',
  metaInfo: {
    title: 'Monitoreo de clientes- Reincorporaciones'
  }
}
</script>
