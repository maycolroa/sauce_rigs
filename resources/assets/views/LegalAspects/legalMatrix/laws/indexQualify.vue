<template>
  <div>
    <h4 class="font-weight-bold mb-4">
      Calificar Normas
    </h4>


    <div class="col-md">
      <b-card no-body>
        <b-card-body>
             <vue-table
                v-if="auth.can['laws_qualify']"
                configName="legalaspects-lm-laws-qualify"
                :params="{ qualify : true }"
                ></vue-table>
        </b-card-body>
    </b-card>
    </div>
  </div>
</template>

<script>
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'legalaspects-lm-laws-qualify',
  metaInfo: {
    title: 'Calificar Normas'
  }
}
</script>
