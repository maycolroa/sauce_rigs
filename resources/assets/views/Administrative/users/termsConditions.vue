<template>
  <div>
    <header-module
      title="TÉRMINOS Y CONDICIONES"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
          <loading :display="!ready"/>
          <div v-if="ready">
            <template>
              <terms-conditions-form
                url="/accept_terms_conditions"
                method="POST"
                :termsConditions="termsConditions"/>
            </template>
          </div>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import TermsConditionsForm from '@/components/Administrative/Users/TermsConditionsForm.vue';
import Loading from "@/components/Inputs/Loading.vue";
import GlobalMethods from '@/utils/GlobalMethods.js';

export default {
  name: 'terms-conditions',
  metaInfo: {
    title: 'Terminos y Condiciones'
  },
  components:{
    TermsConditionsForm,
    Loading
  },
  data(){
    return {
      ready: false,
      termsConditions: ''
    }
  },
  created(){
    axios.get(`/get_terms_conditions`)
    .then(response => {
        this.termsConditions = response.data.data;
        this.ready = true;
    })
    .catch(error => {
        Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
        this.$router.go(-1);
    });
  }
}
</script>
