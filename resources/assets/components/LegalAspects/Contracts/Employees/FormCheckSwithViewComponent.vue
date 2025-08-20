<template>

  <b-form :action="url" @submit.prevent="submit" autocomplete="off">
    <div class="col-md-12">
      <p class="text-center text-big mb-4">
        Estado del empleado.
      </p>
    </div>  
    <div class="col-md-12" >
      <center>
        <b-form-row>
          <vue-datepicker class="col-md-6 offset-md-3" v-model="form.deadline" label="Fecha de inactivacion" :full-month-name="true" :error="form.errorsFor('deadline')" name="deadline" :disabled="true">
                </vue-datepicker>
        </b-form-row>

        <b-form-row>
          <vue-input class="col-md-12" v-model="form.motive_inactivation" label="Motivo de inactivación" type="text" name="motive_inactivation" :error="form.errorsFor('motive_inactivation')" placeholder="Motivo" :disabled="true"/>
        </b-form-row>
      </center>
    </div>  

    <div class="col-md-12 pt-10 pr-10">
      <br>
      <center>
        <b-btn variant="default" @click="refresh()" :disabled="loading">Atras</b-btn>&nbsp;&nbsp;
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
