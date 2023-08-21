<template>
  <div>
    <header-module
      title="MATRIZ DE PELIGROS"
      subtitle="CREAR TAG"
      url="industrialsecure-dangermatrix-tags-epp"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <form-tag
                url="/industrialSecurity/tags/epp"
                method="POST"
                :cancel-url="{ name: 'industrialsecure-dangermatrix-tags-epp'}"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import FormTag from '@/components/IndustrialSecure/Tags/FormEppControlsComponent.vue';

export default {
  name: 'industrialsecure-tag-epp-create',
  metaInfo: {
    title: 'Controles de Epp - Crear'
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
