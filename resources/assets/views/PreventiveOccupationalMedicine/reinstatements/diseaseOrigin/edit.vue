<template>
  <div>
    <header-module
      title="REINCORPORACIONES"
      :subtitle="`EDITAR ${keywordCheck('disease_origin')}`"
      url="reinstatements-disease-origin"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <form-disease-origin-component
                :url="`/biologicalmonitoring/reinstatements/diseaseOrigin/${this.$route.params.id}`"
                method="PUT"
                :diseaseOrigin="data"
                :is-edit="true"                
                :cancel-url="{ name: 'reinstatements-disease-origin'}"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import FormDiseaseOriginComponent from '@/components/PreventiveOccupationalMedicine/Reinstatements/DiseaseOrigin/FormDiseaseOriginComponent.vue';
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'reinstatements-disease-origin-edit',
  metaInfo() {
    return {
        title: `${this.keywordCheck('disease_origin')} - Editar`
    }
  },
  components:{
    FormDiseaseOriginComponent
  },
  data () {
    return {
      data: [],
    }
  },
  created(){
    axios.get(`/biologicalmonitoring/reinstatements/diseaseOrigin/${this.$route.params.id}`)
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