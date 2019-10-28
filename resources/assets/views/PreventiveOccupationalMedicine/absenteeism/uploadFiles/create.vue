<template>
  <div>
    <header-module
      title="AUSENTISMO"
      subtitle="SUBIR ARCHIVO"
      url="absenteeism-upload-files"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <upload-file-form
                url="/biologicalmonitoring/absenteeism/fileUpload"
                talendsDataUrl="/selects/absenteeism/talends"
                method="POST"
                :cancel-url="{ name: 'absenteeism-upload-files'}"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import UploadFileForm from '@/components/PreventiveOccupationalMedicine/Absenteeism/UploadFiles/FormUploadFileComponent.vue';

export default {
  name: 'absenteeism-upload-files-create',
  metaInfo: {
    title: 'Ausentismo - Subir Archivo'
  },
  components:{
    UploadFileForm
  }
}
</script>
