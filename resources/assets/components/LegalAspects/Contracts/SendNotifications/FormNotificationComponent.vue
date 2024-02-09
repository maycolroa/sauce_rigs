<template>

  <b-form :action="url" @submit.prevent="submit" autocomplete="off">
    <b-form-row>
      <vue-radio :disabled="viewOnly" class="col-md-12" v-model="form.type" :options="typeSend" name="type" :error="form.errorsFor('type')" label="Elige el tipo de envio a realizar" :checked="form.type"></vue-radio>
    </b-form-row>
    <b-form-row v-if="form.type == 'Contratista'">
      <vue-ajax-advanced-select :disabled="viewOnly" class="col-md-12" v-model="form.contract_id" :selected-object="form.multiselect_contract_id" name="contract_id" label="Contratista" placeholder="Seleccione la contratista" :url="contractDataUrl" :error="form.errorsFor('contract_id')"></vue-ajax-advanced-select>
    </b-form-row>
    <b-form-row v-if="form.type == 'Actividad'">
      <vue-ajax-advanced-select :disabled="viewOnly" class="col-md-12" v-model="form.activity_id" :error="form.errorsFor('activity_id')" :selected-object="form.multiselect_activity" :multiple="true" :allowEmpty="true" name="activity_id" label="Actividades" placeholder="Seleccione las actividades a asignar" :url="activitiesUrl">
						</vue-ajax-advanced-select>
    </b-form-row>
    <b-form-row>
      <vue-input :disabled="viewOnly" class="col-md-12" v-model="form.subject" label="Asunto" type="text" name="subject" :error="form.errorsFor('subject')" placeholder="Asunto"></vue-input>
    </b-form-row>
    <b-form-row>
      <vue-textarea :disabled="viewOnly" class="col-md-12" v-model="form.body" label="Mensaje" name="body" placeholder="Mensaje" rows="3"></vue-textarea>
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
import VueAdvancedSelect from "@/components/Inputs/VueAdvancedSelect.vue";
import VueAjaxAdvancedSelect from "@/components/Inputs/VueAjaxAdvancedSelect.vue";
import VueInput from "@/components/Inputs/VueInput.vue";
import VueRadio from "@/components/Inputs/VueRadio.vue";
import VueFileSimple from "@/components/Inputs/VueFileSimple.vue";
import VueTextarea from "@/components/Inputs/VueTextarea.vue";
import Form from "@/utils/Form.js";

export default {
  components: {
    VueAdvancedSelect,
    VueInput,
    VueRadio,
    VueFileSimple,
    VueAjaxAdvancedSelect,
    VueTextarea
  },
  props: {
    url: { type: String },
    method: { type: String },
    cancelUrl: { type: [String, Object], required: true },
    isEdit: { type: Boolean, default: false },
    viewOnly: { type: Boolean, default: false },
    notification: {
      default() {
        return {
          subject: '',
          body: '',
          activity_id: '',
          contract_id: '' 
        };
      }
    }
  },
  watch: {
    notification() {
      this.loading = false;
      this.form = Form.makeFrom(this.notification, this.method);
    }
  },
  data() {
    return {
      loading: this.isEdit,
      form: Form.makeFrom(this.notification, this.method),
      typeSend: [
        {text: 'Contratista', value: 'Contratista'},
        {text: 'Actividad', value: 'Actividad'}
      ],
      activitiesUrl: '/selects/contracts/ctActivities',
      contractDataUrl: '/selects/contractors'
    };
  },
  methods: {
    submit(e) {
      this.loading = true;
      this.form
        .submit(e.target.action)
        .then(response => {
          this.loading = false;
          this.$router.push({ name: "contract-send-notification" });
        })
        .catch(error => {
          this.loading = false;
        });
    }
  }
};
</script>
