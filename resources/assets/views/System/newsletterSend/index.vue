<template>
  <div>
    <header-module
      title="SISTEMA"
      subtitle="ENVIO DE BOLETIN"
      url="system"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-header class="with-elements">
          <div class="card-title-elements" v-if="auth.can['newsletterSend_c']">
            <b-btn :to="{name:'system-newslettersend-create'}" variant="primary">Crear Boletin</b-btn>
            <b-btn variant="primary" @click="$refs.modalRoles.show()" >Configurar Roles</b-btn>
          </div>
        </b-card-header>
        <b-card-body>
             <vue-table
                configName="system-newslettersend"
                v-if="auth.can['newsletterSend_r']"
                ></vue-table>
        </b-card-body>
    </b-card>

    <b-modal ref="modalRoles" :hideFooter="true" id="modals-historial" class="modal-top" size="lg">>
			<b-card  bg-variant="transparent"  title="" class="mb-3 box-shadow-none">
        <b-form-row>
            <vue-ajax-advanced-select class="col-md-12" v-model="roles_newsletter" name="roles_newsletter" :multiple="true" label="Selecciones los roles a los cuales se les enviaran los boletines" placeholder="Seleccione una opción" :url="rolesDefined" :selected-object="multiselect_roles"></vue-ajax-advanced-select>
        </b-form-row>
        <b-btn block variant="primary" @click="saveConfiguration()">Aceptar</b-btn>
        <b-btn block variant="default" @click="$refs.modalRoles.hide()">Cancelar</b-btn>
      </b-card>
    </b-modal>
    </div>
  </div>
</template>

<script>
import Alerts from '@/utils/Alerts.js';
import VueAjaxAdvancedSelect from "@/components/Inputs/VueAjaxAdvancedSelect.vue";

export default {
  name: 'newslettersend',
  metaInfo: {
    title: 'Envio de boletin'
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
