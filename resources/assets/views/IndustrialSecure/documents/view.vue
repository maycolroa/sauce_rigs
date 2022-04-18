<template>
  <div>
    <header-module
			title="DOCUMENTOS"
			subtitle="VER DOCUMENTO"
			url="industrialsecure-documentsSecurity"
		/>

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <documents-form
                :url="`/industrialSecurity/document/${this.$route.params.id}`"
                method="PUT"
                :view-only="true"
                :document="data"
                :cancel-url="{ name: 'industrialsecure-documentsSecurity'}"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import DocumentsForm from '@/components/IndustrialSecure/Documents/FormDocuments.vue';
import GlobalMethods from '@/utils/GlobalMethods.js';
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'documentsSecurity-view',
  metaInfo: {
    title: 'Seguridad Documentos'
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
    axios.get(`/industrialSecurity/document/${this.$route.params.id}`)
    .then(response => {
        this.data = response.data.data;
    })
    .catch(error => {
        Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
    });
  },
}
</script>
