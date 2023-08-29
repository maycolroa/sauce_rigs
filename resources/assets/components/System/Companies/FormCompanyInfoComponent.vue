<template>

  <b-form :action="url" @submit.prevent="submit" autocomplete="off">
    <b-form-row>
      <vue-input :disabled="true" class="col-md-12" v-model="form.name" label="Nombre" type="text" name="name" :error="form.errorsFor('name')" placeholder="Nombre"></vue-input>
    </b-form-row>
    <b-form-row>
      <vue-input :disabled="viewOnly" class="col-md-12" v-model="form.nombre_actividad_economica_sede_principal" label="Nombre de la actividad económica" type="text" name="nombre_actividad_economica_sede_principal" :error="form.errorsFor('nombre_actividad_economica_sede_principal')" placeholder="Nombre de la actividad económica"></vue-input>         
    </b-form-row>
    <b-form-row>
      <vue-radio :disabled="viewOnly" :checked="form.tipo_identificacion_sede_principal" class="col-md-6" v-model="form.tipo_identificacion_sede_principal" :options="typesDocuments" name="tipo_identificacion_sede_principal" :error="form.errorsFor('tipo_identificacion_sede_principal')" label="Tipo de identificación"></vue-radio>
      <vue-input :disabled="viewOnly" class="col-md-6" v-model="form.identificacion_sede_principal" label="Número" type="text" name="identificacion_sede_principal" :error="form.errorsFor('identificacion_sede_principal')" placeholder="Documento"></vue-input>
    </b-form-row>
    <b-form-row>
      <vue-input :disabled="viewOnly" class="col-md-6" v-model="form.direccion_sede_principal" label="Dirección" type="text" name="direccion_sede_principal" :error="form.errorsFor('direccion_sede_principal')" placeholder="Dirección"></vue-input>
      <vue-input :disabled="viewOnly" class="col-md-6" v-model="form.email_sede_principal" label="Email" type="text" name="email_sede_principal" :error="form.errorsFor('email_sede_principal')" placeholder="Email"></vue-input>
    </b-form-row>
    <b-form-row>
      <vue-input :disabled="viewOnly" class="col-md-6" v-model="form.telefono_sede_principal" label="Teléfono" type="text" name="telefono_sede_principal" :error="form.errorsFor('telefono_sede_principal')" placeholder="Teléfono"></vue-input>
      <vue-ajax-advanced-select :disabled="viewOnly" class="col-md-6" v-model="form.departamento_sede_principal_id" :selected-object="form.multiselect_departament_sede" name="departamento_sede_principal_id" :error="form.errorsFor('departamento_sede_principal_id')" label="Departamento" placeholder="Seleccione el departamento" :url="departamentsUrl"></vue-ajax-advanced-select>
    </b-form-row>    
      <b-form-row>
      <vue-ajax-advanced-select :disabled="viewOnly" class="col-md-6" v-model="form.ciudad_sede_principal_id" :selected-object="form.multiselect_municipality_sede" name="ciudad_sede_principal_id" :error="form.errorsFor('ciudad_sede_principal_id')" label="Ciudad" placeholder="Seleccione la ciudad" :url="minicipalitiessUrl" :parameters="{departament: form.departamento_sede_principal_id }"></vue-ajax-advanced-select>
      <vue-radio :disabled="viewOnly" :checked="form.zona_sede_principal" class="col-md-6" v-model="form.zona_sede_principal" :options="zones" name="zona_sede_principal" :error="form.errorsFor('zona_sede_principal')" label="Zona"></vue-radio>
    </b-form-row>

    <div class="row float-right pt-10 pr-10">
      <template>
        <b-btn variant="default" @click="$router.go(-1)" :disabled="loading">Cancelar</b-btn>&nbsp;&nbsp;
        <b-btn type="submit" :disabled="loading" variant="primary" v-if="!viewOnly">Finalizar</b-btn>
      </template>
    </div>
  </b-form>
</template>

<script>
import VueAdvancedSelect from "@/components/Inputs/VueAdvancedSelect.vue";
import VueAjaxAdvancedSelect from "@/components/Inputs/VueAjaxAdvancedSelect.vue";
import VueInput from "@/components/Inputs/VueInput.vue";
import VueRadio from "@/components/Inputs/VueRadio.vue";
import Form from "@/utils/Form.js";

export default {
  components: {
    VueAdvancedSelect,
    VueAjaxAdvancedSelect,
    VueRadio,
    VueInput
  },
  props: {
    url: { type: String },
    method: { type: String },
    cancelUrl: { type: [String, Object], required: true },
    isEdit: { type: Boolean, default: false },
    viewOnly: { type: Boolean, default: false },
    usersOptions: {
			type: Array,
			default: function() {
				return [];
			}
    },
    rolesOptions: {
			type: Array,
			default: function() {
				return [];
			}
		},
    company: {
      default() {
        return {
          name: '',
          nombre_actividad_economica_sede_principal: '',
          tipo_identificacion_sede_principal: '',
          identificacion_sede_principal: '',
          direccion_sede_principal: '',
          telefono_sede_principal: '',
          email_sede_principal: '',
          departamento_sede_principal_id: '',
          ciudad_sede_principal_id: '',
          zona_sede_principal: ''
        };
      }
    }
  },
  watch: {
    company() {
      this.loading = false;
      this.form = Form.makeFrom(this.company, this.method);
    }
  },
  data() {
    return {
      loading: this.isEdit,
      form: Form.makeFrom(this.company, this.method),
      departamentsUrl: '/selects/departaments',
      minicipalitiessUrl: '/selects/municipalities',
      zones: [
         {text: 'Urbana', value: 'Urbana'},
         {text: 'Rural', value: 'Rural'}
      ],
      siNo: [
         {text: 'SI', value: 'SI'},
         {text: 'NO', value: 'NO'}
      ],
      typesDocuments: [
        {text: 'NIT', value: 'NIT'},
        {text: 'CC', value: 'CC'},
        {text: 'CE', value: 'CE'},
        {text: 'NU', value: 'NU'},
        {text: 'PA', value: 'PA'},
      ]
    };
  },
  methods: {
    addUser() {
      this.form.users.push({
        key: new Date().getTime() + Math.round(Math.random() * 10000),
        user_id: '',
        role_id: []
      })
    },
    removeUser(index)
    {
      if (this.form.users[index].user_id != undefined)
        this.form.delete.push(this.form.users[index].user_id)

      this.form.users.splice(index, 1)
    },
    submit(e) {
      this.loading = true;
      this.form
        .submit(e.target.action)
        .then(response => {
          this.loading = false;
          window.location =  "/";;
        })
        .catch(error => {
          this.loading = false;
        });
    }
  }
};
</script>
