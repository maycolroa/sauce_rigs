<template>
      <div>
          <b-form-row>
            <vue-textarea class="col-md-12" :disabled="viewOnly" v-model="obs.observaciones_empresa" label="Observaciones de la empresa (Equipo de salud ocupacional, jefe inmediato y comité paritario)" name="observaciones_empresa" placeholder="Observaciones de la empresa (Equipo de salud ocupacional, jefe inmediato y comité paritario)" rows="4"></vue-textarea> 
          </b-form-row>  
        <label class="col-md-12">Registro visual (Formato permitidos: jpg, jpeg, png)</label>
        <b-card bg-variant="transparent" border-variant="dark" title="" class="mb-3 box-shadow-none">
            <div class="row" style="padding-bottom: 10px;">
                <div class="col-md">
                    <b-card no-body>
                        <b-card-body>
                            <b-form-row>
                                <div class="col-md-12" v-if="!viewOnly">
                                <div class="float-right">
                                    <b-btn variant="primary" @click.prevent="addFile()"><span class="ion ion-md-add-circle"></span>&nbsp;&nbsp;Agregar</b-btn>
                                </div>
                                </div>
                            </b-form-row>

                            <b-form-row style="padding-top: 15px;">
                                <perfect-scrollbar :options="{ wheelPropagation: true }" class="mb-4" style="height: 300px; padding-right: 15px; width: 100%;">
                                    <template v-for="(item, index) in obs.files">
                                        <div :key="index">
                                            <center>
                                                <div class="my-4 mx-2 text-center" v-if="item.path">
                                                    <img class="mw-100" :src="`${item.path}`" alt="Max-width 100%">
                                                </div>
                                            </center>
                                            <b-form-row v-if="!viewOnly">
                                                <div class="col-md-12">
                                                    <div class="float-right">
                                                        <b-btn v-if="!viewOnly" variant="outline-primary icon-btn borderless" size="sm" v-b-tooltip.top title="Eliminar Archivos" @click.prevent="removeFile(index)"><span class="ion ion-md-close-circle"></span></b-btn>
                                                    </div>
                                                </div>
                                            </b-form-row>
                                            <b-form-row>
                                                <vue-file-simple :disabled="viewOnly" :help-text="item.id ? `Para descargar el archivo actual, haga click <a href='/legalAspects/evaluationContract/downloadFile/${item.id}' target='blank'>aqui</a> ` : null" class="col-md-10 offset-md-1" v-model="item.file" label="Archivo (*.png, *.jpg, *.jpeg)" name="file" :error="form.errorsFor(`obs.files.${index}.file`)" placeholder="Seleccione un archivo" :maxFileSize="10"></vue-file-simple>
                                            </b-form-row>
                                            <hr class="border-light container-m--x mt-0 mb-4">
                                        </div>
                                    </template>
                                    <blockquote class="blockquote text-center" v-if="form.files.length == 0">
                                        <p class="mb-0">No hay registros</p>
                                    </blockquote>
                                </perfect-scrollbar>
                            </b-form-row>
                        </b-card-body>
                    </b-card>
                </div>
            </div>         
        </b-card>
      </div>
</template>


<script>
import Form from "@/utils/Form.js";
import VueTextarea from "@/components/Inputs/VueTextarea.vue";
import VueFileSimple from "@/components/Inputs/VueFileSimple.vue";
import PerfectScrollbar from '@/vendor/libs/perfect-scrollbar/PerfectScrollbar';

export default {
  components: {
    PerfectScrollbar,
    VueTextarea,
    VueFileSimple
  },
  props:{
    viewOnly: { type: Boolean, default: false },
    form: { type: Object, required: true },
    isEdit: { type: Boolean, default: false },
    obs: {
      default() {
        return {
          observaciones_empresa: '',
          files: []
        }
      }
    },
  },
  watch: {
    obs() {
      this.loading = false;
      this.$emit('input', this.obs);
    }
  },
  data() {
    return {
    }
  },
  methods: {
    showModal () {
        this.$refs.file.show()
    },
    hideModal () {
        this.$refs.file.hide()
    },
    addFile() {
        this.obs.files.push({
            file: '',
            item_id: this.itemId
        })
    },
    removeFile(index) {
        if (this.obs.files[index].id != undefined)
            this.$emit('removeFile', this.obs.files[index].id);

        this.obs.files.splice(index, 1)
    },
    removed() {
        let keys = [];

        this.obs.files.forEach((file, keyObs) => {
            if (file.file)
            {
                keys.push(file);
            }
        });

        this.obs.files.splice(0);

        keys.forEach((item, key) => {
            this.obs.files.push(item)
        });
    }
  }
}
</script>
