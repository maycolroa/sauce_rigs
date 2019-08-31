<template>
  <div>
    <h4 class="font-weight-bold mb-4">Subida de archivos</h4>
    <div class="col-md">
      <b-card no-body>
        <b-card-header class="with-elements">
          <div class="card-title-elements" v-if="auth.can['absen_uploadFiles_c']">
            <b-btn :to="{name:'absenteeism-upload-files-create'}" variant="primary">Subir Archivo</b-btn>
          </div>
        </b-card-header>
        <b-card-body>
           
        </b-card-body>
    </b-card>
  </div>
  </div>
</template>

<script>
import Alerts from "@/utils/Alerts.js";

export default {
	name: "upload-files",
	metaInfo: {
		title: "Subida de Archivos"
	},
  methods: {

  }
};
</script>
