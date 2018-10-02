<template>
  <div>
    <h4 class="font-weight-bold mb-4">
       <span class="text-muted font-weight-light">Usuarios /</span> Crear
    </h4>


    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <administrative-user-form 
                url="/administration/users"
                method="POST"
                :cancel-url="{ name: 'administrative-users'}"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import AdministrativeUserForm from '@/components/Administrative/Users/FormUserComponent.vue';

export default {
  name: 'administrative-users-create',
  metaInfo: {
    title: 'Usuarios - Crear'
  },
  components:{
    AdministrativeUserForm
  }
}
</script>
