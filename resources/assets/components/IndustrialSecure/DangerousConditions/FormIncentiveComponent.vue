<template>
    <b-form :action="url" @submit.prevent="submit" autocomplete="off">
        <b-card no-body class="mb-2 border-secondary" style="width: 100%;">
          <b-card-header class="bg-secondary">
            <div class="row">
                <div class="col-md-12">
                    <center>
                        <vue-radio class="col-md-12" v-model="form.ph_state_incentives" :options="trueFalse" name="ph_state_incentives" :error="form.errorsFor('ph_state_incentives')" label="Elige el estado del incentivo" :checked="form.ph_state_incentives">
                          </vue-radio>
                    </center>
                </div>
            </div>
            <b-form-row> 
                <vue-file-simple :help-text="form.old_ph_file_incentives ? `Para descargar el logo actual, haga click <a href='/industrialSecurity/dangerousConditions/incentive/download' target='blank'>aqui</a> `: null" :disabled="!auth.can['ph_inspections_r']" class="col-md-12" accept=".pdf" v-model="form.ph_file_incentives" label="Incentivos (*.pdf)" name="ph_file_incentives" :error="form.errorsFor('ph_file_incentives')" placeholder="Seleccione un archivo" :maxFileSize="10"></vue-file-simple>
            </b-form-row>
          </b-card-header>
        </b-card>

      <div class="row float-right pt-10 pr-10">
          <template>
            <b-btn variant="default" :to="cancelUrl" :disabled="loading">{{ viewOnly ? "Atras" : "Cancelar"}}</b-btn>&nbsp;&nbsp;
            <b-btn type="submit" :disabled="loading" variant="primary" v-if="!viewOnly">Finalizar</b-btn>
          </template>
      </div>

    </b-form>
</template>

<script>
import Form from "@/utils/Form.js";
import VueFileSimple from "@/components/Inputs/VueFileSimple.vue";
import VueRadio from "@/components/Inputs/VueRadio.vue";
import Loading from "@/components/Inputs/Loading.vue";

export default {
    components: {
        VueFileSimple,
        Loading,
        VueRadio
    },
    props: {
        url: { type: String },
        method: { type: String },
        cancelUrl: { type: [String, Object], required: false },
        isEdit: { type: Boolean, default: false },
        viewOnly: { type: Boolean, default: false },
        incentive: {
            default() {
                return {
                    ph_file_incentives: '',
                    ph_state_incentives: true
                };
            }
        }
    },
    watch: {
        incentive() {
          this.loading = false;
          this.form = Form.makeFrom(this.incentive, this.method);
    }
    },
    data() {
        return {
            ready: false,
            loading: false,
            form: Form.makeFrom(this.incentive, this.method),
            trueFalse: [
              {text: 'Activado', value: true},
              {text: 'Desactivado', value: false}
            ]
        };
    },
    methods: {
        submit(e) {
          this.loading = true;
          this.form
            .submit(e.target.action)
            .then(response => {
              this.loading = false;
              this.$router.push({ name: "industrialsecure-dangerousconditions" });
            })
            .catch(error => {
              this.loading = false;
            });
        }
    }
};
</script>