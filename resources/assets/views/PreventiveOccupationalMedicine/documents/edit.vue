<template>
  <div>
    <header-module
			title="DOCUMENTOS"
			subtitle="EDITAR DOCUMENTO"
			url="preventiveoccupationalmedicine-documentsPreventive"
		/>

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <documents-form
                :url="`/biologicalmonitoring/document/${this.$route.params.id}`"
                method="PUT"
                :is-edit="true"
                :document="data"
                :cancel-url="{ name: 'preventiveoccupationalmedicine-documentsPreventive'}"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import DocumentsForm from '@/components/PreventiveOccupationalMedicine/Documents/FormDocuments.vue';
import GlobalMethods from '@/utils/GlobalMethods.js';
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'documentsPreventive-edit',
  metaInfo: {
    title: 'Medicina Documentos'
  },
  components:{
    DocumentsForm
  },
  data(){
    return {
      data: {},
    }
  },
  created(){
    axios.get(`/biologicalmonitoring/document/${this.$route.params.id}`)
    .then(response => {
        this.data = response.data.data;
    })
    .catch(error => {
        Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
    });
  }
}
</script>
