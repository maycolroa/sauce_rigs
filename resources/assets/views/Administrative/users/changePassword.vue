<template>
  <div>
    <header-module
      title="CAMBIAR CONTRASEÑA"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <form-change-password
                :url="`/administration/users/changePassword`"
                method="POST"
                :cancel-url="{ path: '/'}"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import FormChangePassword from '@/components/Administrative/Users/FormChangePassword.vue';

export default {
  name: 'change-password',
  metaInfo: {
    title: 'Cambiar Contraseña'
  },
  components:{
    FormChangePassword
  },
  data () {
    return {
    }
  },
}
</script>