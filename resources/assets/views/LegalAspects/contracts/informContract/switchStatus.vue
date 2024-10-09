<template>
  <div>
    <header-module
      title="CONTRATISTAS"
      subtitle="CAMBIAR ESTADO"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <form-change-state
              url="/legalAspects/informContract/switchStatus"
              :inform="data"
              :cancel-url="{ name: 'legalaspects-informs-contracts'}"
            />
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import FormChangeState from '@/components/LegalAspects/Contracts/MonthInformsContracts/FormChangeStateComponent.vue';
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'legalaspects-informs-contracts-swich-state',
  metaInfo() {
    return {
      title: 'Cambiar Estado'
    }
  },
  components:{
    FormChangeState
  },
  data () {
    return {
      data: []
    }
  },
  created(){
    axios.get(`/legalAspects/informContract/${this.$route.params.id}`)
    .then(response => {
        this.data = response.data.data;
    })
    .catch(error => {
        Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
    });
  }
}
</script>
