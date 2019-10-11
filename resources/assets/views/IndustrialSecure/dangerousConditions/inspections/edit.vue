<template>
  <div>
    <header-module
      title="CONDICIONES PELIGROSAS"
      subtitle="EDITAR INSPECCIÓN"
      url="dangerousconditions-inspections"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <form-inspection
                :url="`/industrialSecurity/dangerousConditions/inspection/${this.$route.params.id}`"
                method="PUT"
                regionals-data-url="/selects/regionals"
                headquarters-data-url="/selects/headquarters"
                areas-data-url="/selects/areas"
                processes-data-url="/selects/processes"
                :inspection="data"
                :is-edit="true"
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
  name: 'dangerousconditions-inspections-edit',
  metaInfo: {
    title: 'Inspecciones - Editar'
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
        this.$router.go(-1);
    });
  },
}
</script>