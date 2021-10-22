<template>
  <div>
    <header-module
        title="CONTRATISTAS"
        subtitle="EDITAR INFORME MENSUAL REALIZADO"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <form-inform-contract-component
                :url="`/legalAspects/informContract/${this.$route.params.id}`"
                method="PUT"
                :inform="data"
                :is-edit="true"
                :cancel-url="{ name: 'legalaspects-informs-contracts'}"
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
  name: 'legalaspects-informs-contratcs-edit',
  metaInfo: {
    title: 'Informes Mensuales Realizados - Editar'
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

    axios.get(`/legalAspects/informContract/${this.$route.params.id}`)
    .then(response => {
        this.data = response.data.data;
    })
    .catch(error => {
        Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
        this.$router.go(-1);
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
            this.$router.go(-1);
        });
    },
  }
}
</script>