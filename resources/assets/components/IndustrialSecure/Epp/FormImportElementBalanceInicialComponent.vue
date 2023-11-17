<template>

  <b-form :action="url" @submit.prevent="submit" autocomplete="off">

    <b-card no-body class="mb-2 border-secondary" style="width: 100%;">
      <b-card-header class="with-elements">
       <b-btn v-if="auth.can['elements_c']" variant="primary" href="/templates/elementnotidentimport" target="blank" v-b-tooltip.top title="Generar Plantilla Elementos sin identificar"><i class="fas fa-file-alt"></i></b-btn>&nbsp;&nbsp;
        <!--<b-btn v-if="auth.can['elements_c']" variant="primary" href="/templates/elementidentimport" target="blank" v-b-tooltip.top title="Generar Plantilla Elementos identicados"><i class="fas fa-file-alt"></i></b-btn>&nbsp;&nbsp;-->
      </b-card-header>
      <b-card-body>
        <!--<b-form-row>     
            <vue-radio class="col-md-12" v-model="form.type_element" :options="typesElement" name="type_element" :error="form.errorsFor('type_element')" label="¿Que tipo de elemento desea importar?" :checked="form.type_element"></vue-radio>
        </b-form-row>-->
        <b-form-row>
          <vue-file-simple class="col-md-12" v-model="form.file" label="Archivo" name="file" placeholder="Seleccione un archivo" :error="form.errorsFor(`file`)" :maxFileSize="20"/>
        </b-form-row>
      </b-card-body>
    </b-card>

    <div class="row float-right pt-10 pr-10" style="padding-top: 20px;">
      <template>
        <b-btn variant="default" :to="cancelUrl" :disabled="loading">Cancelar</b-btn>&nbsp;&nbsp;
        <b-btn type="submit" :disabled="loading" variant="primary">Importar</b-btn>
      </template>
    </div>
  </b-form>
</template>

<script>
import VueInput from "@/components/Inputs/VueInput.vue";
import VueRadio from "@/components/Inputs/VueRadio.vue";
import VueAjaxAdvancedSelect from "@/components/Inputs/VueAjaxAdvancedSelect.vue";
import PerfectScrollbar from '@/vendor/libs/perfect-scrollbar/PerfectScrollbar';
import Form from "@/utils/Form.js";
import VueTextarea from "@/components/Inputs/VueTextarea.vue";
import VueCheckboxSimple from "@/components/Inputs/VueCheckboxSimple.vue";
import Alerts from '@/utils/Alerts.js';
import VueFileSimple from "@/components/Inputs/VueFileSimple.vue";

export default {
  components: {
    VueInput,
    VueRadio,
    VueAjaxAdvancedSelect,
    PerfectScrollbar,
    VueTextarea,
    VueCheckboxSimple,
    VueFileSimple
  },
  props: {
    url: { type: String },
    method: { type: String },
    cancelUrl: { type: [String, Object], required: true },
    user: {
      default() {
        return {
          file: '',
          //type_element: ''
        };
      }
    }
  },
  watch: {
    user() {
      this.loading = false;
      this.form = Form.makeFrom(this.user, this.method);
    }
  },
  data() {
    return {
      loading: this.isEdit,
      form: Form.makeFrom(this.user, this.method),
      typesElement: [
        {text: 'Identificable', value: 'Identificable'},
        {text: 'No Identificable', value: 'No Identificable'}
      ],
    };
  },
  methods: {
    submit(e) {
      this.loading = true;
      this.form
        .submit(e.target.action)
        .then(response => {
          this.loading = false;
          Alerts.warning('Información', 'Se inicio la importación, se le notificara a su correo electronico cuando finalice el proceso.');
          this.$router.push({ name: "industrialsecure-epp" });
        })
        .catch(error => {
          this.loading = false;
        });
    }
  }
}
</script>
