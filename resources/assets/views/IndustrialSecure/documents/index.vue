<template>
  <div>
    <header-module
			title="CONTRATISTAS"
			subtitle="ADMINISTRAR ARCHIVOS"
			url="industrialsecure"
		/>
    
    <div class="col-md">
      <b-card no-body>
        <b-card-header class="with-elements">
          <div class="card-title-elements" v-if="auth.can['documentsSecurity_c']">
            <b-btn :to="{name:'industrialsecure-documentsSecurity-create'}" variant="primary">Subir Archivo</b-btn>
          </div>
        </b-card-header>
        <b-card-body>
            <vue-table
                v-if="auth.can['documentsSecurity_r']"
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
