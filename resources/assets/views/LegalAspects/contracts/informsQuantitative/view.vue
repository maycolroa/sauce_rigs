<template>
  <div>
    <header-module
			title="CONTRATISTAS"
			subtitle="VER INFORME"
			url="legalaspects-informs"
		/>

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <form-inform-component
                :inform="data"
                :view-only="true"
                :cancel-url="{ name: 'legalaspects-informs'}"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>
 
<script>
import FormInformComponent from '@/components/LegalAspects/Contracts/MonthInforms/FormInformComponent.vue';
import Alerts from '@/utils/Alerts.js';
import GlobalMethods from '@/utils/GlobalMethods.js';

export default {
  name: 'legalaspects-informs-view',
  metaInfo: {
    title: 'Informes - Ver'
  },
  components:{
    FormInformComponent
  },
  data () {
    return {
      data: []
    }
  },
  created(){

    axios.get(`/legalAspects/inform/${this.$route.params.id}`)
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