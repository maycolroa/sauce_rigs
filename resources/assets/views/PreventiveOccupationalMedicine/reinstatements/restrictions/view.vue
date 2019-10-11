<template>
  <div>
    <header-module
      title="REINCORPORACIONES"
      subtitle="VER RESTRICCIÓN"
      url="reinstatements-restrictions"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <form-restriction
                :restriction="data"
                :view-only="true"
                :cancel-url="{ name: 'reinstatements-restrictions'}"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>
 
<script>
import FormRestriction from '@/components/PreventiveOccupationalMedicine/Reinstatements/Restrictions/FormRestrictionComponent.vue';
import GlobalMethods from '@/utils/GlobalMethods.js';
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'reinstatements-restrictions-view',
  metaInfo: {
    title: 'Restricciones - Ver'
  },
  components:{
    FormRestriction
  },
  data () {
    return {
      data: []
    }
  },
  created(){
    axios.get(`/biologicalmonitoring/reinstatements/restriction/${this.$route.params.id}`)
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