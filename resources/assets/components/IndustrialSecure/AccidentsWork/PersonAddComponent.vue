<template>
    <div>
        <b-form-row>
            <div class="col-md-12" v-if="!viewOnly">
            <div class="float-right" style="padding-top: 10px;">
                <b-btn variant="primary" @click.prevent="addInterviewed()"><span class="ion ion-md-add-circle"></span>&nbsp;&nbsp;Agregar Persona</b-btn>
            </div>
            </div>
        </b-form-row>

        <b-form-row style="padding-top: 15px;">
            <perfect-scrollbar :options="{ wheelPropagation: true }" class="mb-4" style="height: 300px; padding-right: 15px; width: 100%;">
            <b-card no-body class="mb-2 border-secondary" style="width: 100%;">
                <b-card-header class="bg-secondary">
                    <b-row>
                        <b-col cols="10" class="d-flex justify-content-between"> Personas </b-col>
                        <b-col cols="2">
                            <div class="float-right">
                                <b-button-group>
                                <b-btn href="javascript:void(0)" v-b-toggle="'accordion-1'" variant="link">
                                    <span class="collapse-icon"></span>
                                </b-btn>
                                </b-button-group>
                            </div>
                        </b-col>
                    </b-row>
                </b-card-header>
                <b-collapse id="accordion-1" visible accordion="accordion-1">
                <b-card-body>
                    <template v-for="(item, index) in form.persons">
                    <div :key="index">
                        <b-form-row>
                            <div class="col-md-12" v-if="!viewOnly">
                            <div class="float-right">
                                <b-btn v-if="!viewOnly" variant="outline-primary icon-btn borderless" size="sm" v-b-tooltip.top title="Eliminar Persona" @click.prevent="removeInterviewed(index)"><span class="ion ion-md-close-circle"></span></b-btn>
                            </div>
                            </div>
                        </b-form-row>
                        <b-form-row>
                            <vue-input :disabled="viewOnly" class="col-md-6" v-model="item.name" label="Nombre" type="text" name="name" :error="form.errorsFor('name')" placeholder="Nombre"></vue-input>
                            <vue-radio :disabled="viewOnly" :checked="item.type_document" class="col-md-6" v-model="item.type_document" :options="typesDocuments" name="type_document" :error="form.errorsFor('type_document')" label="Tipo de identificación"></vue-radio>
                        </b-form-row>
                        <b-form-row>
                            <vue-input :disabled="viewOnly" class="col-md-6" v-model="item.document" label="Número de identificación" type="text" name="document" :error="form.errorsFor('document')" placeholder="Documento"></vue-input>
                            <vue-input :disabled="viewOnly" class="col-md-6" v-model="item.cargo" label="Cargo" type="text" name="cargo" :error="form.errorsFor('cargo')" placeholder="Cargo"></vue-input>
                        </b-form-row>
                        <hr class="border-light container-m--x mt-0 mb-4">
                    </div>
                    </template>
                </b-card-body>
                </b-collapse>
            </b-card>
            </perfect-scrollbar>
        </b-form-row>          
    </div>
</template>


<script>
import VueAjaxAdvancedSelect from "@/components/Inputs/VueAjaxAdvancedSelect.vue";
import VueAdvancedSelect from "@/components/Inputs/VueAdvancedSelect.vue";
import VueInput from "@/components/Inputs/VueInput.vue";
import VueDatepicker from "@/components/Inputs/VueDatepicker.vue";
import VueRadio from "@/components/Inputs/VueRadio.vue";
import Form from "@/utils/Form.js";
import PerfectScrollbar from '@/vendor/libs/perfect-scrollbar/PerfectScrollbar';

export default {
  components: {
    VueAdvancedSelect,
    VueAjaxAdvancedSelect,
    VueDatepicker,
    VueInput,
    VueRadio,
    PerfectScrollbar
  },
  props:{
    viewOnly: { type: Boolean, default: false },
    form: { type: Object, required: true },
    isEdit: { type: Boolean, default: false },
    rol: { type: String, default: '' },
    persons: {
      default() {
        return {
            persons: [],
            delete: []
        }
      }
    },
  },
  watch: {
    persons() {
      this.loading = false;
      this.$emit('input', this.persons);
    }
  },
  data() {
    return {
      typesDocuments: [
        {text: 'NI', value: 'NI'},
        {text: 'CC', value: 'CC'},
        {text: 'CE', value: 'CE'},
        {text: 'NU', value: 'NU'},
        {text: 'PA', value: 'PA'},
      ],
    }
  },
  methods: {
    addInterviewed() {
        this.form.persons.push({
            name: '',
            cargo: '',
            document: '',
            type_document: '',
            rol:this.rol
        })
    },
    removeInterviewed(index)
    {
      if ((this.form.persons.length > 1 && this.rol == 'Miembro Investigación') || this.rol == 'Presencio Accidente')
      {
        if (this.form.persons[index].id != undefined)
          this.form.delete.push(this.form.persons[index].id)

        this.form.persons.splice(index, 1)
      }
    },
  }
}
</script>
