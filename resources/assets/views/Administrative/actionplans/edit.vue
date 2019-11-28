<template>
  <div>
    <header-module
      title="ADMINISTRATIVO"
      subtitle="EDITAR PLAN DE ACCIÓN"
      url="administrative-actionplans"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <form-action-plans-component
                :url="`/administration/actionplan/${this.$route.params.id}`"
                method="PUT"
                :actionPlan="data"
                :cancel-url="{ name: 'administrative-actionplans'}"
                :action-plan-states="actionPlanStates"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import FormActionPlansComponent from '@/components/Administrative/ActionPlans/FormActionPlansComponent.vue';
import Alerts from '@/utils/Alerts.js';
import GlobalMethods from '@/utils/GlobalMethods.js';

export default {
  name: 'administrative-actionplans-edit',
  metaInfo: {
    title: 'Planes de acción - Editar'
  },
  components:{
    FormActionPlansComponent
  },
  data () {
    return {
      data: {
        actionPlan: {
          activities: []
        }
      },
      actionPlanStates: []
    }
  },
  created(){
    axios.get(`/administration/actionplan/${this.$route.params.id}`)
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