<template>
  <div>
    <header-module
      title="MATRIZ DE PELIGROS"
      subtitle="CREAR MATRIZ"
      url="industrialsecure-dangermatrix"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <industrial-secure-danger-matrix-form
                url="/industrialSecurity/dangersMatrix"
                method="POST"
                :cancel-url="{ name: 'industrialsecure-dangermatrix'}"
                :type-activities="typeActivities"
                :danger-generated="dangerGenerated"
                :si-no="siNo"
                :qualifications="qualifications"
                :action-plan-states="actionPlanStates"
                userDataUrl="/selects/users"
                :configuration="configuration"
                :fields="fields"/>
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
  name: 'industrialsecure-dangermatrix-create',
  metaInfo: {
    title: 'Matriz de Peligros - Crear'
  },
  components:{
    IndustrialSecureDangerMatrixForm
  },
  data(){
    return {
      typeActivities: [],
      dangerGenerated: [],
      siNo: [],
      qualifications: [],
      actionPlanStates: [],
      configuration: [],
      fields: {}
    }
  },
  created(){
    this.fetchSelect('typeActivities', '/radios/dmTypeActivities')
    this.fetchSelect('dangerGenerated', '/selects/dmGeneratedDangers')
    this.fetchSelect('siNo', '/radios/siNo')
    this.fetchSelect('qualifications', '/industrialSecurity/dangersMatrix/getQualificationsComponent')
    this.fetchSelect('actionPlanStates', '/selects/actionPlanStates')

    axios.get('/administration/configuration/view')
    .then(response => {
        this.configuration = response.data.data;
    })
    .catch(error => {
        Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
        this.$router.go(-1);
    });

    axios.post("/industrialSecurity/dangersMatrix/getfieldsadd")
		.then(response => {
			this.fields = response.data.data;
      this.ready = true
		})
		.catch(error => {
			Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
			this.$router.go(-1);
    });
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
