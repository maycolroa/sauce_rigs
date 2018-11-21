<template>
  <div>
    <h4 class="font-weight-bold mb-4">
       <span class="text-muted font-weight-light">Roles /</span> Editar
    </h4>

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <administrative-role-form 
                :url="`/administration/role/${this.$route.params.id}`"
                method="PUT"
                :modules="modules"
                :permissions="permissions"
                :role="data"
                :is-edit="true"
                :cancel-url="{ name: 'administrative-roles'}"/>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import AdministrativeRoleForm from '@/components/Administrative/Roles/FormRoleComponent.vue';
import Alerts from '@/utils/Alerts.js';
import GlobalMethods from '@/utils/GlobalMethods.js';

export default {
  name: 'administrative-roles-edit',
  metaInfo: {
    title: 'Roles - Editar'
  },
  components:{
    AdministrativeRoleForm
  },
  data () {
    return {
      data: [],
      modules: [],
      permissions:[]
    }
  },
  created(){  
    GlobalMethods.getPermissionsMultiselect()
    .then(response => {
        this.permissions = response;

        axios.get(`/administration/role/${this.$route.params.id}`)
        .then(response => {
            let elements = response.data.data.permissions_asignates
            let temp = []

            for (var keyModule in elements)
            {
              if (elements.hasOwnProperty(keyModule))
              {
                let subElements = elements[keyModule]["permissions"]

                for (var keyPermission in subElements)
                {
                  if (subElements.hasOwnProperty(keyPermission))
                  {  
                    for (var keyFullPermission in this.permissions[keyModule])
                    {
                      if (this.permissions[keyModule][keyFullPermission].value == subElements[keyPermission].value)
                      {
                        this.permissions[keyModule].splice(keyFullPermission, 1);
                      }
                    }
                  }
                }
              }
              this.$set(temp, keyModule, elements[keyModule])
            }
            
            response.data.data.permissions_asignates = temp
            this.data = response.data.data;
        })
        .catch(error => {
            Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
            this.$router.go(-1);
        });
    })
    .catch(error => {
        Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
        this.$router.go(-1);
    });

    GlobalMethods.getModulesMultiselectGroup()
    .then(response => {
        this.modules = response;
    })
    .catch(error => {
        Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
        this.$router.go(-1);
    });
  },
}
</script>