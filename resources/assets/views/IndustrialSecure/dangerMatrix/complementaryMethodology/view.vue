<template>
  <div>
    <header-module
			title="METODOLOGIAS COMPLEMENTARIAS DE VALORACION DE RIESGOS"
			subtitle="VER METODOLOGIA"
			url="industrialsecure-complementary-methodology"
		/>

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <documents-form
                :view-only="true"
                :document="data"
                :cancel-url="{ name: 'industrialsecure-complementary-methodology'}"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import DocumentsForm from '@/components/IndustrialSecure/DangerMatrix/FormComplementaryMethodology.vue';
import GlobalMethods from '@/utils/GlobalMethods.js';
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'complementary-methodology-view',
  metaInfo: {
    title: 'Seguridad - Metodologia Ver'
  },
  components:{
    DocumentsForm
  },
  data(){
    return {
      data: {}
    }
  },
  created(){
    axios.get(`/industrialSecurity/complementaryMethodology/${this.$route.params.id}`)
    .then(response => {
        this.data = response.data.data;
    })
    .catch(error => {
        Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
    });
  },
}
</script>
