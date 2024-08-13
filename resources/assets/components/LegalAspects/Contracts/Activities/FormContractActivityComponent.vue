<template>

  <b-form :action="url" @submit.prevent="submit" autocomplete="off">
    <b-form-row>
      <vue-input :disabled="viewOnly" class="col-md-12" v-model="form.name" label="Nombre" type="text" name="name" :error="form.errorsFor('name')" placeholder="Nombre"></vue-input>
    </b-form-row>

    <blockquote class="blockquote text-center">
          <p class="mb-0">Documentos Contratistas/Empleados</p>
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
              <vue-advanced-select :disabled="viewOnly" class="col-md-6" v-model="document.class" :error="form.errorsFor(`documents.${index}.class`)" :multiple="false" :options="classDocument" :hide-selected="false" name="class" label="Identificador de documento" placeholder="Seleccione el identificador">
              </vue-advanced-select>
              <vue-radio :disabled="viewOnly" class="col-md-6" v-model="document.required_expired_date" :options="siNo" :name="`required_expired_date${index}`" label="¿Requiere fecha de vencimiento?" :checked="document.required_expired_date" :error="form.errorsFor(`documents.${index}.required_expired_date`)"></vue-radio>
          </b-form-row>
      </div>
    </template>

    <b-form-row style="padding-bottom: 20px;">
      <div class="col-md-12" v-if="!viewOnly">
          <center><b-btn variant="primary" @click.prevent="addDocument()"><span class="ion ion-md-add-circle"></span>&nbsp;&nbsp;Agregar Documento</b-btn></center>
      </div>
    </b-form-row>

    <b-form-row style="padding-bottom: 20px;" v-if="message_validation">
      <div class="col-md-12" v-if="!viewOnly">
          <center><label style="color:#f0635f">No puede haber 2 documentos de la misma clase en la actividad</label></center>
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
import VueRadio from "@/components/Inputs/VueRadio.vue";

export default {
  components: {
    VueInput,
    VueAdvancedSelect,
    VueRadio
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
      classDocument: [
          {name: 'Seguridad social', value: 'Seguridad social'},
          {name: 'Certificado alturas ', value: 'Certificado alturas '},
          {name: 'Certificado espacios confinados', value: 'Certificado espacios confinados'},
          {name: 'Examen medico ocupacional', value: 'Examen medico ocupacional'},
          {name: 'Documento de identidad', value: 'Documento de identidad'},
          {name: 'Inducción - Reinducción', value: 'Inducción - Reinducción'},
          {name: 'Certificado de estudios', value: 'Certificado de estudios'},
          {name: 'Contrato', value: 'Contrato'},
          {name: 'Póliza', value: 'Póliza'},
          {name: 'Certificados contratistas(Rut Cámara y comercio)', value: 'Certificados contratistas(Rut Cámara y comercio)'},
          {name: 'Hoja de Vida', value: 'Hoja de Vida'},
          {name: 'Curso obligatorio del SGSST', value: 'Curso obligatorio del SGSST'},
          {name: 'Licencia SST', value: 'Licencia SST'},
          {name: 'Licencia conducción', value: 'Licencia conducción'},
          {name: 'Otros', value: 'Otros'},
      ],
      siNo: [
        {text: 'SI', value: 'SI'},
        {text: 'NO', value: 'NO'}
      ],
      message_validation: false,
      social_security: 0,
      certificate_altura: 0,
      certificate_confinados: 0,
      medical_exam_ocup: 0,
      doc_ident: 0,
      induction: 0,
      cerificate_studies: 0,
      contract: 0,
      poliza: 0,
      certificate_contract: 0,
      hoja_life: 0,
      curso_sgsst: 0,
      license_sst: 0,
      license_conduction: 0,
      romper: false,
      verify: false
    };
  },
  methods: {
    addDocument() {
        this.form.documents.push({
            key: new Date().getTime(),
            name: '',
            class: '',
            typeDocument: '',
            required_expired_date: ''
        })
    },
    removeDocument(index)
    {
      if (this.form.documents[index].id != undefined)
        this.form.delete.documents.push(this.form.documents[index].id)

      this.form.documents.splice(index, 1)
    },
    verifyClassDocument(documents)
    {
      this.social_security = 0;
      this.certificate_altura = 0;
      this.certificate_confinados = 0;
      this.medical_exam_ocup = 0;
      this.doc_ident = 0;
      this.induction = 0;
      this.cerificate_studies = 0;
      this.contract = 0;
      this.poliza = 0;
      this.certificate_contract = 0;
      this.hoja_life = 0;
      this.curso_sgsst = 0;
      this.license_sst = 0;
      this.license_conduction = 0;
      this.romper = false;

      _.forIn(documents, (document) => {
        if(document.class == 'Seguridad social')
        {
          if(this.social_security > 0)
            this.romper = true;
          else
            this.social_security = 1
        }
        else if(document.class == 'Certificado alturas ')
        {
          if(this.certificate_altura > 0)
            this.romper = true;
          else
            this.certificate_altura = 1
        }
        else if(document.class == 'Certificado espacios confinados')
        {
          if(this.certificate_confinados > 0)
            this.romper = true;
          else
            this.certificate_confinados = 1
        }
        else if(document.class == 'Examen medico ocupacional')
        {
          if(this.medical_exam_ocup > 0)
            this.romper = true;
          else
            this.medical_exam_ocup = 1
        }
        else if(document.class == 'Documento de identidad')
        {
          if(this.doc_ident > 0)
            this.romper = true;
          else
            this.doc_ident = 1
        }
        else if(document.class == 'Inducción - Reinducción')
        {
          if(this.induction > 0)
            this.romper = true;
          else
            this.induction = 1
        }
        else if(document.class == 'Certificado de estudios')
        {
          if(this.cerificate_studies > 0)
            this.romper = true;
          else
            this.cerificate_studies = 1
        }
        else if(document.class == 'Contrato')
        {
          if(this.contract > 0)
            this.romper = true;
          else
            this.contract = 1
        }
        else if(document.class == 'Póliza')
        {
          if(this.poliza > 0)
            this.romper = true;
          else
            this.poliza = 1
        }
        else if(document.class == 'Certificados contratistas(Rut Cámara y comercio)')
        {
          if(this.certificate_contract > 0)
            this.romper = true;
          else
            this.certificate_contract = 1
        }
        else if(document.class == 'Hoja de Vida')
        {
          if(this.hoja_life > 0)
            this.romper = true;
          else
            this.hoja_life = 1
        }
        else if(document.class == 'Curso obligatorio del SGSST')
        {
          if(this.curso_sgsst > 0)
            this.romper = true;
          else
            this.curso_sgsst = 1
        }
        else if(document.class == 'Licencia SST ')
        {
          if(this.license_sst > 0)
            this.romper = true;
          else
            this.license_sst = 1
        }
        else if(document.class == 'Licencia conducción')
        {
          if(this.license_conduction > 0)
            this.romper = true;
          else
            this.license_conduction = 1
        }
      });

      return this.romper;
    },
    submit(e) {
      this.loading = true;
      this.verify = this.verifyClassDocument(this.form.documents)

      if (this.verify)
      {
        this.message_validation = true;
        this.loading = false;
      }
      else
      {
        this.message_validation = false;
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
  }
};
</script>
