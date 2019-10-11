<template>
  <div>
    <header-module
      title="MATRIZ DE PELIGROS"
      subtitle="EDITAR ACTIVIDAD"
      url="industrialsecure-activities"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <industrial-secure-activity-form
                :url="`/industrialSecurity/activity/${this.$route.params.id}`"
                method="PUT"
                :activity="data"
                :is-edit="true"
                :cancel-url="{ name: 'industrialsecure-activities'}"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import IndustrialSecureActivityForm from '@/components/IndustrialSecure/Activities/FormActivityComponent.vue';
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'industrialsecure-activities-edit',
  metaInfo: {
    title: 'Actividades - Editar'
  },
  components:{
    IndustrialSecureActivityForm
  },
  data () {
    return {
      data: [],
    }
  },
  created(){
    axios.get(`/industrialSecurity/activity/${this.$route.params.id}`)
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