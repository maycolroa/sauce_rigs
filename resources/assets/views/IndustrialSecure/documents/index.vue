<template>
  <div>
    <header-module
			title="DOCUMENTOS"
			subtitle="ADMINISTRAR DOCUMENTOS"
			url="industrialsecure"
		/>
    
    <div class="col-md">
      <b-card no-body>
        <b-card-header class="with-elements">
          <!--<div class="card-title-elements" v-if="auth.can['documentsSecurity_c']">-->
          <div class="card-title-elements">
            <b-btn :to="{name:'industrialsecure-documentssecurity-create'}" variant="primary">Cargar Documento</b-btn>
          </div>
        </b-card-header>
        <b-card-body>
            <vue-table
                configName="industrialsecure-documents"
                ></vue-table>
        </b-card-body>
    </b-card>
  </div>
  </div>
</template>

<script>
import Alerts from "@/utils/Alerts.js";

export default {
	name: "documentsSecurity",
	metaInfo: {
		title: "Documentos Seguridad"
	},
  methods: {

  }
};
</script>
