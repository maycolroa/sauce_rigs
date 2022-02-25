<template>

  <b-form :action="url" @submit.prevent="submit" autocomplete="off">
    <b-form-row>
      <vue-radio :checked="form.inventory_management" class="col-md-12" v-model="form.inventory_management" :options="siNo" name="inventory_management" :error="form.errorsFor('inventory_management')" label="¿Desea manejar inventario?">
        </vue-radio>
    </b-form-row>

    <b-form-row>
      <vue-textarea class="col-md-12" v-model="form.text_letter_epp" label="Texto a mostrar en la carta de entregas (Opcional, existe un texto por defecto)" name="text_letter_epp" placeholder="Texto" :error="form.errorsFor('text_letter_epp')" rows="5"></vue-textarea>  
    </b-form-row>

    <b-form-row>
      <vue-radio :checked="form.expired_elements_asigned" class="col-md-12" v-model="form.expired_elements_asigned" :options="siNo" name="expired_elements_asigned" :error="form.errorsFor('expired_elements_asigned')" label="¿Desea recibir notificación por vencimiento de elementos asignados?">
        </vue-radio>
      <vue-input v-if="form.expired_elements_asigned == 'SI'" class="col-md-12" v-model="form.days_alert_expiration_date_elements" label="Días de alerta por fecha de vencimiento cercana para los elementos" type="number" name="days_alert_expiration_date_elements" :error="form.errorsFor('days_alert_expiration_date_elements')" placeholder="1"></vue-input>
    </b-form-row>

    <div class="row float-right pt-10 pr-10">
      <template>
        <b-btn type="submit" :disabled="loading || (!auth.can['configuration_epp_c'])" variant="primary">Guardar</b-btn>
      </template>
    </div>
  </b-form>
</template>

<script>
import VueInput from "@/components/Inputs/VueInput.vue";
import VueRadio from "@/components/Inputs/VueRadio.vue";
import VueTextarea from "@/components/Inputs/VueTextarea.vue";
import Form from "@/utils/Form.js";

export default {
  components: {
    VueInput,
    VueRadio,
    VueTextarea
  },
  props: {
    url: { type: String },
    method: { type: String },
    siNo: {
      type: Array,
      default: function() {
        return [];
      }
    },
    configuration: {
      default() {
        return {
          inventory_management: '',
          text_letter_epp: ''
        };
      }
    }
  },
  watch: {
    configuration() {
      this.loading = false;
      this.form = Form.makeFrom(this.configuration, this.method);
    }
  },
  data() {
    return {
      loading: this.isEdit,
      form: Form.makeFrom(this.configuration, this.method)
    };
  },
  methods: {
    submit(e) {
      this.loading = true;
      this.form
        .submit(e.target.action)
        .then(response => {
          this.loading = false;
          window.location = "/industrialsecure/epp/menu"
          //this.$router.push({ name: "industrialsecure-epp" });
        })
        .catch(error => {
          this.loading = false;
        });
    }
  }
};
</script>
