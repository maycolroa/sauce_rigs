<template>
  <div>
    <header-module
			title="METODOLOGIAS COMPLEMENTARIAS DE VALORACION DE RIESGOS"
			subtitle="ADMINISTRAR METODOLOGIAS"
			url="industrialsecure"
		/>
    
    <div class="col-md">
      <b-card no-body>
        <b-card-header class="with-elements">
          <div class="card-title-elements">
            <b-btn v-if="auth.can['dangerMatrix_methodology_c']" :to="{name:'industrialsecure-complementary-methodology-create'}" variant="primary">Cargar</b-btn>
          </div>
        </b-card-header>
        <b-card-body>
            <vue-table
                configName="industrialsecure-complementary-methodology"
                v-if="auth.can['dangerMatrix_methodology_r']"
                ></vue-table>
        </b-card-body>
    </b-card>
  </div>
  </div>
</template>

<script>
import Alerts from "@/utils/Alerts.js";

export default {
	name: "complementary-methodology",
	metaInfo: {
		title: "Metodologias complementarias de valoracion de riesgos"
	},
  methods: {

  }
};
</script>
