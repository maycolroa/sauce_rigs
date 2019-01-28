<template>
  <div>
    <h4 class="font-weight-bold mb-4">
       <span class="text-muted font-weight-light">Áreas /</span> Crear
    </h4>


    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <administrative-area-form 
                url="/administration/area"
                method="POST"
                regionals-data-url="/selects/regionals"
                headquarters-data-url="/selects/headquarters"
                :cancel-url="{ name: 'administrative-areas'}"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import AdministrativeAreaForm from '@/components/Administrative/Areas/FormAreaComponent.vue';
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'administrative-areas-create',
  metaInfo: {
    title: 'Áreas - Crear'
  },
  components:{
    AdministrativeAreaForm
  },
  data(){
    return {
    }
  }
}
</script>
