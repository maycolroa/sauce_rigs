<template>
  <div>
    <header-module
      title="AUSENTISMO"
      :subtitle="`CREAR DATO - ${table.name ? table.name : ''}`"
      url="absenteeism-tables-records"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
          <loading :display="!ready"/>
          <div v-if="ready">
            <record-form
              url="/biologicalmonitoring/absenteeism/tableRecords"
              method="POST"
              :cancel-url="{ name: 'absenteeism-tables-records'}"
              :table="table"/>
          </div>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import RecordForm from '@/components/PreventiveOccupationalMedicine/Absenteeism/Tables/Records/FormRecordComponent.vue';
import Alerts from '@/utils/Alerts.js';
import Loading from "@/components/Inputs/Loading.vue";

export default {
  name: 'absenteeism-tables-records-create',
  metaInfo: {
    title: 'Dato - Crear'
  },
  components:{
    RecordForm,
    Loading
  },
  data(){
    return {
      ready: false,
      table: {}
    }
  },
  created(){
    axios.get(`/biologicalmonitoring/absenteeism/tables/${this.$route.params.table}`)
    .then(response => {
      this.table = response.data.data
      this.ready = true
    })
    .catch(error => {
      Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
    });
  }
}
</script>
