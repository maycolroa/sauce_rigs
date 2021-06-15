<template>
  <div>
    <header-module
      v-if="!modal"
      title="MATRIZ DE RIESGOS"
      subtitle="CREAR RIESGOS"
      url="industrialsecure-risks"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <industrial-secure-risk-form
                url="/industrialSecurity/risk"
                method="POST"
                :cancel-url="{ name: 'industrialsecure-risks'}"
                :categories="categories"
                :modal="modal"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import IndustrialSecureRiskForm from '@/components/IndustrialSecure/RiskMatrix/Risk/FormRiskComponent.vue';
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'industrialsecure-risk-create',
  metaInfo: {
    title: 'Riesgos - Crear'
  },
  components:{
    IndustrialSecureRiskForm
  },
  props: {
    modal: { type: Boolean, default: false },
  },
  data(){
    return {
      categories: []
    }
  },
  created(){
    this.fetchOptions('categories', 'rm_risk_categories')
  },
  methods: {
    fetchOptions(key, search)
    {
      axios.post(`/configurableForm/selectOptions`, {key: search})
        .then(response => {
          this[key] = response.data;
        })
        .catch(error => {
          Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
          this.$router.go(-1);
        });
    }
	}
}
</script>
