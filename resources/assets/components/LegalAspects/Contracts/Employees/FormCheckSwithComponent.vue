<template>

  <b-form :action="url" @submit.prevent="submit" autocomplete="off">
    <div class="col-md-12">
      <p class="text-center text-big mb-4" v-if="auth.hasRole['Arrendatario'] || auth.hasRole['Contratista']">
        ¿Está seguro que desea cambiar el estado del empleado seleccionado?
      </p>
      <p class="text-center text-big mb-4" v-else>
        Estado del empleado.
      </p>
    </div>
    <div class="col-md-12" v-if="auth.hasRole['Arrendatario'] || auth.hasRole['Contratista']">
      <center>
        <b-form-row v-if="form.state_employee">
          <vue-datepicker class="col-md-6 offset-md-3" v-model="form.deadline" label="Fecha de inactivacion" :full-month-name="true" :error="form.errorsFor('deadline')" name="deadline">
                </vue-datepicker>
        </b-form-row>

        <b-form-row v-if="form.state_employee">
          <vue-input class="col-md-12" v-model="form.motive_inactivation" label="Motivo de inactivación" type="text" name="motive_inactivation" :error="form.errorsFor('motive_inactivation')" placeholder="Motivo"/>
        </b-form-row>

        <b-form-row  v-if="form.state_employee">
          <vue-file-simple class="col-md-12" v-model="form.file_inactivation" label="Liquidación" name="file_inactivation" placeholder="Seleccione un archivo" :error="form.errorsFor(`file_inactivation`)" :maxFileSize="20" :help-text="form.file_inactivation ? `Para descargar el archivo actual, haga click <a href='/legalAspects/employeeContract/download/${form.id}' target='blank'>aqui</a> ` : 'El tamaño del archivo no debe ser mayor a 15MB.'"/>
        </b-form-row>
      </center>
    </div>  
    <div class="col-md-12" v-else>
      <center>
        <b-form-row>
          <vue-datepicker class="col-md-6 offset-md-3" v-model="form.deadline" label="Fecha de inactivacion" :full-month-name="true" :error="form.errorsFor('deadline')" name="deadline" :disabled="true">
                </vue-datepicker>
        </b-form-row>

        <b-form-row>
          <vue-input class="col-md-12" v-model="form.motive_inactivation" label="Motivo de inactivación" type="text" name="motive_inactivation" :error="form.errorsFor('motive_inactivation')" placeholder="Motivo" :disabled="true"/>
        </b-form-row>

        <b-form-row v-if="form.file_inactivation && form.type_file == 'pdf'">
          <b-card border-variant="primary" class="mb-3 box-shadow-none" style="width: 100%;" title="Liquidación">
            <iframe style="width: 100%; height: 700px;" frameborder="0" id="frame_imprimir_rendicion" title="Archivo" :src="form.file_inactivation_path"></iframe>
          </b-card>
        </b-form-row>
        <b-form-row v-if="form.file_inactivation && (form.type_file == 'png' || form.type == 'jpg' || form.type == 'jpeg')">
          <b-card border-variant="primary" class="mb-3 box-shadow-none" style="width: 100%;" title="Liquidación">
            <img class="mw-100" :src="`${form.file_inactivation_path}`" alt="Max-width 100%">
          </b-card>
        </b-form-row>
        <b-form-row v-if="form.file_inactivation && (form.type_file != 'png' & form.type != 'jpg' && form.type != 'jpeg' && form.type_file != 'pdf')">
          <vue-file-simple class="col-md-12" v-model="form.file_inactivation" :disabled="true" label="Liquidación" name="file_inactivation" placeholder="Seleccione un archivo" :error="form.errorsFor(`file_inactivation`)" :maxFileSize="20" :help-text="`Para descargar el archivo actual, haga click <a href='/legalAspects/employeeContract/download/${form.id}' target='blank'>aqui</a> `"/>
        </b-form-row>
      </center>
    </div>  

    <div class="col-md-12 pt-10 pr-10">
      <br>
      <center>
        <b-btn variant="default" @click="refresh()" :disabled="loading">{{ !auth.hasRole['Arrendatario'] && !auth.hasRole['Contratista'] ? "Atras" : "Cancelar"}}</b-btn>&nbsp;&nbsp;
        <b-btn type="submit" :disabled="loading" variant="primary" v-if="auth.hasRole['Arrendatario'] || auth.hasRole['Contratista']">Finalizar</b-btn>
      </center>
    </div>
  </b-form>
</template>

<script>
import VueDatepicker from "@/components/Inputs/VueDatepicker.vue";
import VueAjaxAdvancedSelectTagUnic from "@/components/Inputs/VueAjaxAdvancedSelectTagUnic.vue";
import VueInput from "@/components/Inputs/VueInput.vue";
import VueFileSimple from "@/components/Inputs/VueFileSimple.vue";
import Form from "@/utils/Form.js";

export default {
  components: {
    VueDatepicker,
    VueAjaxAdvancedSelectTagUnic,
    VueInput,
    VueFileSimple
  },
  props: {
    url: { type: String },
    method: { type: String, default: 'POST' },
    isEdit: { type: Boolean, default: true },
    cancelUrl: { type: [String, Object], required: true },
    employee: {
      default() {
        return {
            state: '',
            deadline: '',
            motive_inactivation: '',
            file_inactivation: ''
        };
      }
    }
  },
  watch: {
    employee() {
      this.loading = false;
      this.form = Form.makeFrom(this.employee, this.method);
    }
  },
  data() {
    return {
      loading: false,
      form: Form.makeFrom(this.employee, this.method),
    };
  },
  methods: {
    submit(e) {
      this.loading = true;
      this.form
        .submit(e.target.action)
        .then(response => {
          this.loading = false;
          this.$router.push({ name: "legalaspects-contracts-employees" });
        })
        .catch(error => {
          this.loading = false;
        });
    },
    closeEvent() {
      this.$emit("closeModal")
    },
    refresh() {
      if (!this.auth.hasRole['Arrendatario'] && !this.auth.hasRole['Contratista'])
      {
        window.location =  "/legalaspects/employees/view/contract/"+this.form.contract_id
      }
      else
      {
        this.$router.go(-1);
      }
    },
  }
};
</script>
