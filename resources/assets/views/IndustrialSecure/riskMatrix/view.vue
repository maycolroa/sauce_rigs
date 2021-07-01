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
                :si-no="siNo"
                :evaluation-controls="evaluationControls"
                :impacts-description="impactsDescription"
                :controls-decrease="controlsDecrease"
                :nature="nature"
                :coverage="coverage"
                :documentation="documentation"
                :mitigation="mitigation"/>
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
      evaluationControls: {},
      impactsDescription: {},
      controlsDecrease: [],
      nature: [],
      coverage: [],
      documentation: [],
      mitigation : []
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
    this.fetchSelect('controlsDecrease', '/selects/rmControlsDecrease')
    this.fetchSelect('nature', '/selects/rmNature')
    this.fetchSelect('coverage', '/selects/rmCoverage')
    this.fetchSelect('documentation', '/selects/rmDocumentation')
    this.fetchSelect('evaluationControls', '/industrialSecurity/risksMatrix/getEvaluationControls')
    this.fetchSelect('impactsDescription', '/industrialSecurity/risksMatrix/getImpacts')
    this.fetchSelect('mitigation', '/industrialSecurity/risksMatrix/getMitigation')
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