<template>
  <div>
    <header-module
      title="MATRIZ DE RIESGOS"
      subtitle="EDITAR MATRIZ"
      url="industrialsecure-riskmatrix"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
          <loading :display="!ready"/>
          <div v-if="ready">
            <industrial-secure-risk-matrix-form
                :url="`/industrialSecurity/risksMatrix/${this.$route.params.id}`"
                method="PUT"
                :riskMatrix="data"
                :is-edit="true"
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
  name: 'industrialsecure-riskmatrix-edit',
  metaInfo: {
    title: 'Matriz de Riesgos - Editar'
  },
  components:{
    IndustrialSecureRiskMatrixForm,
    Loading
  },
  data () {
    return {
      siNo: [],
      data: [],
      ready: false,
    }
  },
  created(){
    axios.get(`/industrialSecurity/risksMatrix/${this.$route.params.id}`)
    .then(response => {
        this.data = response.data.data;
        this.ready = true  
        //this.fields = response.data.data.add_fields;

        /*axios.get('/administration/configuration/view')
        .then(response2 => {
            this.configuration = response2.data.data;
            this.ready = true
        })
        .catch(error => {
        });*/
    })
    .catch(error => {
    });

    /*this.fetchSelect('typeActivities', '/radios/dmTypeActivities')
    this.fetchSelect('dangerGenerated', '/selects/dmGeneratedDangers')*/
    this.fetchSelect('siNo', '/radios/siNo')
    /*this.fetchSelect('qualifications', '/industrialSecurity/dangersMatrix/getQualificationsComponent')
    this.fetchSelect('actionPlanStates', '/selects/actionPlanStates')*/
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