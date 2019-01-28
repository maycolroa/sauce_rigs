<template>
  <div>
    <h4 class="font-weight-bold mb-4">
       <span class="text-muted font-weight-light">Regionales /</span> Ver
    </h4>

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <administrative-regional-form
                :regional="data"
                :view-only="true"
                :cancel-url="{ name: 'administrative-regionals'}"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>
 
<script>
import AdministrativeRegionalForm from '@/components/Administrative/Regionals/FormRegionalComponent.vue';
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'administrative-regionals-edit',
  metaInfo: {
    title: 'Regionales - Ver'
  },
  components:{
    AdministrativeRegionalForm
  },
  data () {
    return {
      data: [],
    }
  },
  created(){
    axios.get(`/administration/regional/${this.$route.params.id}`)
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