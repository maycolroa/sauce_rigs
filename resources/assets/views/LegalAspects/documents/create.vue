<template>
  <div>
    <header-module
			title="DOCUMENTOS"
			subtitle="CREAR DOCUMENTO"
			url="legalaspects-documentslegals"
		/>

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <documents-form
                url="/legalAspects/document"
                method="POST"
                :cancel-url="{ name: 'legalaspects-documentslegals'}"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import DocumentsForm from '@/components/LegalAspects/Documents/FormDocuments.vue';
import GlobalMethods from '@/utils/GlobalMethods.js';

export default {
  name: 'documentsLegals-create',
  metaInfo: {
    title: 'Legales Documentos'
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
