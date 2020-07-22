<template>
  <div>
    <header-module
      title="CONDICIONES PELIGROSAS"
      subtitle="INSPECCIONES CALIFICADAS"
      url="dangerousconditions-inspections"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
              <vue-table
                configName="dangerousconditions-inspections-qualification"
                :customColumnsName="true" 
                v-if="auth.can['ph_inspections_r']"
                :params="{inspectionId: `${this.$route.params.id}`}"
                ></vue-table>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'dangerousconditions-inspections-qualification',
  metaInfo: {
    title: 'Inspecciones - Calificadas'
  }
}
</script>
