<template>
  <div>
    <header-module
			title="METODOLOGIAS COMPLEMENTARIAS DE VALORACION DE RIESGOS"
			subtitle="CREAR METODOLOGIA"
			url="industrialsecure-complementary-methodology"
		/>

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <documents-form
                url="/industrialSecurity/complementaryMethodology"
                method="POST"
                :cancel-url="{ name: 'industrialsecure-complementary-methodology'}"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import DocumentsForm from '@/components/IndustrialSecure/DangerMatrix/FormComplementaryMethodology.vue';
import GlobalMethods from '@/utils/GlobalMethods.js';

export default {
  name: 'complementary-methodology-create',
  metaInfo: {
    title: 'Seguridad - Metodologia'
  },
  components:{
    DocumentsForm
  },
  data(){
    return {
      data: {},
    }
  }
}
</script>
