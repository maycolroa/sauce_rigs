<template>
  <div>
    <header-module
			title="MATRIZ LEGAL"
			subtitle="VER INTERES"
			url="legalaspects-legalmatrix"
		/>

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <form-interest-component
                :interest="data"
                :view-only="true"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>
 
<script>
import FormInterestComponent from '@/components/LegalAspects/LegalMatrix/Interest/FormInterestComponent.vue';
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'legalaspects-lm-interest-view',
  metaInfo: {
    title: 'Intereses - Ver'
  },
  components:{
    FormInterestComponent
  },
  data () {
    return {
      data: [],
    }
  },
  created(){
    axios.get(`/legalAspects/legalMatrix/interest/${this.$route.params.id}`)
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