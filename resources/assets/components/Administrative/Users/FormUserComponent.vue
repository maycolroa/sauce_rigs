<template>

  <b-form :action="url" @submit.prevent="submit" autocomplete="off">
    <b-form-row>
      <vue-input :disabled="viewOnly" class="col-md-6" v-model="form.name" label="Nombre" type="text" name="name" :error="form.errorsFor('name')" placeholder="Nombre"></vue-input>
      <vue-input :disabled="viewOnly" class="col-md-6" v-model="form.email" label="Email" type="text" name="email" :error="form.errorsFor('email')" placeholder="Email"></vue-input>
    </b-form-row>

    <b-form-row>
      <vue-input v-if="!isEdit" help-text="La contraseña es un campo opcional. En caso de no ingresar la contraseña se le enviará un correo al usuario para que sea él quien la configure." :disabled="viewOnly" class="col-md-6" v-model="form.password" label="Contraseña" type="password" name="password" :error="form.errorsFor('password')" placeholder="Contraseña"></vue-input>
      <vue-input :disabled="viewOnly" class="col-md-6" v-model="form.document" label="Documento" type="text" name="document" :error="form.errorsFor('document')" placeholder="Documento"></vue-input>
    </b-form-row>

    <b-form-row>
      <template v-if="!auth.hasRole['Arrendatario'] && !auth.hasRole['Contratista'] && form.edit_role">
        <vue-ajax-advanced-select :disabled="viewOnly" class="col-md-6" v-model="form.role_id" :error="form.errorsFor('role_id')" :selected-object="form.multiselect_role" name="role_id" label="Rol" placeholder="Seleccione el rol del usuario" :url="rolesDataUrl">
            </vue-ajax-advanced-select>
      </template>
      <vue-checkbox-simple v-if="isEdit || viewOnly" style="padding-top: 20px;" :disabled="viewOnly" class="col-md-6" v-model="form.active" label="¿Activo?" :checked="form.active" name="active" checked-value="SI" unchecked-value="NO"></vue-checkbox-simple>
    </b-form-row>

    <div class="row float-right pt-10 pr-10">
      <template>
        <b-btn variant="default" :to="cancelUrl" :disabled="loading">{{ viewOnly ? "Atras" : "Cancelar"}}</b-btn>&nbsp;&nbsp;
        <b-btn type="submit" :disabled="loading" variant="primary" v-if="!viewOnly">Finalizar</b-btn>
      </template>
    </div>
  </b-form>
</template>

<script>
import VueAjaxAdvancedSelect from "@/components/Inputs/VueAjaxAdvancedSelect.vue";
import VueInput from "@/components/Inputs/VueInput.vue";
import VueCheckboxSimple from "@/components/Inputs/VueCheckboxSimple.vue";
import Form from "@/utils/Form.js";

export default {
  components: {
    VueAjaxAdvancedSelect,
    VueInput,
    VueCheckboxSimple
  },
  props: {
    url: { type: String },
    method: { type: String },
    cancelUrl: { type: [String, Object], required: true },
    isEdit: { type: Boolean, default: false },
    viewOnly: { type: Boolean, default: false },
    rolesDataUrl: { type: String, default: "" },
    user: {
      default() {
        return {
            name: '',
            email: '',
            password: '',
            document: '',
            role_id: '',
            edit_role: true
        };
      }
    }
  },
  watch: {
    user() {
      this.loading = false;
      this.form = Form.makeFrom(this.user, this.method);
    }
  },
  data() {
    return {
      loading: this.isEdit,
      form: Form.makeFrom(this.user, this.method),
    };
  },
  methods: {
    submit(e) {
      this.loading = true;
      this.form
        .submit(e.target.action)
        .then(response => {
          this.loading = false;
          this.$router.push({ name: "administrative-users" });
        })
        .catch(error => {
          this.loading = false;
        });
    }
  }
};
</script>
