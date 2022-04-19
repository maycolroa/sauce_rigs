<template>
  <div>
    <header-module
			title="DOCUMENTOS"
			subtitle="ADMINISTRAR DOCUMENTOS"
			url="legalaspects"
		/>
    
    <div class="col-md">
      <b-card no-body>
        <b-card-header class="with-elements">
          <div class="card-title-elements" v-if="auth.can['documentsLegals_c']">
            <b-btn :to="{name:'legalaspects-documentslegals-create'}" variant="primary">Cargar Documento</b-btn>
          </div>
        </b-card-header>
        <b-card-body>
            <vue-table
                v-if="auth.can['documentsLegals_r']"
                configName="legalaspects-documents"
                ></vue-table>
        </b-card-body>
    </b-card>
  </div>
  </div>
</template>

<script>
import Alerts from "@/utils/Alerts.js";

export default {
	name: "documentsLegals",
	metaInfo: {
		title: "Documentos Seguridad"
	},
  methods: {

  }
};
</script>
