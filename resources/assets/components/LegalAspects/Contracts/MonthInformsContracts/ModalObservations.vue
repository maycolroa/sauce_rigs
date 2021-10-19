<template>
    <div>
        <div>
            <b-btn class="btn-modals" variant="outline-info icon-btn borderless" size="xs" v-b-tooltip.top title="Observaciones" @click="showModal()"><span class="ion ion-md-eye"></span></b-btn>
        </div>

        <!-- Modal -->
        <b-modal ref="observations" :hideFooter="true" id="modals-top" size="lg" class="modal-top" @hidden="removed">
            <div slot="modal-title">
                Observaciones
            </div>

            <div class="row" style="padding-bottom: 10px;">
                <div class="col-md">
                    <b-card no-body>
                        <b-card-body>
                            <b-form-row>
                                <div class="col-md-12" v-if="!viewOnly">
                                <div class="float-right">
                                    <b-btn variant="primary" @click.prevent="addObservation()"><span class="ion ion-md-add-circle"></span>&nbsp;&nbsp;Agregar</b-btn>
                                </div>
                                </div>
                            </b-form-row>

                            <b-form-row style="padding-top: 15px;">
                                <perfect-scrollbar :options="{ wheelPropagation: true }" class="mb-4" style="height: 300px; padding-right: 15px; width: 100%;">
                                    <template v-for="(item, index) in value">
                                        <div :key="index">
                                            <b-form-row v-if="!viewOnly">
                                                <div class="col-md-12">
                                                    <div class="float-right">
                                                        <b-btn v-if="!viewOnly" variant="outline-primary icon-btn borderless" size="sm" v-b-tooltip.top title="Eliminar Observación" @click.prevent="removeObservation(index)"><span class="ion ion-md-close-circle"></span></b-btn>
                                                    </div>
                                                </div>
                                            </b-form-row>
                                            <b-form-row>
                                                <vue-textarea :disabled="viewOnly" class="col-md-12" v-model="item.description" label="Descripción" name="description" placeholder="Descripción" rows="3" :error="form.errorsFor(`${prefixIndex}.${index}.description`)"></vue-textarea>
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
import VueTextarea from "@/components/Inputs/VueTextarea.vue";
import PerfectScrollbar from '@/vendor/libs/perfect-scrollbar/PerfectScrollbar';

export default {
    name: 'modals-observations',
    components:{
        SweetModal,
        VueTextarea,
        PerfectScrollbar
    },
    props: {
        value: {
          type: Array,
          default: function() {
            return [];
          }
        },
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
            this.$refs.observations.show()
        },
        hideModal () {
            this.$refs.observations.hide()
        },
        addObservation() {
            this.value.push({
                description: '',
                item_id: this.itemId
            })
        },
        removeObservation(index) {
            if (this.value[index].id != undefined)
                this.$emit('removeObservation', this.value[index].id);

            this.value.splice(index, 1)
        },
        removed() {
            let keys = [];

            this.value.forEach((observation, keyObs) => {
              if (observation.description)
              {
                keys.push(observation);
              }
            });

            this.value.splice(0);

            keys.forEach((item, key) => {
                this.value.push(item)
            });
        }
    }
}
</script>