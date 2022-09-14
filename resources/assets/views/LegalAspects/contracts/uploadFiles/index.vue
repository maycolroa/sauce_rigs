<template>
  <div>
    <header-module
			title="CONTRATISTAS"
			subtitle="ADMINISTRAR ARCHIVOS"
			url="legalaspects-contracts"
		/>
    
    <div class="col-md">
      <b-card no-body>
        <b-card-header class="with-elements">
          <div class="card-title-elements">
            <b-btn v-if="auth.can['contracts_uploadFiles_c']" :to="{name:'legalaspects-upload-files-create'}" variant="primary">Subir Archivo</b-btn>
            <b-btn :to="{name:'legalaspects-upload-files-report'}" variant="primary">Ver Reportes</b-btn>
          </div>
        </b-card-header>
        <b-card-body>
            <vue-table
                v-if="auth.can['contracts_uploadFiles_r']"
                configName="legalAspects-fileUpload"
                :customColumnsName="true" 
                modelId="id"
                ></vue-table>
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
