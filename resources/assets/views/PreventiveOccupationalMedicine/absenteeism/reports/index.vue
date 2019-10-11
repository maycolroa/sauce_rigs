<template>
  <div>
    <header-module
      title="AUSENTISMO"
      subtitle="ADMINISTRAR INFORMES"
      url="preventiveoccupationalmedicine-absenteeism"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-header class="with-elements">
          <div class="card-title-elements" v-if="auth.can['absen_reports_c']">
            <b-btn :to="{name:'absenteeism-reports-create'}" variant="primary">Crear Informe</b-btn>
          </div>
        </b-card-header>
        <b-card-body>
             <vue-table
                configName="absenteeism-reports"
                v-if="auth.can['absen_reports_r']"
                ></vue-table>
        </b-card-body>
    </b-card>
    </div>
  </div>
</template>

<script>
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'absenteeism-reports',
  metaInfo: {
    title: 'Informes'
  }
}
</script>
