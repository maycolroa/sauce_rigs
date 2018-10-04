<template>

  <b-form :action="url" @submit.prevent="submit" autocomplete="off">
    <b-form-row>
      <vue-input :disabled="viewOnly" class="col-md-6" v-model="form.name" label="Nombre" type="text" name="name" :error="form.errorsFor('name')" placeholder="Nombre"></vue-input>
      <vue-input :disabled="viewOnly" class="col-md-6" v-model="form.description" label="Descripción" type="text" name="description" :error="form.errorsFor('description')" placeholder="Descripción"></vue-input>
    </b-form-row>

    <b-form-row>
      <vue-advanced-select :disabled="viewOnly" class="col-md-12" v-model="form.permissions_multiselect" :error="form.errorsFor('permissions_multiselect')" :multiple="true" :options="permissions" :value="form.permissions_multiselect" :limit="1000" :searchable="true" name="permissions_multiselect" label="Permisos" placeholder="Seleccione los permisos">
        </vue-advanced-select>
    </b-form-row>

    <template v-if="!viewOnly">
      <b-btn type="submit" :disabled="loading" variant="primary">Finalizar</b-btn>
    </template>
    <template v-else>
      <b-btn :to="{name:'administrative-roles'}" variant="primary">Regresar</b-btn>
    </template>
  </b-form>
</template>

<script>
import VueInput from "@/components/Inputs/VueInput.vue";
import VueAdvancedSelect from "@/components/Inputs/VueAdvancedSelect.vue";
import Form from "@/utils/Form.js";

export default {
  components: {
    VueInput,
    VueAdvancedSelect
  },
  props: {
    url: { type: String },
    method: { type: String },
    cancelUrl: { type: [String, Object], required: true },
    isEdit: { type: Boolean, default: false },
    viewOnly: { type: Boolean, default: false },
    permissions: {
      type: Array,
      default: function() {
        return [];
      }
    },
    role: {
      default() {
        return {
            name: '',
            description: '',
            permissions_multiselect: ''
        };
      }
    }
  },
  watch: {
    role() {
      this.loading = false;
      this.form = Form.makeFrom(this.role, this.method);
    }
  },
  data() {
    return {
      loading: this.isEdit,
      form: Form.makeFrom(this.role, this.method)
    };
  },
  methods: {
    submit(e) {
      this.loading = true;
      this.form
        .submit(e.target.action)
        .then(response => {
          this.loading = false;
          this.$router.push({ name: "administrative-roles" });
        })
        .catch(error => {
          this.loading = false;
        });
    }
  }
};
</script>
