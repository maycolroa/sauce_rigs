<template>
  <div>
    <header-module
      title="SISTEMA"
      subtitle="CREAR AYUDA"
      url="system-helpers"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body style="height: 1500px">
            <form-helper
                url="/system/helpers"
                method="POST"
                modules-data-url="/selects/modules"
                :cancel-url="{ name: 'system-helpers'}"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import FormHelper from '@/components/System/Helpers/FormHelperComponent.vue';
import GlobalMethods from '@/utils/GlobalMethods.js';
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'system-helpers-create',
  metaInfo: {
    title: 'Ayudas - Crear'
  },
  components:{
    FormHelper
  },
  data(){
    return {}
  }
}
</script>
