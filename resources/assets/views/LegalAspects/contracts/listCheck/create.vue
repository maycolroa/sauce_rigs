<template>
  <div>
    <header-module
      title="LISTA DE ESTÁNDARES MÍNIMOS"
      subtitle="CREAR CALIFICACIÓN"
      url="legalaspects-list-check-qualification"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <qualification-form 
                url="/legalAspects/listCheck"
                method="POST"
                :cancel-url="{ name: 'legalaspects-list-check-qualification'}"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import QualificationForm from '@/components/LegalAspects/Contracts/ListCheck/FormQualificationComponent.vue';
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'list-check-qualification-create',
  metaInfo() {
    return {
      title: 'Calificaciones - Crear'
    }
  },
  components:{
    QualificationForm
  }
}
</script>
