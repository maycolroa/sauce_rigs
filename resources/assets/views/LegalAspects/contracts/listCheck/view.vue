<template>
  <div>
    <header-module
      title="LISTA DE ESTÁNDARES MÍNIMOS"
      subtitle="VER CALIFICACIÓN"
      url="legalaspects-list-check-qualification"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <qualification-form
                :listCheck="data"
                :view-only="true"
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
  name: 'list-check-qualification-view',
  metaInfo() {
    return {
      title: 'Calificaciones - Ver'
    }
  },
  components:{
    QualificationForm
  },
  data () {
    return {
      data: [],
    }
  },
  created(){
    axios.get(`/legalAspects/listCheck/${this.$route.params.id}`)
    .then(response => {
        this.data = response.data.data;
    })
    .catch(error => {
        Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
        this.$router.go(-1);
    });
  },
}
</script>