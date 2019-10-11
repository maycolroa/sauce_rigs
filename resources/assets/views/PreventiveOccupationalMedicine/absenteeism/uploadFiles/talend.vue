<template>
  <div>
    <header-module
      title="AUSENTISMO"
      subtitle="SUBIR TALEND"
      url="absenteeism-upload-files"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <upload-file-form
                url="/biologicalmonitoring/absenteeism/talendUpload"
                method="POST"
                :talend="true"
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
  name: 'absenteeism-upload-files-talend',
  metaInfo: {
    title: 'Ausentismo - Subir Talend'
  },
  components:{
    UploadFileForm
  },
}
</script>
