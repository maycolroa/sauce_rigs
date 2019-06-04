<template>
  <div>
    <h4 class="font-weight-bold mb-4">
       <span class="text-muted font-weight-light">Matriz de Peligros /</span> Ver
    </h4>

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
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
                :configuration="configuration"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>
 
<script>
import IndustrialSecureDangerMatrixForm from '@/components/IndustrialSecure/DangerMatrix/FormDangerMatrixComponent.vue';
import Alerts from '@/utils/Alerts.js';
import GlobalMethods from '@/utils/GlobalMethods.js';

export default {
  name: 'industrialsecure-dangermatrix-view',
  metaInfo: {
    title: 'Matriz de Peligros - Ver'
  },
  components:{
    IndustrialSecureDangerMatrixForm
  },
  data () {
    return {
      typeActivities: [],
      dangerGenerated: [],
      siNo: [],
      qualifications: [],
      actionPlanStates: [],
      data: [],
      configuration: []
    }
  },
  created(){
    axios.get('/administration/configuration/view')
    .then(response => {
        this.configuration = response.data.data;
    })
    .catch(error => {
        Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
        this.$router.go(-1);
    });
    
    axios.get(`/industrialSecurity/dangersMatrix/${this.$route.params.id}`)
    .then(response => {
        this.data = response.data.data;
    })
    .catch(error => {
        Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
        this.$router.go(-1);
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
            Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
            this.$router.go(-1);
        });
    },
  }
}
</script>