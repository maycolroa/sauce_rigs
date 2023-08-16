<template>
  <div>
    <header-module
      title="MATRIZ DE PELIGROS"
      subtitle="CREAR TAG"
      url="industrialsecure-dangermatrix-tags-administrative-controls"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <form-tag
                url="/industrialSecurity/tags/administrativeControls"
                method="POST"
                :cancel-url="{ name: 'industrialsecure-dangermatrix-tags-administrative-controls'}"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import FormTag from '@/components/IndustrialSecure/Tags/FormAdministrativeControlsComponent.vue';

export default {
  name: 'industrialsecure-tag-administrative-controls-create',
  metaInfo: {
    title: 'Controles Administrativos - Crear'
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
