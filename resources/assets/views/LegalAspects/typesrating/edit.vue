<template>
  <div>
    <h4 class="font-weight-bold mb-4">
       <span class="text-muted font-weight-light">Tipos de calificaciones /</span> Editar
    </h4>

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <form-type-rating-component
                :url="`/legalAspects/typeRating/${this.$route.params.id}`"
                method="PUT"
                :type-rating="data"
                :is-edit="true"
                :cancel-url="{ name: 'legalaspects-typesrating'}"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import FormTypeRatingComponent from '@/components/LegalAspects/TypesRating/FormTypeRatingComponent.vue';
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'legalaspects-typesrating-edit',
  metaInfo: {
    title: 'Tipos de calificaciones - Editar'
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