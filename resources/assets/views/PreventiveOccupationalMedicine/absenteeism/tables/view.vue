<template>
  <div>
    <header-module
      title="AUSENTISMO"
      subtitle="VER TABLA"
      url="absenteeism-tables"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <form-table
                :table="data"
                :view-only="true"
                :cancel-url="{ name: 'absenteeism-tables'}"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>
 
<script>
import formTable from '@/components/PreventiveOccupationalMedicine/Absenteeism/Tables/FormTableComponent.vue';
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'absenteeism-tables-view',
  metaInfo: {
    title: 'Tablas - Ver'
  },
  components:{
    formTable
  },
  data () {
    return {
      data: []
    }
  },
  created(){
    axios.get(`/biologicalmonitoring/absenteeism/tables/${this.$route.params.id}`)
    .then(response => {
        this.data = response.data.data;
    })
    .catch(error => {
        Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
    });
  }
}
</script>