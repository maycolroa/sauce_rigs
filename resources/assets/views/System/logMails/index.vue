<template>
  <div>
    <h4 class="font-weight-bold mb-4">
      Supervisar Correos
    </h4>


    <div class="col-md">
      <b-card no-body>
        <b-card-body>
             <vue-table
                configName="system-logmails"
                v-if="auth.can['logMails_r']"
                ></vue-table>
        </b-card-body>
    </b-card>
    </div>
  </div>
</template>

<script>
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'logMails',
  metaInfo: {
    title: 'Supervisar Correos'
  }
}
</script>
