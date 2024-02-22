<template>
  <div>
    <header-module
      title="SEGURIDAD VIAL"
      subtitle="ADMINISTRAR DOCUMENTOS"
      url="industrialsecure-roadsafety"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-header class="with-elements">
          <div class="card-title-elements" v-if="auth.can['roadsafety_documents_c']">
            <b-btn :to="{name:'industrialsecure-roadsafety-documents-create'}" variant="primary">Crear Documento</b-btn>
          </div>
        </b-card-header>
        <b-card-body>
             <vue-table
                configName="industrialsecure-roadsafety-documents"
                v-if="auth.can['roadsafety_documents_r']"
                ></vue-table>
        </b-card-body>
    </b-card>
    </div>
  </div>
</template>

<script>
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'activities',
  metaInfo: {
    title: 'Actividades'
  }
}
</script>
