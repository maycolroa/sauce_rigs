<template>
  <div>
    <header-module
      title="REINCORPORACIONES"
      subtitle="GENERAR CARTA"
      url="reinstatements-checks"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
          <loading :display="!ready"/>
          <div v-if="ready">
            <template v-if="form == 'default'">
              <form-check-letter
                  :url="`/biologicalmonitoring/reinstatements/check/${this.$route.params.id}`"
                  method="PUT"
                  :letter="data"
                  :is-edit="true"
                  :cancel-url="{ name: 'reinstatements-checks'}"/>
            </template>
            <template v-if="form == 'reditos'">
              <form-check-letter-reditos 
                :url="`/biologicalmonitoring/reinstatements/check/${this.$route.params.id}`"
                  method="PUT"
                  :letter="data"
                  :is-edit="true"
                  :cancel-url="{ name: 'reinstatements-checks'}"/>
            </template>
          </div>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import FormCheckLetter from '@/components/PreventiveOccupationalMedicine/Reinstatements/Checks/FormCheckLetterComponent.vue';
import FormCheckLetterReditos from '@/components/PreventiveOccupationalMedicine/Reinstatements/Checks/FormCheckLetterReditosComponent.vue';
import Loading from "@/components/Inputs/Loading.vue";
import Alerts from '@/utils/Alerts.js';
import GlobalMethods from '@/utils/GlobalMethods.js';

export default {
  name: 'reinstatements-checks-letter',
  metaInfo: {
    title: 'Generando carta'
  },
  components:{
    FormCheckLetter,
    FormCheckLetterReditos,
    Loading
  },
  data () {
    return {
      data: [],
      ready: false,
      form: ''
    }
  },
  created(){
    axios.post(`/configurableForm/formModel`, {key: 'form_check_letter'})
		.then(response => {
      this.form = response.data;
      
      axios.get(`/biologicalmonitoring/reinstatements/check/${this.$route.params.id}`)
      .then(response2 => {
          this.data = response2.data.data;
          this.ready = true
      })
      .catch(error => {
          Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
          this.$router.go(-1);
      });
		})
		.catch(error => {
			Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
			this.$router.go(-1);
		});
  }
}
</script>