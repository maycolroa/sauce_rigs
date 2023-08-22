<template>
  <div>
    <header-module
      title="MATRIZ DE PELIGROS"
      subtitle="CREAR TAG"
      url="industrialsecure-dangermatrix-tags-possible-consequences-danger"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <form-tag
                url="/industrialSecurity/tags/possibleConsequencesDanger"
                method="POST"
                :cancel-url="{ name: 'industrialsecure-dangermatrix-tags-possible-consequences-danger'}"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import FormTag from '@/components/IndustrialSecure/Tags/FormPossibleConsequencesDangerComponent.vue';

export default {
  name: 'industrialsecure-tag-possible-consequences-danger-create',
  metaInfo: {
    title: 'Posibles consecuencias del peligro - Crear'
  },
  components:{
    FormTag
  },
  data(){
    return {
    }
  }
}
</script>
