<template>
  <div>
    <header-module
      title="INSPECCIONES"
      subtitle="VER INSPECCIÓN PLANEADA CALIFICADA"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <form-inspection-qualification-personalized
                v-if="type"
                :url="`/industrialSecurity/roadsafety/inspection/qualification/${this.$route.params.id}`"
                method="PUT"
                :qualification="data"
                :action-plan-states="actionPlanStates"
                :is-edit="true"
                :cancel-url="{ name: 'roadsafety-inspections-qualification'}"/>
            <form-inspection-qualification
                v-else
                :url="`/industrialSecurity/roadsafety/inspection/qualification/${this.$route.params.id}`"
                method="PUT"
                :qualification="data"
                :action-plan-states="actionPlanStates"
                :is-edit="true"
                :cancel-url="{ name: 'roadsafety-inspections-qualification'}"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import FormInspectionQualification from '@/components/IndustrialSecure/RoadSafety/Inspections/Qualifications/FormInspectionQualificationComponent.vue';
import FormInspectionQualificationPersonalized from '@/components/IndustrialSecure/RoadSafety/Inspections/Qualifications/FormInspectionQualificationPersonalizedComponent.vue';
import Alerts from '@/utils/Alerts.js';
import GlobalMethods from '@/utils/GlobalMethods.js';

export default {
  name: 'dangerousconditions-inspections-qualification-view',
  metaInfo: {
    title: 'Inspecciones Planeadas - Calificaciones Ver'
  },
  components:{
    FormInspectionQualification,
    FormInspectionQualificationPersonalized
  },
  data () {
    return {
      data: [],
      actionPlanStates: [],
      type: false
    }
  },
  created(){
    this.fetchSelect('actionPlanStates', '/selects/actionPlanStates')
    
    axios.get(`/industrialSecurity/roadsafety/inspection/qualification/${this.$route.params.id}`)
    .then(response => {
        this.data = response.data.data;

        if (this.data.type == 3)
        {
          this.type = true
        }
    })
    .catch(error => {
        Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
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
        });
    },
  }
}
</script>