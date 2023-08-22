<template>
  <div>
    <header-module
      title="MATRIZ DE PELIGROS"
      subtitle="CREAR TAG"
      url="industrialsecure-dangermatrix-tags-warning-signage"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <form-tag
                url="/industrialSecurity/tags/warningSignage"
                method="POST"
                :cancel-url="{ name: 'industrialsecure-dangermatrix-tags-warning-signage'}"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import FormTag from '@/components/IndustrialSecure/Tags/FormWarningsignageComponent.vue';

export default {
  name: 'industrialsecure-tag-warning-signage-create',
  metaInfo: {
    title: 'Controles de Señalizacion y Advertencias - Crear'
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
