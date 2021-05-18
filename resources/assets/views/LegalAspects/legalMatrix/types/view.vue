<template>
  <div>
    <header-module
      title="MATRIZ LEGAL"
      subtitle="VER TIPO"
      url="legalaspects-lm-type-company"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <form-type-component
                :type="data"
                :view-only="true"
                :cancel-url="{ name: 'legalaspects-lm-type-company'}"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>
 
<script>
import FormTypeComponent from '@/components/LegalAspects/LegalMatrix/Type/FormTypeComponent.vue';
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'legalaspects-lm-type-view',
  metaInfo: {
    title: 'Tipos - Ver'
  },
  components:{
    FormTypeComponent
  },
  data () {
    return {
      data: [],
    }
  },
  created(){
    axios.get(`/legalAspects/legalMatrix/type/${this.$route.params.id}`)
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