<template>

        <!-- <b-row>
            <b-col>
                <b-card border-variant="primary" title="Población Expuesta" class="mb-3 box-shadow-none">
                   
                </b-card>
            </b-col>
        </b-row>
        <b-row>
            <b-col>
                <b-card border-variant="primary" title="Población Expuesta con CUAT" class="mb-3 box-shadow-none">
                  
                </b-card>
            </b-col>
        </b-row> -->
    <b-row>
        <b-col>
            <b-card border-variant="primary" title="Datos personales" class="mb-3 box-shadow-none">
                <b-form :action="url" @submit.prevent="submit" autocomplete="off">
                    <b-form-row>
                        <vue-input :disabled="viewOnly" class="col-md-6" v-model="form.name" label="Nombre" type="text" name="name" :error="form.errorsFor('name')" placeholder="Nombre"></vue-input>
                        <vue-input :disabled="viewOnly" class="col-md-6" v-model="form.email" label="Email" type="text" name="email" :error="form.errorsFor('email')" placeholder="Email"></vue-input>
                    </b-form-row>

                    <b-form-row>
                        <vue-input :disabled="viewOnly" class="col-md-6" v-model="form.nit" label="Nit" type="number" name="nit" :error="form.errorsFor('nit')" placeholder="Nit o número de indetificación"></vue-input>
                        <vue-ajax-advanced-select :disabled="viewOnly" class="col-md-6" v-model="form.role_id" :error="form.errorsFor('role_id')" :selected-object="form.multiselect_role" name="role_id" label="Rol predefinido" placeholder="Seleccione el rol predefinido" :url="rolesDataUrl">
                        </vue-ajax-advanced-select>
                    </b-form-row>

                    <b-form-row>
                        <vue-input :disabled="viewOnly" class="col-md-6" v-model="form.password" label="Razón social" type="text" name="social_reason" :error="form.errorsFor('social_reason')" placeholder="Razón social"></vue-input>
                    </b-form-row>

                    <div class="row float-right pt-10 pr-10">
                        <template>
                            <b-btn variant="default" :to="cancelUrl" :disabled="loading">{{ viewOnly ? "Atras" : "Cancelar"}}</b-btn>&nbsp;&nbsp;
                            <b-btn type="submit" :disabled="loading" variant="primary" v-if="!viewOnly">Finalizar</b-btn>
                        </template>
                    </div>
                </b-form>
            </b-card>
        </b-col>
    </b-row>
</template>

<script>
import VueAjaxAdvancedSelect from "@/components/Inputs/VueAjaxAdvancedSelect.vue";
import VueInput from "@/components/Inputs/VueInput.vue";
import Form from "@/utils/Form.js";

export default {
  components: {
    VueAjaxAdvancedSelect,
    VueInput
  },
  props: {
    url: { type: String },
    method: { type: String },
    cancelUrl: { type: [String, Object], required: true },
    isEdit: { type: Boolean, default: false },
    viewOnly: { type: Boolean, default: false },
    rolesDataUrl: { type: String, default: "" },
    contract: {
      default() {
        return {
            name: '',
            email: '',
            nit: '',
            role_id: '',
            social_reason: ''            
        };
      }
    }
  },
  watch: {
    user() {
      this.loading = false;
      this.form = Form.makeFrom(this.contract, this.method);
    }
  },
  data() {
    return {
      loading: this.isEdit,
      form: Form.makeFrom(this.contract, this.method),
    };
  },
  methods: {
    submit(e) {
      this.loading = true;
      this.form
        .submit(e.target.action)
        .then(response => {
          this.loading = false;
          this.$router.push({ name: "legalaspects-contracts" });
        })
        .catch(error => {
          this.loading = false;
        });
    }
  }
};
</script>
