<template>
  <div>
    <header-module
      title="SISTEMA"
      subtitle="USUARIOS - COMPAÑIAS"
      url="system"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
             <vue-table
                configName="system-users-companies"
                v-if="auth.can['usersCompanies_r']"
                ></vue-table>
        </b-card-body>
    </b-card>
    </div>
  </div>
</template>

<script>
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'users-companies',
  metaInfo: {
    title: 'Usuarios - Compañias'
  }
}
</script>
