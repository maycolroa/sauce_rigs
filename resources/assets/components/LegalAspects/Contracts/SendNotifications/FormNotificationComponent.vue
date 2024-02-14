<template>

  <b-form :action="url" @submit.prevent="submit" autocomplete="off">
    <b-form-row>
      <vue-radio :disabled="viewOnly" class="col-md-12" v-model="form.type" :options="typeSend" name="type" :error="form.errorsFor('type')" label="Elige el tipo de envio a realizar" :checked="form.type"></vue-radio>
    </b-form-row>
    <b-form-row v-if="form.type == 'Contratista'">
      <vue-ajax-advanced-select :disabled="viewOnly" class="col-md-12" v-model="form.contract_id" :selected-object="form.multiselect_contracts" name="contract_id" label="Contratista" placeholder="Seleccione la contratista" :multiple="true" :url="contractDataUrl" :error="form.errorsFor('contract_id')"></vue-ajax-advanced-select>
    </b-form-row>
    <b-form-row v-if="form.type == 'Actividad'">
      <vue-ajax-advanced-select :disabled="viewOnly" class="col-md-12" v-model="form.activity_id" :error="form.errorsFor('activity_id')" :selected-object="form.multiselect_activity" :multiple="true" :allowEmpty="true" name="activity_id" label="Actividades" placeholder="Seleccione las actividades a asignar" :url="activitiesUrl">
						</vue-ajax-advanced-select>
    </b-form-row>
      <br v-if="form.type == 'Actividad'">
    <b-row align-h="end" v-if="form.type == 'Actividad' && form.activity_id.length > 0">
        <b-col>
            <b-btn @click="getListContract()" variant="primary">Obtener Listado</b-btn>
        </b-col>
    </b-row>
      <br v-if="form.type == 'Actividad'">
    <b-form-row v-if="showListContract && form.activity_id.length > 0">
      <b-card border-variant="primary" title="Listado de contratistas seleccionados:" class="mb-3 box-shadow-none">
        <table class="table table-bordered table-striped mb-0">
          <thead>
            <tr>
              <th class="text-center align-middle">NIT</th>
              <th class="text-center align-middle">Razon Social</th>
              <th class="text-center align-middle">Tipo</th>
              <th class="text-center align-middle">Nombre Responsable SST</th>
              <th class="text-center align-middle">Correo</th>
            </tr>
          </thead>
          <tbody>
              <tr v-for="(row, index) in contractListSend" :key="`row-${index}`">
                  <td class="align-middle">{{ row['nit'] }}</td>
                  <td class="text-center align-middle">{{ row.social_reason }}</td>
                  <td class="text-center align-middle">{{ row.classification }}</td>
                  <td class="text-center align-middle">{{ row.name }}</td>
                  <td class="text-center align-middle">{{ row.email }}</td>
              </tr>
          </tbody>
        </table>
      </b-card>
    </b-form-row>
      <br v-if="form.type == 'Actividad'">
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
    },
    'form.activity_id' () {
        if (this.showListContract)
          this.getListContract()
    },
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
      contractDataUrl: '/selects/contractors',
      showListContract: false,
      contractListSend: []
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
    },
    getListContract() {
        let postData = Object.assign({}, {activities: this.form.activity_id}, this.filters);

        axios.post('/legalAspects/contracts/notificationSend/getListContract', postData)
        .then(response => {
            this.contractListSend = response.data.data;
            this.showListContract = true;
        })
        .catch(error => {
            this.isLoading = false;
            Alerts.error('Error', 'Hubo un problema recolectando la información');
        });
    }
  }
};
</script>
