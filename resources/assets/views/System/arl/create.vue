<template>
  <div>
    <header-module
      title="SISTEMA"
      subtitle="CREAR ARL"
      url="system-arl"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body style="height: 1500px">
            <form-arl
                url="/system/employeeArl"
                method="POST"
                :cancel-url="{ name: 'system-arl'}"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import FormArl from '@/components/System/Arl/FormArlComponent.vue';
import GlobalMethods from '@/utils/GlobalMethods.js';
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'system-arl-create',
  metaInfo: {
    title: 'ARL - Crear'
  },
  components:{
    FormArl
  },
  data(){
    return {}
  }
}
</script>
