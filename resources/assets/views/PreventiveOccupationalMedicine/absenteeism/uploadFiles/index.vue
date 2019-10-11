<template>
  <div>
    <header-module
      title="AUSENTISMO"
      subtitle="ADMINISTRAR ARCHIVOS"
      url="preventiveoccupationalmedicine-absenteeism"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-header class="with-elements">
          <div class="card-title-elements" v-if="auth.can['absen_uploadFiles_c']">
            <b-btn :disabled="data==null" :to="{name:'absenteeism-upload-files-create'}" variant="primary">Subir Archivo</b-btn>
          </div>
          <div class="card-title-elements" v-if="auth.can['absen_uploadTalend_c']">
            <b-btn :to="{name:'absenteeism-upload-files-talend'}" variant="primary">Subir Talend</b-btn>
          </div>
        </b-card-header>
        <b-card-body>
           <vue-table
                v-if="auth.can['absen_uploadFiles_r']"
                configName="absenteeism-fileUpload"
                ></vue-table>
        </b-card-body>
    </b-card>
  </div>
  </div>
</template>

<script>
import Alerts from "@/utils/Alerts.js";

export default {
	name: "absenteeism-upload-files",
	metaInfo: {
		title: "Subida de Archivos"
	},
  methods: {

  },
  data(){
    return {
      data: {}
    }
  },
  created(){
    axios.post(`/biologicalmonitoring/absenteeism/talendUpload/data`)
    .then(response => {
        this.data = response.data.data;
    })
    .catch(error => {
        Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
        this.$router.go(-1);
    });
  }
};
</script>
