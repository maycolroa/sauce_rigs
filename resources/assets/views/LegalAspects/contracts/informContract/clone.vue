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
  name: 'legalaspects-evaluations-contratc-create',
  metaInfo: {
    title: 'Evaluaciones - Evaluar'
  },
  components:{
    FormInformContractComponent
  },
  data(){
    return {
      data: [],
      actionPlanStates: []
    }
  },
  created(){
     axios.get(`/legalAspects/informContract/${this.$route.params.id}`)
    .then(response => {
        this.data = response.data.data;
        console.log(this.data);
        delete this.data.id
        this.data.year = ''
        this.data.month = ''
        this.data.inform.themes.map((theme) => {
            theme.items.map((item) => {
              item.file = [];
              item.actionPlan.activities.map((plan) => {
                plan.id = ''

                return plan;
              });
              item.files_pdf = [];
              item.observations.map((obs) => {
                delete obs.id
                delete obs.evaluation_id

                return obs
              });
              return item;
            });
          return theme;
        });
    })
    .catch(error => {
      console.log(error)
        Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
        this.$router.go(-1);
    });
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
