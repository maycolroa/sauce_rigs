<template>
    <div>
        <div>
            <b-btn class="btn-modals" variant="outline-info icon-btn borderless" size="xs" v-b-tooltip.top title="Archivos" @click="showModal()"><span class="ion ion-ios-archive"></span></b-btn>
        </div>

        <!-- Modal -->
        <b-modal ref="file" :hideFooter="true" id="modals-top" size="lg" class="modal-top">
            <div slot="modal-title">
                Archivos
            </div>

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
                                    <template v-for="(item, index) in value">
                                        <div :key="index">
                                            <template v-if="item.path">
                                                <center>
                                                    <div class="my-4 mx-2 text-center">
                                                        <img class="mw-100" :src="`${item.path}`" alt="Max-width 100%">
                                                    </div>
                                                </center>
                                            </template>
                                            <b-form-row v-if="!viewOnly">
                                                <div class="col-md-12">
                                                    <div class="float-right">
                                                        <b-btn v-if="!viewOnly" variant="outline-primary icon-btn borderless" size="sm" v-b-tooltip.top title="Eliminar Archivos" @click.prevent="removeFile(index)"><span class="ion ion-md-close-circle"></span></b-btn>
                                                    </div>
                                                </div>
                                            </b-form-row>
                                            <b-form-row>
                                                <vue-file-simple :disabled="viewOnly" :help-text="item.id ? `Para descargar el archivo actual, haga click <a href='/legalAspects/evaluationContract/downloadFile/${item.id}' target='blank'>aqui</a> ` : null" class="col-md-10 offset-md-1" v-model="item.file" label="Imagen (*.png, *.jpg, *.jpeg)" name="file" :error="form.errorsFor(`${prefixIndex}.${index}.file`)" placeholder="Seleccione una imagen" :maxFileSize="10"></vue-file-simple>
                                            </b-form-row>
                                            <hr class="border-light container-m--x mt-0 mb-4">
                                        </div>
                                    </template>
                                    <blockquote class="blockquote text-center" v-if="value.length == 0">
                                        <p class="mb-0">No hay registros</p>
                                    </blockquote>
                                </perfect-scrollbar>
                            </b-form-row>
                        </b-card-body>
                    </b-card>
                </div>
            </div>
            <br>
            <div class="row float-right pt-12 pr-12y">
                <b-btn variant="primary" @click="hideModal()">Cerrar</b-btn>
            </div>
        </b-modal>
    </div>
</template>

<style>
</style>

<script>
import { SweetModal, SweetModalTab } from 'sweet-modal-vue';
import VueFileSimple from "@/components/Inputs/VueFileSimple.vue";
import PerfectScrollbar from '@/vendor/libs/perfect-scrollbar/PerfectScrollbar';

export default {
    name: 'modals-files',
    components:{
        SweetModal,
        VueFileSimple,
        PerfectScrollbar
    },
    props: {
        value: {type: [Array], default:[]},
        viewOnly: { type: Boolean, default: false },
        prefixIndex: { type: String, default: ''},
        form: { type: Object, required: true },
        itemId: { type: Number, required: true },
    },
    data () {
        return {}
    },
    methods: {
        showModal () {
            this.$refs.file.show()
        },
        hideModal () {
            this.$refs.file.hide()
        },
        addFile() {
            this.value.push({
                file: '',
                item_id: this.itemId
            })
        },
        removeFile(index) {
            if (this.value[index].id != undefined)
                this.$emit('removeFile', this.value[index].id);

            this.value.splice(index, 1)
        },
    }
}
</script>