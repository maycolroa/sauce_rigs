<template>
  <div>
    <header-module
      title="MATRIZ DE RIESGOS"
      subtitle="VER MATRIZ"
      url="industrialsecure-riskmatrix"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
        <loading :display="!ready"/>
          <div v-if="ready">
            <industrial-secure-risk-matrix-form
                :riskMatrix="data"
                :action-plan-states="actionPlanStates"
                :view-only="true"
                :cancel-url="{ name: 'industrialsecure-riskmatrix'}"
                :si-no="siNo"/>
          </div>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>
 
<script>
import IndustrialSecureRiskMatrixForm from '@/components/IndustrialSecure/RiskMatrix/FormRiskMatrixComponent.vue';
//import Alerts from '@/utils/Alerts.js';
import GlobalMethods from '@/utils/GlobalMethods.js';
import Loading from "@/components/Inputs/Loading.vue";

export default {
  name: 'industrialsecure-riskmatrix-view',
  metaInfo: {
    title: 'Matriz de Riesgos - Ver'
  },
  components:{
    IndustrialSecureRiskMatrixForm,
    Loading
  },
  data () {
    return {
      siNo: [],
      actionPlanStates: [],
      data: [],     
      ready: false,
    }
  },
  created(){
    
    
    axios.get(`/industrialSecurity/risksMatrix/${this.$route.params.id}`)
    .then(response => {
        this.data = response.data.data;
        this.ready = true  
    })
    .catch(error => {
    });
    this.fetchSelect('siNo', '/radios/siNo')
    this.fetchSelect('actionPlanStates', '/selects/actionPlanStates')
  },
  methods: {
    fetchSelect(key, url)
    {
        GlobalMethods.getDataMultiselect(url)
        .then(response => {
            this[key] = response;
        })
        .catch(error => {
        });
    },
  }
}
</script>