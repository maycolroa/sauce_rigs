<template>
  <div>
    <header-module
      title="SISTEMA"
      subtitle="USUARIOS RECEPTORES"
      url="system-newslettersend"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
             <vue-table
                configName="system-newslettersend-opens"
                v-if="auth.can['newsletterSend_r']"
                :modelId="`${this.$route.params.id}`"
                ></vue-table>
        </b-card-body>
    </b-card>
    </div>
  </div>
</template>

<script>
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'newslettersend-report-opens',
  metaInfo: {
    title: 'Usuarios receptores'
  }
}
</script>
