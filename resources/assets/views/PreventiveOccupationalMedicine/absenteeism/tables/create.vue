<template>
  <div>
    <header-module
      title="AUSENTISMO"
      subtitle="CREAR TABLA"
      url="absenteeism-tables"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <table-form
                url="/biologicalmonitoring/absenteeism/tables"
                method="POST"
                :cancel-url="{ name: 'absenteeism-tables'}"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import TableForm from '@/components/PreventiveOccupationalMedicine/Absenteeism/Tables/FormTableComponent.vue';
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'absenteeism-tables-create',
  metaInfo: {
    title: 'Tablas - Crear'
  },
  components:{
    TableForm
  },
  data(){
    return {
    }
  }
}
</script>
