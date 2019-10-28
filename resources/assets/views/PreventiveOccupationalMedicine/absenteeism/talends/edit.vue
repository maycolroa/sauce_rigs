<template>
  <div>
    <header-module
      title="AUSENTISMO"
      subtitle="EDITAR TALEND"
      url="absenteeism-talends"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <form-talend
                :url="`/biologicalmonitoring/absenteeism/talendUpload/${this.$route.params.id}`"
                method="PUT"
                :talend="data"
                :is-edit="true"
                :cancel-url="{ name: 'absenteeism-talends'}"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import FormTalend from '@/components/PreventiveOccupationalMedicine/Absenteeism/Talends/FormTalendComponent.vue';
import GlobalMethods from '@/utils/GlobalMethods.js';
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'absenteeism-talends-edit',
  metaInfo: {
    title: 'Talends - Editar'
  },
  components:{
    FormTalend
  },
  data () {
    return {
      data: []
    }
  },
  created(){
    axios.get(`/biologicalmonitoring/absenteeism/talendUpload/${this.$route.params.id}`)
    .then(response => {
        this.data = response.data.data;
    })
    .catch(error => {
        Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
        this.$router.go(-1);
    });
  }
}
</script>