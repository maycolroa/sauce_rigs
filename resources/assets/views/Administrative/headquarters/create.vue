<template>
  <div>
    <h4 class="font-weight-bold mb-4">
       <span class="text-muted font-weight-light">Sedes /</span> Crear
    </h4>


    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <administrative-headquarter-form 
                url="/administration/headquarter"
                method="POST"
                regionals-data-url="/selects/regionals"
                :cancel-url="{ name: 'administrative-headquarters'}"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import AdministrativeHeadquarterForm from '@/components/Administrative/Headquarters/FormHeadquarterComponent.vue';
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'administrative-positions-create',
  metaInfo: {
    title: 'Sedes - Crear'
  },
  components:{
    AdministrativeHeadquarterForm
  },
  data(){
    return {
    }
  }
}
</script>
