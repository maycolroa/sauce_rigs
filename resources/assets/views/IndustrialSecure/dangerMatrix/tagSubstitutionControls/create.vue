<template>
  <div>
    <header-module
      title="MATRIZ DE PELIGROS"
      subtitle="CREAR TAG"
      url="industrialsecure-dangermatrix-tags-substitutions"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <form-tag
                url="/industrialSecurity/tags/substitution"
                method="POST"
                :cancel-url="{ name: 'industrialsecure-dangermatrix-tags-substitutions'}"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import FormTag from '@/components/IndustrialSecure/Tags/FormSubstitutionsComponent.vue';

export default {
  name: 'industrialsecure-tag-substitutions-create',
  metaInfo: {
    title: 'Sustituciones - Crear'
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
