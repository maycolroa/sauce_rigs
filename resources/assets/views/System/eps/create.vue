<template>
  <div>
    <header-module
      title="SISTEMA"
      subtitle="CREAR EPS"
      url="system-eps"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body style="height: 1500px">
            <form-eps
                url="/system/employeeEps"
                method="POST"
                :cancel-url="{ name: 'system-eps'}"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import FormEps from '@/components/System/Eps/FormEpsComponent.vue';
import GlobalMethods from '@/utils/GlobalMethods.js';
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'system-eps-create',
  metaInfo: {
    title: 'EPS - Crear'
  },
  components:{
    FormEps
  },
  data(){
    return {}
  }
}
</script>
