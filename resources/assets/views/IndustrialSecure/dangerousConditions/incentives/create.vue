<template>
  <div>
    <header-module
      title="INSPECCIONES"
      subtitle="ADMINISTRAR INCENTIVOS"
      url="industrialsecure-dangerousconditions"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <form-incentive
                :url="`/industrialSecurity/dangerousConditions/incentive`"
                method="POST"
                :incentive="data"
                :is-edit="true"
                :cancel-url="{ name: 'industrialsecure-dangerousconditions'}"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import FormIncentive from '@/components/IndustrialSecure/DangerousConditions/FormIncentiveComponent.vue';
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'dangerousConditions-incentive',
  metaInfo: {
    title: 'Cargar Incentivo'
  },
  components:{
    FormIncentive
  },
  data () {
    return {
      data: []
    }
  },
  created(){

    axios.get(`/industrialSecurity/dangerousConditions/incentive/view`)
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