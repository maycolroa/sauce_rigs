<template>
  <div>
    <header-module
      title="MATRIZ LEGAL"
      subtitle="VER TEMA SST"
      url="legalaspects-lm-sstrisk"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <form-sst-risk-component
                :sst_risk="data"
                :view-only="true"
                :cancel-url="{ name: 'legalaspects-lm-sstrisk'}"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>
 
<script>
import FormSstRiskComponent from '@/components/LegalAspects/LegalMatrix/SstRisk/FormSstRiskComponent.vue';
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'legalaspects-lm-sstrisk-view',
  metaInfo: {
    title: 'Temas SST - Ver'
  },
  components:{
    FormSstRiskComponent
  },
  data () {
    return {
      data: [],
    }
  },
  created(){
    axios.get(`/legalAspects/legalMatrix/sstRisk/${this.$route.params.id}`)
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