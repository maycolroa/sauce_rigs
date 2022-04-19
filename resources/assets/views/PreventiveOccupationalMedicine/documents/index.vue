<template>
  <div>
    <header-module
			title="DOCUMENTOS"
			subtitle="ADMINISTRAR DOCUMENTOS"
			url="preventiveoccupationalmedicine"
		/>
    
    <div class="col-md">
      <b-card no-body>
        <b-card-header class="with-elements">
          <div class="card-title-elements" v-if="auth.can['documentsPreventive_c']">
            <b-btn :to="{name:'preventiveoccupationalmedicine-documentspreventive-create'}" variant="primary">Cargar Documento</b-btn>
          </div>
        </b-card-header>
        <b-card-body>
            <vue-table
                v-if="auth.can['documentsPreventive_r']"
                configName="biologicalmonitoring-documents"
                ></vue-table>
        </b-card-body>
    </b-card>
  </div>
  </div>
</template>

<script>
import Alerts from "@/utils/Alerts.js";

export default {
	name: "documentsPreventive",
	metaInfo: {
		title: "Documentos Medicina"
	},
  methods: {

  }
};
</script>
