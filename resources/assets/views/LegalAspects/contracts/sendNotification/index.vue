<template>
  <div>
    <header-module
      title="CONTRATISTAS"
      subtitle="ENVIO DE COMUNICACIÓN"
      url="legalaspects-menu-upload-files"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-header class="with-elements">
          <div class="card-title-elements">
            <b-btn :to="{name:'contract-send-notification-create'}" variant="primary">Crear Notificación</b-btn>
          </div>
        </b-card-header>
        <b-card-body>
             <vue-table
                configName="contract-notification-send"
                ></vue-table>
        </b-card-body>
    </b-card>
    </div>
  </div>
</template>

<script>
import Alerts from '@/utils/Alerts.js';
import VueAjaxAdvancedSelect from "@/components/Inputs/VueAjaxAdvancedSelect.vue";

export default {
  name: 'contract-send-notification',
  metaInfo: {
    title: 'Envio de Comunicación'
  },
  components: {
    VueAjaxAdvancedSelect
  },
  data()
  {     
    return {
      roles_newsletter: [],
      multiselect_roles: [],
      rolesDefined:'/selects/rolesDefined'
    }
  },
  created(){
    this.getConfiguration();
  },
  methods: {
    saveConfiguration()
    {
      let postData = Object.assign({}, {roles_newsletter: this.roles_newsletter, multiselect_roles: this.multiselect_roles});
      axios.post('/system/newsletterSend/saveRoles', postData)
      .then(response => {
          this.$refs.modalRoles.hide();
      })
      .catch(error => {
          Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
      });
    },
    getConfiguration()
    {
      axios.get('/system/newsletterSend/configuration/view')
    .then(response => {
        this.roles_newsletter = response.data.data.roles_newsletter;
        this.multiselect_roles = response.data.data.multiselect_roles
    })
    .catch(error => {
        Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
    });
    }
  }
}
</script>
