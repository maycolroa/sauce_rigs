<template>
  <div>
    <header-module
      title="MATRIZ DE PELIGROS"
      subtitle="CREAR TAG"
      url="industrialsecure-dangermatrix-tags-participants"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <form-tag
                url="/industrialSecurity/tags/participants"
                method="POST"
                :cancel-url="{ name: 'industrialsecure-dangermatrix-tags-participants'}"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import FormTag from '@/components/IndustrialSecure/Tags/FormParticipantsComponent.vue';

export default {
  name: 'industrialsecure-tag-participants-create',
  metaInfo: {
    title: 'Participantes - Crear'
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
