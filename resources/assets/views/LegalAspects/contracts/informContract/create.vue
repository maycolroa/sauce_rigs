<template>
  <div>
    <header-module
        title="CONTRATISTAS"
        subtitle="EVALUAR"
        url="legalaspects-informs"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <form-inform-contract-component
                :url="`/legalAspects/informContract`"
                method="POST"
                :inform="data"
                :cancel-url="{ name: 'legalaspects-informs'}"
                :action-plan-states="actionPlanStates"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>
 
<script>
import FormInformContractComponent from '@/components/LegalAspects/Contracts/MonthInformsContracts/FormInformContractComponent.vue';
import Alerts from '@/utils/Alerts.js';
import GlobalMethods from '@/utils/GlobalMethods.js';

export default {
  name: 'legalaspects-informs-contratc-create',
  metaInfo: {
    title: 'Informes Mensuales - Evaluar'
  },
  components:{
    FormInformContractComponent
  },
  data () {
    return {
      data: [],
      actionPlanStates: []
    }
  },
  created(){

    axios.get(`/legalAspects/informContract/getData/${this.$route.params.id}`)
    .then(response => {
        this.data = response.data.data;
    })
    .catch(error => {
        Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
    });

    this.fetchSelect('actionPlanStates', '/selects/actionPlanStates')
  },
  methods: {
    returnPage() {
      this.$router.go(-1);
    },
    fetchSelect(key, url)
    {
        GlobalMethods.getDataMultiselect(url)
        .then(response => {
            this[key] = response;
        })
        .catch(error => {
            Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
        });
    },
  }
}
</script>