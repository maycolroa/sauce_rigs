<template>
  <div>
    <header-module
      title="CONTRATISTAS"
      subtitle="VER EMPLEADO"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <form-contract-employee-view
                :employee="data"
                :view-only="true"
                :sexs="sexs"
                :contract_id="contract_id"
                activitiesUrl="/selects/contracts/ctActivitiesContracts"
                afp-data-url="/selects/afp"
                :states="states"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>
 
<script>
import FormContractEmployeeView from '@/components/LegalAspects/Contracts/Employees/FormContractEmployeeViewContractComponent.vue';
import Alerts from '@/utils/Alerts.js';
import GlobalMethods from '@/utils/GlobalMethods.js';

export default {
  name: 'legalaspects-contracts-employees-view-contract-view',
  metaInfo: {
    title: 'Empleados - Ver'
  },
  components:{
    FormContractEmployeeView
  },
  data () {
    return {
      data: [],
      contract_id: '',
			sexs: [],
      states: []
    }
  },
  created(){
    axios.get(`/legalAspects/employeeContract/${this.$route.params.id}`)
    .then(response => {
        this.data = response.data.data;
        this.contract_id = this.data.contract_id
    })
    .catch(error => {
        Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
    });

    this.fetchSelect('states', '/selects/contracts/statesFile')
    this.fetchSelect('sexs', '/selects/sexs')
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