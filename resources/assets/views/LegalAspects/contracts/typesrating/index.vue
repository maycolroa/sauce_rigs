<template>
  <div>
    <header-module
			title="CONTRATISTAS"
			subtitle="ADMINISTRAR TIPOS DE CALIFICACIONES"
			url="legalaspects-evaluations"
		/>

    <div class="col-md">
      <b-card no-body>
        <b-card-header class="with-elements">
          <div class="card-title-elements"> 
            <b-btn v-if="auth.can['contracts_typesQualification_c']" :to="{name:'legalaspects-typesrating-create'}" variant="primary">Crear tipo de calificación</b-btn>
          </div>
        </b-card-header>
        <b-card-body>
             <vue-table
                v-if="auth.can['contracts_typesQualification_r']"
                configName="legalaspects-typesrating"
                ></vue-table>
        </b-card-body>
    </b-card>
    </div>
  </div>
</template>

<script>
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'types-rating',
  metaInfo: {
    title: 'Tipos de calificaciones'
  }
}
</script>
