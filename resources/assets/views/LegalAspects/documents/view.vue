<template>
  <div>
    <header-module
			title="DOCUMENTOS"
			subtitle="VER DOCUMENTO"
			url="legalaspects-documentslegals"
		/>

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <documents-form
                :url="`/LegalAspects/document/${this.$route.params.id}`"
                method="PUT"
                :view-only="true"
                :document="data"
                :cancel-url="{ name: 'legalaspects-documentslegals'}"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import DocumentsForm from '@/components/LegalAspects/Documents/FormDocuments.vue';
import GlobalMethods from '@/utils/GlobalMethods.js';
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'documentsLegals-view',
  metaInfo: {
    title: 'Legales Documentos'
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
    axios.get(`/LegalAspects/document/${this.$route.params.id}`)
    .then(response => {
        this.data = response.data.data;
    })
    .catch(error => {
        Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
    });
  },
}
</script>
