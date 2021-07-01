<template>
  <div>
    <header-module
      title="MATRIZ DE RIESGOS"
      subtitle="CREAR MATRIZ"
      url="industrialsecure-riskmatrix"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <industrial-secure-risk-matrix-form
                url="/industrialSecurity/risksMatrix"
                method="POST"
                :action-plan-states="actionPlanStates"
                :evaluation-controls="evaluationControls"
                :impacts-description="impactsDescription"
                :controls-decrease="controlsDecrease"
                :nature="nature"
                :coverage="coverage"
                :documentation="documentation"
                :mitigation="mitigation"
                :cancel-url="{ name: 'industrialsecure-riskmatrix'}"
                :si-no="siNo"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import IndustrialSecureRiskMatrixForm from '@/components/IndustrialSecure/RiskMatrix/FormRiskMatrixComponent.vue';
//import Alerts from '@/utils/Alerts.js';
import GlobalMethods from '@/utils/GlobalMethods.js';

export default {
  name: 'industrialsecure-riskmatrix-create',
  metaInfo: {
    title: 'Matriz de Riesgos - Crear'
  },
  components:{
    IndustrialSecureRiskMatrixForm
  },
  data(){
    return {
      siNo: [],
      actionPlanStates: [],
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
            Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
            this.$router.go(-1);
        });
    },
  }
}
</script>
