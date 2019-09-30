<template>
  <div>
    <h4 class="font-weight-bold mb-4">
       <span class="text-muted font-weight-light">Inspecciones /</span> Calificaciones Ver
    </h4>

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <form-inspection-qualification
                :url="`/industrialSecurity/dangerousConditions/inspection/qualification/${this.$route.params.id}`"
                method="PUT"
                :qualification="data"
                :action-plan-states="actionPlanStates"
                :is-edit="true"
                :cancel-url="{ name: 'dangerousconditions-inspections-qualification'}"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import FormInspectionQualification from '@/components/IndustrialSecure/DangerousConditions/Inspections/FormInspectionQualificationComponent.vue';
import Alerts from '@/utils/Alerts.js';
import GlobalMethods from '@/utils/GlobalMethods.js';

export default {
  name: 'dangerousconditions-inspections-qualification-view',
  metaInfo: {
    title: 'Inspecciones - Calificaciones Ver'
  },
  components:{
    FormInspectionQualification
  },
  data () {
    return {
      data: [],
      actionPlanStates: []
    }
  },
  created(){
    this.fetchSelect('actionPlanStates', '/selects/actionPlanStates')
    
    axios.get(`/industrialSecurity/dangerousConditions/inspection/qualification/${this.$route.params.id}`)
    .then(response => {
        this.data = response.data.data;
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