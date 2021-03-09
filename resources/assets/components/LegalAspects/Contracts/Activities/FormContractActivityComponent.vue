<template>

  <b-form :action="url" @submit.prevent="submit" autocomplete="off">
    <b-form-row>
      <vue-input :disabled="viewOnly" class="col-md-12" v-model="form.name" label="Nombre" type="text" name="name" :error="form.errorsFor('name')" placeholder="Nombre"></vue-input>
    </b-form-row>

    <blockquote class="blockquote text-center">
          <p class="mb-0">Documentos de los empleados del contratistas</p>
    </blockquote>

    <template v-for="(document, index) in form.documents">
      <div :key="document.key">
          <b-form-row>
              <div class="col-md-12" v-if="!viewOnly">
                  <div class="float-right">
                      <b-btn variant="outline-primary icon-btn borderless" size="sm" v-b-tooltip.top title="Eliminar" @click.prevent="removeDocument(index)"><span class="ion ion-md-close-circle"></span></b-btn>
                  </div>
              </div>
              <vue-input class="col-md-6" v-model="document.name" label="Nombre" name="name" :disabled="viewOnly" type="text" placeholder="Nombre" :error="form.errorsFor(`documents.${index}.name`)"></vue-input>
              <vue-advanced-select :disabled="viewOnly" class="col-md-6" v-model="document.type" :error="form.errorsFor(`documents.${index}.type`)" :multiple="false" :options="typeDocument" :hide-selected="false" name="type" label="Tipo" placeholder="Seleccione el tipo">
          </vue-advanced-select>
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
import VueAdvancedSelect from "@/components/Inputs/VueAdvancedSelect.vue";
import Form from "@/utils/Form.js";

export default {
  components: {
    VueInput,
    VueAdvancedSelect
  },
  props: {
    url: { type: String },
    method: { type: String },
    cancelUrl: { type: [String, Object], required: true },
    isEdit: { type: Boolean, default: false },
    viewOnly: { type: Boolean, default: false },
    typeDocument: {
      type: Array,
      default: function() {
        return [];
      }
    },
    activity: {
      default() {
        return {
            name: '',
            documents: [
            ],
        };
      }
    }
  },
  watch: {
    activity() {
      this.loading = false;
      this.form = Form.makeFrom(this.activity, this.method);
    }
  },
  data() {
    return {
      loading: this.isEdit,
      form: Form.makeFrom(this.activity, this.method),
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
          this.$router.push({ name: "legalaspects-contracts-activities" });
        })
        .catch(error => {
          this.loading = false;
        });
    }
  }
};
</script>
