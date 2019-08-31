<template>
  <div>
    <h4 class="font-weight-bold mb-4">
       Subir Archivo
    </h4>


    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <legal-aspects-contracts-upload-file-form
                :url="`/legalAspects/fileUpload/${this.$route.params.id}`"
                method="PUT"
                :is-edit="true"
                :fileUpload="data"
                :cancel-url="{ name: 'legalaspects-upload-files'}"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import LegalAspectsContractsUploadFileForm from '@/components/LegalAspects/Contracts/UploadFiles/FormUploadFile.vue';
import GlobalMethods from '@/utils/GlobalMethods.js';

export default {
  name: 'contracts-upload-files',
  metaInfo: {
    title: 'Contratista - Subir Archivos'
  },
  components:{
    LegalAspectsContractsUploadFileForm
  },
  data(){
    return {
      data: {}
    }
  },
  created(){
    axios.get(`/legalAspects/fileUpload/${this.$route.params.id}`)
    .then(response => {
        this.data = response.data.data;
    })
    .catch(error => {
        Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
        this.$router.go(-1);
    });
  }
}
</script>
