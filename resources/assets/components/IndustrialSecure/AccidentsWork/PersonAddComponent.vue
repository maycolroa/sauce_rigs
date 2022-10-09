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
                  <template v-for="(item, index) in persons.persons">
                    <div :key="index">
                        <b-form-row>
                            <div class="col-md-12" v-if="!viewOnly">
                              <div class="float-right">
                                  <b-btn v-if="!viewOnly" variant="outline-primary icon-btn borderless" size="sm" v-b-tooltip.top title="Eliminar Persona" @click.prevent="removeInterviewed(index)"><span class="ion ion-md-close-circle"></span></b-btn>
                              </div>
                            </div>
                        </b-form-row>
                        <b-form-row v-if="rol == 'Miembro Investigación'">
                            <vue-ajax-advanced-select-tag-unic :disabled="viewOnly" class="col-md-12" v-model="item.type_rol" name="type_rol" :error="form.errorsFor('type_rol')" label="Rol" placeholder="Seleccione el rol" :url="tagsRolesAWDataUrl" :multiple="false" :allowEmpty="true" :taggable="true">
                              </vue-ajax-advanced-select-tag-unic>
                        </b-form-row>
                        <b-form-row>
                            <vue-input :disabled="viewOnly" class="col-md-6" v-model="item.name" label="Nombre" type="text" name="name" :error="form.errorsFor('name')" placeholder="Nombre"></vue-input>
                            <vue-radio :disabled="viewOnly" :checked="item.type_document" class="col-md-6" v-model="item.type_document" :options="typesDocuments" name="type_document" :error="form.errorsFor('type_document')" label="Tipo de identificación"></vue-radio>
                        </b-form-row>
                        <b-form-row>
                            <vue-input :disabled="viewOnly" class="col-md-6" v-model="item.document" label="Número de identificación" type="text" name="document" :error="form.errorsFor('document')" placeholder="Documento"></vue-input>
                            <vue-input :disabled="viewOnly" class="col-md-6" v-model="item.position" label="Cargo" type="text" name="position" :error="form.errorsFor('position')" placeholder="Cargo"></vue-input>
                            <vue-input hidden :disabled="viewOnly" class="col-md-6" v-model="item.rol" label="rol" type="text" name="rol" :error="form.errorsFor('rol')" placeholder="rol" :value="rol"></vue-input>
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
import VueAjaxAdvancedSelectTagUnic from "@/components/Inputs/VueAjaxAdvancedSelectTagUnic.vue";

export default {
  components: {
    VueAdvancedSelect,
    VueAjaxAdvancedSelect,
    VueDatepicker,
    VueInput,
    VueRadio,
    PerfectScrollbar,
    VueAjaxAdvancedSelectTagUnic
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
      tagsRolesAWDataUrl: '/selects/tagsRoles',
      roles: []
    }
  },
  mounted() {
    if (this.persons.persons.length < 1 && this.rol == 'Miembro Investigación')
    {
      this.roles = ['Jefe inmediato del trabajador o del área', 'Representante del COPASST / Vigía', 'Encargado del desarrollo del SG-SST', 'Profesional con licencia en Salud Ocupacional (propio o contratado)', 'Encargado del diseño de normas, procesos y/o mantenimiento.'];

      this.roles.forEach(rol => {
        this.persons.persons.push({
          name: '',
          position: '',
          document: '',
          type_document: '',
          type_rol: rol,
          rol:this.rol
        })        
      });
    }
  },
  methods: {
    addInterviewed() {
        this.persons.persons.push({
            name: '',
            position: '',
            document: '',
            type_document: '',
            type_rol: '',
            rol:this.rol
        })
    },
    removeInterviewed(index)
    {
      if (this.persons.persons.length > 1)
      {
        if (this.persons.persons[index].id != undefined)
          this.persons.delete.push(this.persons.persons[index].id)

        this.persons.persons.splice(index, 1)
      }
      else if (this.rol == 'Presencio Accidente')
      {
        if (this.persons.persons[index].id != undefined)
          this.persons.delete.push(this.persons.persons[index].id)

        this.persons.persons.splice(index, 1)
      }
    },
  }
}
</script>
