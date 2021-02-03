<template>
  <div>
    <header-module
      title="ADMINISTRATIVO"
      subtitle="IMPORTAR USUARIO"
      url="administrative-users"
    />


    <div class="col-md">
      <b-card no-body>
        <b-card-body>
          <div>
            <form-import
              url="/administration/users/import"
              method="POST"
              roles-data-url="/selects/roles"
              :cancel-url="{ name: 'administrative-users'}"/>
          </div>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import FormImport from '@/components/Administrative/Users/FormImportComponent.vue';
import Loading from "@/components/Inputs/Loading.vue";

export default {
  name: 'administrative-users-create',
  metaInfo: {
    title: 'Usuarios - Importar'
  },
  components:{
    FormImport,
    Loading
  },
  data(){
    return {
      form: ''
    }
  }
}
</script>
