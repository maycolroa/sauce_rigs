<template>
  <div>
    <header-module
      title="REINCORPORACIONES"
      subtitle="EDITAR CONCLUSIÓN MÉDICA"
      url="reinstatements-medical-conclusions"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <form-medical-conclusion-component
                :url="`/biologicalmonitoring/reinstatements/medicalConclusion/${this.$route.params.id}`"
                method="PUT"
                :medicalConclusion="data"
                :is-edit="true"                
                :cancel-url="{ name: 'reinstatements-medical-conclusions'}"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import FormMedicalConclusionComponent from '@/components/PreventiveOccupationalMedicine/Reinstatements/MedicalConclusions/FormMedicalConclusionComponent.vue';
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'reinstatements-medical-conclusions-edit',
  metaInfo: {
    title: 'Conclusión médica - Editar'
  },
  components:{
    FormMedicalConclusionComponent
  },
  data () {
    return {
      data: [],
    }
  },
  created(){
    axios.get(`/biologicalmonitoring/reinstatements/medicalConclusion/${this.$route.params.id}`)
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