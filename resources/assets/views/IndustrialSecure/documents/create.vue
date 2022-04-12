<template>
  <div>
    <header-module
			title="SEGURIDAD"
			subtitle="CREAR DOCUMENTO"
			url="industrialsecure-documentsSecurity"
		/>

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <documents-form
                url="/legalAspects/fileUpload"
                method="POST"
                :cancel-url="{ name: 'industrialsecure-documentsSecurity'}"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import DocumentsForm from '@/components/IndustrialSecure/Documents/FormDocuments.vue';
import GlobalMethods from '@/utils/GlobalMethods.js';

export default {
  name: 'documents',
  metaInfo: {
    title: 'Seguridad - Documentos'
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
