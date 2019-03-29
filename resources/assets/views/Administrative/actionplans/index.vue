<template>
  <div>
    <h4 class="font-weight-bold mb-4">
      Planes de Acción
    </h4>


    <div class="col-md">
      <b-card no-body>
        <b-card-body>
             <vue-table
                configName="administrative-actionplans"
                v-if="auth.can['actionPlans_r']"
                ></vue-table>
        </b-card-body>
    </b-card>
    </div>
  </div>
</template>

<script>
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'actionplans',
  metaInfo: {
    title: 'Planes de Acción'
  }
}
</script>
