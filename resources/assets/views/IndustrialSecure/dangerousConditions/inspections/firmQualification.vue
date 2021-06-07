<template>
  <div>
    <header-module
      title="SOLICITUD FIRMA"
      subtitle="VER INSPECCIÓN PLANEADA CALIFICADA"
      url="dangerousconditions-inspections-request-firm"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <form-qualification-firm
                url="/industrialSecurity/dangerousConditions/inspection/requestFirm/saveFirm"
                method="POST"
                :qualification="data"
                :action-plan-states="actionPlanStates"
                :is-edit="true"
                :cancel-url="{ name: 'dangerousconditions-inspections-request-firm'}"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import FormQualificationFirm from '@/components/IndustrialSecure/DangerousConditions/Inspections/FormQualificationFirmComponent.vue';
import Alerts from '@/utils/Alerts.js';
import GlobalMethods from '@/utils/GlobalMethods.js';

export default {
  name: 'dangerousconditions-qualification-firm',
  metaInfo: {
    title: 'Inspecciones Planeadas - Solicitud Firma'
  },
  components:{
    FormQualificationFirm
  },
  data () {
    return {
      data: [],
      actionPlanStates: []
    }
  },
  created(){
    this.fetchSelect('actionPlanStates', '/selects/actionPlanStates')
    
    axios.post(`/industrialSecurity/dangerousConditions/inspection/requestFirm/view/${this.$route.params.id}`)
    .then(response => {
        this.data = response.data.data;
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