<template>
  <div>
    <header-module
      title="MATRIZ LEGAL"
      subtitle="EDITAR SISTEMA"
      url="legalaspects-legalmatrix"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <form-system-apply-component
                :url="`/legalAspects/legalMatrix/systemApply/${this.$route.params.id}`"
                method="PUT"
                :systemApply="data"
                :is-edit="true"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import FormSystemApplyComponent from '@/components/LegalAspects/LegalMatrix/SystemApply/FormSystemApplyComponent.vue';
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'legalaspects-lm-system-apply-edit',
  metaInfo: {
    title: 'Sistemas que Aplican - Editar'
  },
  components:{
    FormSystemApplyComponent
  },
  data () {
    return {
      data: [],
    }
  },
  created(){
    axios.get(`/legalAspects/legalMatrix/systemApply/${this.$route.params.id}`)
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