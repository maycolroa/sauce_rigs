<template>
  <div>
    <header-module
      title="REINCORPORACIONES"
      subtitle="CAMBIAR ESTADO"
      url="reinstatements-checks"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body style="height: 400px">
            <form-check-swith
              :url="`/biologicalmonitoring/reinstatements/check/switchStatus/${this.$route.params.id}`"
              :check="data"
            />
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import FormCheckSwith from '@/components/PreventiveOccupationalMedicine/Reinstatements/Checks/FormCheckSwithComponent.vue';
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'reinstatements-disease-origin-create',
  metaInfo() {
    return {
      title: 'Cambiar Estado'
    }
  },
  components:{
    FormCheckSwith
  },
  data () {
    return {
      data: []
    }
  },
  created(){
    axios.get(`/biologicalmonitoring/reinstatements/check/${this.$route.params.id}`)
      .then(response => {
          this.data = response.data.data;
      })
      .catch(error => {
          Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
          this.$router.go(-1);
      });
  }
}
</script>
