<template>
  <div>
    <header-module
      title="MONITOREO DE ACTIVIDADES"
      subtitle="ACTIVIDAD DE USUARIOS"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <vue-table
                configName="administrative-user-activity"
            ></vue-table>
        </b-card-body>
    </b-card>
    </div>
  </div>
</template>

<script>
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'userActivities',
  metaInfo: {
    title: 'Actividad de usuarios'
  }
}
</script>
