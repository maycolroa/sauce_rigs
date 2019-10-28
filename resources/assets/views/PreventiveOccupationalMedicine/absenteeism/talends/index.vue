<template>
  <div>
    <header-module
      title="AUSENTISMO"
      subtitle="ADMINISTAR TALENDS"
      url="preventiveoccupationalmedicine-absenteeism"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-header class="with-elements">
          <div class="card-title-elements" v-if="auth.can['absen_uploadTalend_c']">
            <b-btn :to="{name:'absenteeism-talends-create'}" variant="primary">Subir Talend</b-btn>
          </div>
        </b-card-header>
        <b-card-body>
             <vue-table
                configName="absenteeism-talends"
                v-if="auth.can['absen_uploadTalend_r']"
                ></vue-table>
        </b-card-body>
    </b-card>
    </div>
  </div>
</template>

<script>
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'absenteeism-talends',
  metaInfo: {
    title: 'Talends'
  }
}
</script>
