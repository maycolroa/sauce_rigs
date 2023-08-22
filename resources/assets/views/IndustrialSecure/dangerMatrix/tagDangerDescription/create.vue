<template>
  <div>
    <header-module
      title="MATRIZ DE PELIGROS"
      subtitle="CREAR TAG"
      url="industrialsecure-dangermatrix-tags-danger-description"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <form-tag
                url="/industrialSecurity/tags/dangerDescription"
                method="POST"
                :cancel-url="{ name: 'industrialsecure-dangermatrix-tags-danger-description'}"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import FormTag from '@/components/IndustrialSecure/Tags/FormDangerDescriptionsComponent.vue';

export default {
  name: 'industrialsecure-tag-danger-description-create',
  metaInfo: {
    title: 'Descripcion del peligro - Crear'
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
