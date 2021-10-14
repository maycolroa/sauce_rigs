<template>
  <div>
    <header-module
			title="CONTRATISTAS"
			subtitle="CREAR INFORME"
			url="legalaspects-informs"
		/>

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <form-inform-component
                url="/legalAspects/inform"
                method="POST"
                :cancel-url="{ name: 'legalaspects-informs'}"
                :inform="data"/>
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
  name: 'legalaspects-informs-create',
  metaInfo: {
    title: 'Informes - Crear'
  },
  components:{
    FormInformComponent
  },
  data(){
    return {
      data: []
    }
  },
  created(){
    axios.get(`/legalAspects/inform/${this.$route.params.id}`)
    .then(response => {
        this.data = response.data.data;
        delete this.data.id
        this.data.themes.map((theme) => {
          delete theme.id
          delete theme.inform_id
          return theme;
        });
    })
  }
}
</script>
