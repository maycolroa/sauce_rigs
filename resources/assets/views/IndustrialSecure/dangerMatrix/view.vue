<template>
  <div>
    <header-module
      title="MATRIZ DE PELIGROS"
      subtitle="VER MATRIZ"
      url="industrialsecure-dangermatrix"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
        <loading :display="!ready"/>
          <div v-if="ready">
            <industrial-secure-danger-matrix-form
                :dangerMatrix="data"
                :view-only="true"
                :cancel-url="{ name: 'industrialsecure-dangermatrix'}"
                :type-activities="typeActivities"
                :danger-generated="dangerGenerated"
                :si-no="siNo"
                :qualifications="qualifications"
                :action-plan-states="actionPlanStates"
                userDataUrl="/selects/users"
                :configuration="configuration"
                :fields="fields"/>
          </div>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>
 
<script>
import IndustrialSecureDangerMatrixForm from '@/components/IndustrialSecure/DangerMatrix/FormDangerMatrixComponent.vue';
import Alerts from '@/utils/Alerts.js';
import GlobalMethods from '@/utils/GlobalMethods.js';
import Loading from "@/components/Inputs/Loading.vue";

export default {
  name: 'industrialsecure-dangermatrix-view',
  metaInfo: {
    title: 'Matriz de Peligros - Ver'
  },
  components:{
    IndustrialSecureDangerMatrixForm,
    Loading
  },
  data () {
    return {
      typeActivities: [],
      dangerGenerated: [],
      siNo: [],
      qualifications: [],
      actionPlanStates: [],
      data: [],
      configuration: [],      
      ready: false,
      fields: {}
    }
  },
  created(){
    
    
    axios.get(`/industrialSecurity/dangersMatrix/${this.$route.params.id}`)
    .then(response => {
        this.data = response.data.data;
        this.fields = response.data.data.add_fields;

        axios.get('/administration/configuration/view')
        .then(response2 => {
            this.configuration = response2.data.data;
            this.ready = true
        })
        .catch(error => {
        });
    })
    .catch(error => {
    });

    this.fetchSelect('typeActivities', '/radios/dmTypeActivities')
    this.fetchSelect('dangerGenerated', '/selects/dmGeneratedDangers')
    this.fetchSelect('siNo', '/radios/siNo')
    this.fetchSelect('qualifications', '/industrialSecurity/dangersMatrix/getQualificationsComponent')
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