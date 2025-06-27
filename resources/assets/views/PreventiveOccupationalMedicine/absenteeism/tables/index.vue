<template>
  <div>
    <header-module
      title="AUSENTISMO"
      subtitle="ADMINISTRAR TABLAS"
      url="preventiveoccupationalmedicine-absenteeism"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-header class="with-elements">
          <div class="card-title-elements" v-if="auth.can['absen_tables_c']">
            <b-btn :to="{name:'absenteeism-tables-create'}" variant="primary">Crear Tabla</b-btn>
          </div>
        </b-card-header>
        <b-card-body>
             <vue-table
                configName="absenteeism-tables"
                v-if="auth.can['absen_tables_r']"
                ></vue-table>
        </b-card-body>
    </b-card>
    </div>
  </div>
</template>

<script>
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'absenteeism-tables',
  metaInfo: {
    title: 'Tablas'
  }
}
</script>
