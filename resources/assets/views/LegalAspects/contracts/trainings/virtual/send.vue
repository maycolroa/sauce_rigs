<template>
  <div>
    <header-module
      title="CONTRATISTAS"
      subtitle="ENVIAR CAPACITACIÓN"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <form-send-training
              :url="`/legalAspects/trainingContract/sendNotification/${this.$route.params.id}`"
              :employee="data"
              :cancel-url="{ name: 'legalaspects-contracts-trainings-virtual'}"
            />
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import FormSendTraining from '@/components/LegalAspects/Contracts/Trainings/FormSendComponent.vue';
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'legalaspects-contracts-employees-liquidated',
  metaInfo() {
    return {
      title: 'Envio'
    }
  },
  components:{
    FormSendTraining
  },
  data () {
    return {
      data: []
    }
  },
  created(){}
}
</script>
