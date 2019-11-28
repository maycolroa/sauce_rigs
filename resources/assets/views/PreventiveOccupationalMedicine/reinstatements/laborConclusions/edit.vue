<template>
  <div>
    <header-module
      title="REINCORPORACIONES"
      subtitle="EDITAR CONCLUSIÓN LABORAL"
      url="reinstatements-labor-conclusions"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <form-labor-conclusion-component
                :url="`/biologicalmonitoring/reinstatements/laborConclusion/${this.$route.params.id}`"
                method="PUT"
                :laborConclusion="data"
                :is-edit="true"                
                :cancel-url="{ name: 'reinstatements-labor-conclusions'}"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import FormLaborConclusionComponent from '@/components/PreventiveOccupationalMedicine/Reinstatements/LaborConclusions/FormLaborConclusionComponent.vue';
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'reinstatements-labor-conclusions-edit',
  metaInfo: {
    title: 'Conclusión laboral - Editar'
  },
  components:{
    FormLaborConclusionComponent
  },
  data () {
    return {
      data: [],
    }
  },
  created(){
    axios.get(`/biologicalmonitoring/reinstatements/laborConclusion/${this.$route.params.id}`)
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