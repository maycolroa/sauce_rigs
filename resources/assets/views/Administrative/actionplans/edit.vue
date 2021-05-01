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
           <loading :display="!ready"/>
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
import Loading from "@/components/Inputs/Loading.vue";

export default {
  name: 'administrative-actionplans-edit',
  metaInfo: {
    title: 'Planes de acción - Editar'
  },
  components:{
    FormActionPlansComponent,
    Loading
  },
  data () {
    return {
      data: {
        actionPlan: {
          activities: []
        }
      },
      ready: false,
      actionPlanStates: []
    }
  },
  created(){
    axios.get(`/administration/actionplan/${this.$route.params.id}`)
    .then(response => {
        this.data = response.data.data;
        if (this.data.actionPlan.activities[0].table == 'sau_ct_item_qualification_contract')
        {
          this.fetchSelect('actionPlanStates', '/selects/actionPlanStates/all')
        }
        else 
        {
          this.fetchSelect('actionPlanStates', '/selects/actionPlanStates')
        }
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