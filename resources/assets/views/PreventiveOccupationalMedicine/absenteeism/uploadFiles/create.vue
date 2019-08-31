<template>
  <div>
    <h4 class="font-weight-bold mb-4">
       Subir Archivo
    </h4>


    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <!--<upload-file-form
                url="/absenteeism/fileUpload"
                method="POST"
                :cancel-url="{ name: 'absenteeism-upload-files'}"/>-->
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import UploadFileForm from '@/components/PreventiveOccupationalMedicine/Absenteeism/UploadFiles/FormUploadFile.vue';
import GlobalMethods from '@/utils/GlobalMethods.js';

export default {
  name: 'absenteeism-upload-files-create',
  metaInfo: {
    title: 'Ausentismo - Subir Archivos'
  },
  components:{
    UploadFileForm
  },
}
</script>
