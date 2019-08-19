<template>
  <div>
    <h4 class="font-weight-bold mb-4">
       <span class="text-muted font-weight-light">Restricciones /</span> Crear
    </h4>


    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <form-restriction
                url="/biologicalmonitoring/reinstatements/restriction"
                method="POST"
                :cancel-url="{ name: 'reinstatements-restrictions'}"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import FormRestriction from '@/components/PreventiveOccupationalMedicine/Reinstatements/Restrictions/FormRestrictionComponent.vue';
import GlobalMethods from '@/utils/GlobalMethods.js';
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'reinstatements-restrictions-create',
  metaInfo: {
    title: 'Restricciones - Crear'
  },
  components:{
    FormRestriction
  }
}
</script>
