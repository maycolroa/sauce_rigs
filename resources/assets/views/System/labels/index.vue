<template>
  <div>
    <header-module
      title="SISTEMA"
      subtitle="ADMINISTRAR LABELS"
      url="system"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
             <vue-table
                configName="system-labels"
                v-if="auth.can['labels_r']"
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
    title: 'Labels'
  }
}
</script>
