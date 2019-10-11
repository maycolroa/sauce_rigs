<template>
  <div>
    <header-module
      title="AUSENTISMO"
      subtitle="EDITAR ARCHIVO"
      url="absenteeism-upload-files"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <upload-file-form
                :url="`/biologicalmonitoring/absenteeism/fileUpload/${this.$route.params.id}`"
                method="PUT"
                :is-edit="true"
                :fileUpload="data"
                :cancel-url="{ name: 'absenteeism-upload-files'}"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import UploadFileForm from '@/components/PreventiveOccupationalMedicine/Absenteeism/UploadFiles/FormUploadFile.vue';
import GlobalMethods from '@/utils/GlobalMethods.js';

export default {
  name: 'absenteeism-upload-files-edit',
  metaInfo: {
    title: 'Ausentismo - Subir Archivos'
  },
  components:{
    UploadFileForm
  },
  data(){
    return {
      data: {}
    }
  },
  created(){
    axios.get(`/biologicalmonitoring/absenteeism/fileUpload/${this.$route.params.id}`)
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
