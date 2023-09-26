<template>
  <div>
    <header-module
      title="ACCIDENTES E INCIDENTES DE TRABAJO"
      subtitle="ENVIAR REPORTE"
      url="industrialsecure-accidentswork"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body style="height: 400px">
            <form-check-swith
              :url="`/industrialSecurity/accidents/emailPdf/${this.$route.params.id}`"
              :event="data"
              :cancel-url="{ name: 'industrialsecure-accidentswork'}"
            />
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import FormCheckSwith from '@/components/IndustrialSecure/AccidentsWork/FormEmailReportPdfComponent.vue';
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'industrialsecure-accidents-work-report-pdf',
  metaInfo() {
    return {
      title: 'Enviar Reporte'
    }
  },
  components:{
    FormCheckSwith
  },
  data () {
    return {
      data: {
        add_email: ''
      }
    }
  }
}
</script>
