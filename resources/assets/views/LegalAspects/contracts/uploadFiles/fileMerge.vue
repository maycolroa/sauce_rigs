<template>
  <div>
    <header-module
			title="CONTRATISTAS"
			subtitle="DESCARGAR ARCHIVO"
			url="legalaspects-upload-files"
		/>

    <div class="col-md">
      <b-card no-body>
        <b-card-body style="height: 700px">
            <legal-aspects-contracts-upload-file-form
                url="/legalAspects/fileUpload/downloadMerge"
                method="POST"
                :cancel-url="{ name: 'legalaspects-upload-files'}"
                :states="states"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import LegalAspectsContractsUploadFileForm from '@/components/LegalAspects/Contracts/UploadFiles/FormFileEmployeeMerge.vue';
import GlobalMethods from '@/utils/GlobalMethods.js';

export default {
  name: 'contracts-upload-files',
  metaInfo: {
    title: 'Contratista - Descargar Archivos'
  },
  components:{
    LegalAspectsContractsUploadFileForm
  },
  data(){
    return {
      data: {},
      states: []
    }
  },
  created(){
    this.fetchSelect('states', '/selects/contracts/statesFile')
  },
  methods: {
    fetchSelect(key, url)
    {
        GlobalMethods.getDataMultiselect(url)
        .then(response => {
            this[key] = response;
        })
        .catch(error => {
            Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
            this.$router.go(-1);
        });
    },
  }
}
</script>
