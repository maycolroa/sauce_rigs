<template>
  <div>
    <header-module
      title="ADMINISTRATIVO"
      subtitle="ADMINISTRAR ETIQUETAS"
      url="administrative"
    />


    <div class="col-md">
      <b-card no-body>
        <b-card-header class="with-elements">
          <div class="card-title-elements" v-if="auth.can['customLabels_c']">
            <b-btn :to="{name:'administrative-customlabels-create'}" variant="primary">Configurar Etiqueta</b-btn>
          </div>
        </b-card-header>
        <b-card-body>
          <template>
            <p><b>Nota:</b>Las etiquetas pertenecientes a los niveles de localización se deben configurar en plural y singular.</p>
          </template>
             <vue-table
                configName="administrative-labels"
                v-if="auth.can['customLabels_r']"
                ></vue-table>
        </b-card-body>
    </b-card>
    </div>
  </div>
</template>

<script>
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'labels',
  metaInfo: {
    title: 'Configurar Etiquetas'
  }
}
</script>
