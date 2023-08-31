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

    <b-form-row v-if="isEdit">
      <div class="col-md-12">
        <div class="float-right" style="padding-top: 10px;">
              <b-btn variant="primary" @click.prevent="addUser()"><span class="ion ion-md-add-circle"></span>&nbsp;&nbsp;Agregar Centro de trabajo</b-btn>
          </div>
      </div>
    </b-form-row>

    <b-form-row style="padding-top: 15px;" v-if="isEdit">
      <template v-for="(center, index) in form.work_centers">
        <div class="col-md-12" :key="center.key">
          <b-form-row>
            <div class="col-md-12">
              <div class="float-right">
                <b-btn variant="outline-primary icon-btn borderless" size="sm" v-b-tooltip.top title="Eliminar Centro" @click.prevent="removeUser(index)"><span class="ion ion-md-close-circle"></span></b-btn>
              </div>
            </div>
          </b-form-row>
          <b-card bg-variant="transparent" title="Información centro de trabajo" border-variant="dark" class="mb-3 box-shadow-none">
            <b-form-row>
              <vue-input :disabled="viewOnly" class="col-md-6" v-model="center.activity_economic" label="Nombre de la actividad económica" type="text" name="activity_economic" :error="form.errorsFor('activity_economic')" placeholder="Nombre de la actividad económica"></vue-input>
              <vue-input :disabled="viewOnly" class="col-md-6" v-model="center.direction" label="Dirección" type="text" name="direction" :error="form.errorsFor('direction')" placeholder="Dirección"></vue-input>         
            </b-form-row>
            <b-form-row>
              <vue-input :disabled="viewOnly" class="col-md-6" v-model="center.email" label="Email" type="text" name="email" :error="form.errorsFor('email')" placeholder="Email"></vue-input>
              <vue-input :disabled="viewOnly" class="col-md-6" v-model="center.telephone" label="Teléfono" type="text" name="telephone" :error="form.errorsFor('telephone')" placeholder="Teléfono"></vue-input>
            </b-form-row>
            <b-form-row>            
              <vue-ajax-advanced-select :disabled="viewOnly" class="col-md-6" v-model="center.departament_id" :selected-object="center.multiselect_departament_centro" name="departament_id" :error="form.errorsFor('departament_id')" label="Departamento" placeholder="Seleccione el departamento" :url="departamentsUrl"></vue-ajax-advanced-select>
              <vue-ajax-advanced-select :disabled="viewOnly" class="col-md-6" v-model="center.city_id" :selected-object="center.multiselect_municipality_centro" name="city_id" :error="form.errorsFor('city_id')" label="Ciudad" placeholder="Seleccione la ciudad" :url="minicipalitiessUrl" :parameters="{departament: center.departament_id }"></vue-ajax-advanced-select>
            </b-form-row>    
            <b-form-row>            
              <vue-radio :disabled="viewOnly" :checked="center.zona" class="col-md-12" v-model="center.zona" :options="zones" name="zona" :error="form.errorsFor('zona')" label="Zona"></vue-radio>
            </b-form-row>
          </b-card>
        </div>
      </template>
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
          zona_sede_principal: '',
          work_centers: {},
          delete: []
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
      this.form.work_centers.push({
        key: new Date().getTime() + Math.round(Math.random() * 10000),
        activity_economic: '',
        direction: '',
        telephone: '',
        email: '',
        departament_id: '',
        city_id: '',
        zona: '',
      })
    },
    removeUser(index)
    {
      if (this.form.work_centers[index].user_id != undefined)
        this.form.delete.push(this.form.work_centers[index].user_id)

      this.form.work_centers.splice(index, 1)
    },
    submit(e) {
      this.loading = true;
      this.form.input = true;
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
