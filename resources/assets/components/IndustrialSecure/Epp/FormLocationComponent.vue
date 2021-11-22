<template>
  <div>
    <b-form :action="url" @submit.prevent="submit" autocomplete="off">
      
      <b-form-row>
        <vue-input :disabled="viewOnly" class="col-md-12" v-model="form.name" label="Nombre" type="text" name="name" :error="form.errorsFor('name')" placeholder="Nombre"></vue-input>
      </b-form-row>

      <b-form-row>
        <location-level-component
          :is-edit="isEdit"
          :view-only="viewOnly"
          v-model="form.locations"
          :location-level="location.locations"
          :form="form"
          application="industrialSecure"
          module="epp"
          @configLocation="setConfigLocation"/>
      </b-form-row>

      <div class="row float-right pt-10 pr-10">
        <template>
          <b-btn variant="default" :to="cancelUrl" :disabled="loading">{{ viewOnly ? "Atras" : "Cancelar"}}</b-btn>&nbsp;&nbsp;
          <b-btn @click="submit" :disabled="loading" variant="primary" v-if="!viewOnly">Finalizar</b-btn>
        </template>
      </div>
    </b-form>
  </div>
</template>

<script>
import VueInput from "@/components/Inputs/VueInput.vue";
import Form from "@/utils/Form.js";
import LocationLevelComponent from '@/components/CustomInputs/LocationLevelComponent.vue';

export default {
  components: {
    VueInput,
    LocationLevelComponent,
  },
  props: {
    url: { type: String },
    method: { type: String },
    cancelUrl: { type: [String, Object], required: true },
    isEdit: { type: Boolean, default: false },
    viewOnly: { type: Boolean, default: false },
    location: {
      default() {
        return {
            locations: {
              employee_regional_id: '',
              employee_headquarter_id: '',
              employee_area_id: '',
              employee_process_id: ''
            },
            name: ''
        };
      }
    }
  },
  watch: {
    location() {
      this.form = Form.makeFrom(this.location, this.method);
    }
  },
  data() {
    return {
      loading: false,
      form: Form.makeFrom(this.location, this.method),
      configLocation: {}
    }
  },
  methods: {
    submit(redirect = true) {
      this.loading = true;
      this.form.add_fields = this.fields;
      this.form
        .submit(this.url)
        .then(response => {
          this.loading = false;

          if (redirect)
            this.$router.push({ name: "industrialsecure-epps-locations" });
        })
        .catch(error => {
          this.loading = false;
        });
    },
    setConfigLocation(value)
    {
      this.configLocation = value
    }
  }
};
</script>
