<template>
  <div>
    <header-module
			title="CONTRATISTAS"
			subtitle="CREAR PROCESO A EVALUAR"
			url="legalaspects-typesrating"
		/>

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
import FormTypeRatingComponent from '@/components/LegalAspects/Contracts/TypesRating/FormTypeRatingComponent.vue';
import Alerts from '@/utils/Alerts.js';

export default {
  name: 'legalaspects-typesrating-create',
  metaInfo: {
    title: 'Procesos a evaluar - Crear'
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
