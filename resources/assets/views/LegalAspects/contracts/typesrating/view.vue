<template>
  <div>
    <header-module
			title="CONTRATISTAS"
			subtitle="VER PROCESO A EVALUAR"
			url="legalaspects-typesrating"
		/>

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <form-type-rating-component
                :type-rating="data"
                :view-only="true"
                :cancel-url="{ name: 'legalaspects-typesrating'}"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>
 
<script>
import FormTypeRatingComponent from '@/components/LegalAspects/Contracts/TypesRating/FormTypeRatingComponent.vue';
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'legalaspects-typesrating-view',
  metaInfo: {
    title: 'Procesos a evaluar - Ver'
  },
  components:{
    FormTypeRatingComponent
  },
  data () {
    return {
      data: [],
    }
  },
  created(){
    axios.get(`/legalAspects/typeRating/${this.$route.params.id}`)
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