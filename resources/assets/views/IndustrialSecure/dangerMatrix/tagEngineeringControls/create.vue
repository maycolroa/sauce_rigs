<template>
  <div>
    <header-module
      title="MATRIZ DE PELIGROS"
      subtitle="CREAR TAG"
      url="industrialsecure-dangermatrix-tags-engineering-controls"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <form-tag
                url="/industrialSecurity/tags/engineeringControls"
                method="POST"
                :cancel-url="{ name: 'industrialsecure-dangermatrix-tags-engineering-controls'}"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import FormTag from '@/components/IndustrialSecure/Tags/FormEngineeringControlsComponent.vue';

export default {
  name: 'industrialsecure-tag-engineering-controls-create',
  metaInfo: {
    title: 'Controles de Ingenieria - Crear'
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
