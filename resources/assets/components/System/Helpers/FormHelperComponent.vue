<template>

  <b-form :action="url" @submit.prevent="submit" autocomplete="off">
    <b-form-row>
      <vue-input :disabled="viewOnly" class="col-md-6" v-model="form.title" label="Título" type="text" name="title" :error="form.errorsFor('title')" placeholder="Título"></vue-input>
      <vue-textarea :disabled="viewOnly" class="col-md-6" v-model="form.description" label="Descripción" type="text" name="description" :error="form.errorsFor('description')" placeholder="Abreviatura"></vue-textarea>
    </b-form-row>
    <b-form-row>
       <vue-ajax-advanced-select :disabled="viewOnly" class="col-md-12" v-model="form.module_id" :error="form.errorsFor('module_id')" :selected-object="form.multiselect_module" name="module_id" label="Módulo" placeholder="Seleccione una opción" :url="modulesDataUrl"> </vue-ajax-advanced-select>
    </b-form-row>

    <b-card no-body class="mb-2 border-secondary" style="width: 100%;">
      <b-card-header class="bg-secondary">
        <b-row>
          <b-col cols="11" class="d-flex justify-content-between">Archivo</b-col>
          <b-col cols="1">
            <div class="float-right">
              <b-button-group>
                <b-btn href="javascript:void(0)" v-b-toggle="'accordion-file'" variant="link">
                  <span class="collapse-icon"></span>
                </b-btn>
              </b-button-group>
            </div>
          </b-col>
        </b-row>
      </b-card-header>
      <b-collapse :id="`accordion-file`" visible :accordion="`accordion-master`">
      <b-card-body>
        <template v-for="(file, index) in form.files">
          <div :key="file.key">
              <b-form-row>
                <!--<div class="col-md-12">
                    <div class="float-right">
                        <b-btn variant="outline-primary icon-btn borderless" size="sm" v-b-tooltip.top title="Eliminar" @click.prevent="removeFile(index)"><span class="ion ion-md-close-circle"></span></b-btn>
                    </div>
                </div>-->
                <vue-input class="col-md-6" v-model="file.name" label="Nombre" name="name" type="text" placeholder="Nombre" :error="form.errorsFor(`files.${index}.name`)"></vue-input>
                <vue-file-simple :disabled="viewOnly" :help-text="file.id ? `Para descargar el archivo actual, haga click <a href='/system/helpers/download/${file.id}' target='blank'>aqui</a> ` : null" class="col-md-12" v-model="file.file" label="Archivo" name="file" placeholder="Seleccione un archivo" :error="form.errorsFor(`file`)" :maxFileSize="20"/>
              </b-form-row>
          </div>
        </template>

        <!--<b-form-row style="padding-bottom: 20px;">
          <div class="col-md-12">
              <center><b-btn v-if="!viewOnly" variant="primary" @click.prevent="addFile()"><span class="ion ion-md-add-circle"></span>&nbsp;&nbsp;Agregar</b-btn></center>
          </div>
        </b-form-row> -->     
      </b-card-body>
      </b-collapse>
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
import VueInput from "@/components/Inputs/VueInput.vue";
import VueTextarea from "@/components/Inputs/VueTextarea.vue";
import VueFileSimple from "@/components/Inputs/VueFileSimple.vue";
import VueAdvancedSelect from "@/components/Inputs/VueAdvancedSelect.vue";
import VueAjaxAdvancedSelect from "@/components/Inputs/VueAjaxAdvancedSelect.vue";
import Form from "@/utils/Form.js";

export default {
  components: {
    VueInput,
    VueTextarea,
    VueFileSimple,
    VueAdvancedSelect,
    VueAjaxAdvancedSelect
  },
  props: {
    url: { type: String },
    method: { type: String },
    cancelUrl: { type: [String, Object], required: true },
    isEdit: { type: Boolean, default: false },
    viewOnly: { type: Boolean, default: false },
    modulesDataUrl: { type: String, default: "" },
    help: {
      default() {
        return {
            title: '',
            description: '',
            module_id: '',
            files: [
              {
                key: new Date().getTime(),
                name: '',
                file: '',
              }
            ],
            delete: []
        };
      }
    }
  },
  watch: {
    help() {
      this.loading = false;
      this.form = Form.makeFrom(this.help, this.method);
    }
  },
  data() {
    return {
      loading: this.isEdit,
      form: Form.makeFrom(this.help, this.method),
    };
  },
  methods: {
    submit(e) {
      this.loading = true;

       this.form.clearFilesBinary();
        _.forIn(this.form.files, (file, keyFile) => {
          if (file.file)
            this.form.addFileBinary(`${keyFile}`, file.file);
        });

      this.form
        .submit(e.target.action)
        .then(response => {
          this.loading = false;
          this.$router.push({ name: "system-helpers" });
        })
        .catch(error => {
          this.loading = false;
        });
    },
    addFile() 
    {
      this.form.files.push({
          key: new Date().getTime(),
          name: '',
          file: '',
      })
    },
	  removeFile(index)
    {
      if (this.form.files[index].id != undefined)
        this.form.delete.files.push(this.form.files[index].id)

      this.form.files.splice(index, 1)
    },

  }
};
</script>
