<template>
  <div>
    <header-module
      title="REINCORPORACIONES"
      subtitle="EDITAR PROCEDENCIA DE RECOMENDACIONES"
      url="reinstatements-origin-advisor"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <form-origin-advisor-component
                :url="`/biologicalmonitoring/reinstatements/originAdvisor/${this.$route.params.id}`"
                method="PUT"
                :originAdvisor="data"
                :is-edit="true"                
                :cancel-url="{ name: 'reinstatements-origin-advisor'}"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import FormOriginAdvisorComponent from '@/components/PreventiveOccupationalMedicine/Reinstatements/OriginAdvisor/FormOriginAdvisorComponent.vue';
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'reinstatements-origin-advisor-edit',
  metaInfo: {
    title: 'Procedencia de recomendaciones - Editar'
  },
  components:{
    FormOriginAdvisorComponent
  },
  data () {
    return {
      data: [],
    }
  },
  created(){
    axios.get(`/biologicalmonitoring/reinstatements/originAdvisor/${this.$route.params.id}`)
    .then(response => {
        this.data = response.data.data;
    })
    .catch(error => {
        Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
        this.$router.go(-1);
    });
  },
}
</script>