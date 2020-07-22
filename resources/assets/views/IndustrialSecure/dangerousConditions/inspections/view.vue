<template>
  <div>
    <header-module
      title="CONDICIONES PELIGROSAS"
      subtitle="VER INSPECCIÓN"
      url="dangerousconditions-inspections"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <form-inspection
                :inspection="data"
                :view-only="true"
                :cancel-url="{ name: 'dangerousconditions-inspections'}"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>
 
<script>
import FormInspection from '@/components/IndustrialSecure/DangerousConditions/Inspections/FormInspectionComponent.vue';
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'dangerousconditions-inspections-view',
  metaInfo: {
    title: 'Inspecciones - Ver'
  },
  components:{
    FormInspection
  },
  data () {
    return {
      data: [],
    }
  },
  created(){
    axios.get(`/industrialSecurity/dangerousConditions/inspection/${this.$route.params.id}`)
    .then(response => {
        this.data = response.data.data;
    })
    .catch(error => {
        Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
    });
  },
}
</script>