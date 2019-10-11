<template>
  <div>
    <header-module
      title="REINCORPORACIONES"
      subtitle="INFORMES"
      url="reinstatements-checks"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <loading :display="!ready"/>
            <div v-if="ready">
                <informs
                  :form="form"
                  :cancel-url="{ name: 'reinstatements-checks'}"/>
            </div>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import Informs from '@/components/PreventiveOccupationalMedicine/Reinstatements/Checks/InformsComponent.vue';
import Loading from "@/components/Inputs/Loading.vue";
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'reinstatements-checks-create',
  metaInfo: {
    title: 'Reincorporaciones - Informes'
  },
  components:{
    Informs,
    Loading
  },
  data(){
    return {
      ready: false,
      form: ''
    }
  },
  created(){
    axios.post(`/configurableForm/formModel`, { key: 'form_check' })
		.then(response => {
      this.form = response.data;
			this.ready = true
		})
		.catch(error => {
			Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
			this.$router.go(-1);
    });
  }
}
</script>