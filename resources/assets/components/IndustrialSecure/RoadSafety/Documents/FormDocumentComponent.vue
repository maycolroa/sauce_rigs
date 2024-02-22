<template>

  <b-form :action="url" @submit.prevent="submit" autocomplete="off">
    <b-form-row>
      <vue-ajax-advanced-select :disabled="viewOnly" class="col-md-12" v-model="form.employee_position_id" :error="form.errorsFor('employee_position_id')" :selected-object="form.multiselect_cargo" name="employee_position_id" :label="keywordCheck('position')" placeholder="Seleccione una opción" :url="positionsDataUrl">
          </vue-ajax-advanced-select>
    </b-form-row>

    <blockquote class="blockquote text-center">
          <p class="mb-0">Documentos</p>
    </blockquote>

    <template v-for="(document, index) in form.documents">
      <div :key="document.key">
          <b-form-row>
              <div class="col-md-12" v-if="!viewOnly">
                  <div class="float-right">
                      <b-btn variant="outline-primary icon-btn borderless" size="sm" v-b-tooltip.top title="Eliminar" @click.prevent="removeDocument(index)"><span class="ion ion-md-close-circle"></span></b-btn>
                  </div>
              </div>
              <vue-input class="col-md-12" v-model="document.name" label="Nombre" name="name" :disabled="viewOnly" type="text" placeholder="Nombre" :error="form.errorsFor(`documents.${index}.name`)"></vue-input>
          </b-form-row>
      </div>
    </template>

    <b-form-row style="padding-bottom: 20px;">
      <div class="col-md-12" v-if="!viewOnly">
          <center><b-btn variant="primary" @click.prevent="addDocument()"><span class="ion ion-md-add-circle"></span>&nbsp;&nbsp;Agregar Documento</b-btn></center>
      </div>
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
import VueInput from "@/components/Inputs/VueInput.vue";
import VueAjaxAdvancedSelect from "@/components/Inputs/VueAjaxAdvancedSelect.vue";
import VueAdvancedSelect from "@/components/Inputs/VueAdvancedSelect.vue";
import Form from "@/utils/Form.js";

export default {
  components: {
    VueInput,
    VueAdvancedSelect,
    VueAjaxAdvancedSelect

  },
  props: {
    url: { type: String },
    method: { type: String },
    cancelUrl: { type: [String, Object], required: true },
    isEdit: { type: Boolean, default: false },
    viewOnly: { type: Boolean, default: false },
    positionsDataUrl: { type: String, default: "" },
    document: {
      default() {
        return {
            employee_position_id: '',
            documents: [],
        };
      }
    }
  },
  watch: {
    document() {
      this.loading = false;
      this.form = Form.makeFrom(this.document, this.method);
    }
  },
  data() {
    return {
      loading: this.isEdit,
      form: Form.makeFrom(this.document, this.method),
    };
  },
  methods: {
    addDocument() {
        this.form.documents.push({
            key: new Date().getTime(),
            name: ''
        })
    },
    removeDocument(index)
    {
      if (this.form.documents[index].id != undefined)
        this.form.delete.documents.push(this.form.documents[index].id)

      this.form.documents.splice(index, 1)
    },
    submit(e) {
      this.loading = true;
      this.form
        .submit(e.target.action)
        .then(response => {
          this.loading = false;
          this.$router.push({ name: "industrialsecure-roadsafety-documents" });
        })
        .catch(error => {
          this.loading = false;
        });
    }
  }
};
</script>
