<template>
  <div>
    <header-module
			title="DOCUMENTOS"
			subtitle="CREAR DOCUMENTO"
			url="preventiveoccupationalmedicine-documentspreventive"
		/>

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <documents-form
                url="/biologicalmonitoring/document"
                method="POST"
                :cancel-url="{ name: 'preventiveoccupationalmedicine-documentspreventive'}"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import DocumentsForm from '@/components/PreventiveOccupationalMedicine/Documents/FormDocuments.vue';
import GlobalMethods from '@/utils/GlobalMethods.js';

export default {
  name: 'documentsPreventive-create',
  metaInfo: {
    title: 'Medicina - Documentos'
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
