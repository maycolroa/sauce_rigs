<template>
  <div>
    <h4 class="font-weight-bold mb-4">
       <span class="text-muted font-weight-light">Tipos de calificaciones /</span> Crear
    </h4>


    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <form-type-rating-component
                url="/legalAspects/typeRating"
                method="POST"
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
  name: 'legalaspects-typesrating-create',
  metaInfo: {
    title: 'Tipos de calificaciones - Crear'
  },
  components:{
    FormTypeRatingComponent
  },
  data(){
    return {
    }
  }
}
</script>
