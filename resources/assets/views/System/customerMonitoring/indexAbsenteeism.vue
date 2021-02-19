<template>
  <div>
    <header-module
      title="SISTEMA"
      subtitle="MONITOREO DE CLIENTES - AUSENTISMO"
      url="system-customermonitoring"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
             <vue-table
                configName="system-customermonitoring-absenteeism"
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
  name: 'customermonitoring-absenteeism',
  metaInfo: {
    title: 'Monitoreo de clientes- Ausentismo'
  }
}
</script>
