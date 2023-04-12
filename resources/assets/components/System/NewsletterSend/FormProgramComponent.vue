<template>

  <b-form :action="url" @submit.prevent="submit" autocomplete="off">
    <div class="col-md-12">
      <p class="text-center text-big mb-4">
       Programacion de envio de boletin.
      </p>
    </div>
    <div class="col-md-12">
      <center>
        <b-form-row>
          <vue-datepicker class="col-md-6 offset-md-3" v-model="form.date_send" label="Fecha de envio" :full-month-name="true" :error="form.errorsFor('date_send')" name="date_send">
                </vue-datepicker>
        </b-form-row>
        <b-form-row v-if="form.date_send">
          <vue-advanced-select class="col-md-12" v-model="form.hour" :error="form.errorsFor('hour')" :multiple="false" :options="hours" :hide-selected="false" name="hour" label="Hora de envio" placeholder="Seleccione un horario">
          </vue-advanced-select>
        </b-form-row>
      </center>
    </div>  

    <div class="col-md-12 pt-10 pr-10">
      <center>
        <b-btn variant="default" :to="cancelUrl">Cancelar</b-btn>&nbsp;&nbsp;
        <b-btn type="submit" :disabled="loading" variant="primary">Aceptar</b-btn>
      </center>
    </div>
  </b-form>
</template>

<script>
import VueDatepicker from "@/components/Inputs/VueDatepicker.vue";
import VueAdvancedSelect from "@/components/Inputs/VueAdvancedSelect.vue";
import Form from "@/utils/Form.js";

export default {
  components: {
    VueDatepicker,
    VueAdvancedSelect
  },
  props: {
    url: { type: String },
    method: { type: String, default: 'PUT' },
    isEdit: { type: Boolean, default: true },
    cancelUrl: { type: [String, Object], required: true },
    newsletter: {
      default() {
        return {
            date_send: '',
            hour: ''
        };
      }
    }
  },
  watch: {
    newsletter() {
      this.loading = false;
      this.form = Form.makeFrom(this.newsletter, this.method);
    }
  },
  data() {
    return {
      loading: false,
      form: Form.makeFrom(this.newsletter, this.method),
      hours: [
        {name: '12:00am', value: '00:00:00'},
        {name: '01:00am', value: '01:00:00'},
        {name: '02:00am', value: '02:00:00'},
        {name: '03:00am', value: '03:00:00'},
        {name: '04:00am', value: '04:00:00'},
        {name: '05:00am', value: '05:00:00'},
        {name: '06:00am', value: '06:00:00'},
        {name: '07:00am', value: '07:00:00'},
        {name: '08:00am', value: '08:00:00'},
        {name: '09:00am', value: '09:00:00'},
        {name: '10:00am', value: '10:00:00'},
        {name: '11:00am', value: '11:00:00'},
        {name: '12:00pm', value: '12:00:00'},
        {name: '01:00pm', value: '13:00:00'},
        {name: '02:00pm', value: '14:00:00'},
        {name: '03:00pm', value: '15:00:00'},
        {name: '04:00pm', value: '16:00:00'},
        {name: '05:00pm', value: '17:00:00'},
        {name: '06:00pm', value: '18:00:00'},
        {name: '07:00pm', value: '19:00:00'},
        {name: '08:00pm', value: '20:00:00'},
        {name: '09:00pm', value: '21:00:00'},
        {name: '10:00pm', value: '22:00:00'},
        {name: '11:00pm', value: '23:00:00'},
      ]
    };
  },
  methods: {
    submit(e) {
      this.loading = true;
      console.log(this.form)
      this.form
        .submit(e.target.action)
        .then(response => {
          this.loading = false;

            console.log(this.$data)
            console.log(this)
            Object.assign(this.$data, this.$options.data.apply(this))
            //this.closeEvent()
            this.$router.push({ name: "system-newslettersend" });
        })
        .catch(error => {
          this.loading = false;
        });
    }
  }
};
</script>
