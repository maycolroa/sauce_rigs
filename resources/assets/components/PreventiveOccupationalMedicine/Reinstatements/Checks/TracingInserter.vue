<template>
    <div class="tracing-inserter">
        <h5 class="col-md-12 text-center">{{ label }}</h5>
        <div v-if="!disabled" class="tracing">
            <template v-for="(tracing, index) in newTracing">
                <div :key="tracing.key">
                    <b-form-row>
                        <div class="col-md-12" v-if="!disabled">
                            <div class="float-right">
                                <b-btn variant="outline-primary icon-btn borderless" size="sm" v-b-tooltip.top title="Eliminar" @click.prevent="remove(index)"><span class="ion ion-md-close-circle"></span></b-btn>
                            </div>
                        </div>
                    </b-form-row>
                    <vue-textarea class="col-md-12" v-model="tracing.description" label="Nuevo Registro" name="new_tracing"></vue-textarea>
                    <b-form-row v-if="config == 'harinera'">
                        <vue-ajax-advanced-select-tag-unic class="col-md-12" v-model="tracing.informant_role" name="informant_role"  label="Rol informante" placeholder="Seleccione el rol del informante" :url="tagsInformantRoleDataUrl" :multiple="false" :allowEmpty="true" :taggable="true">
                                </vue-ajax-advanced-select-tag-unic>
                    </b-form-row>
                    <tracing-generate-pdf-button
                        v-if="generatePdf && checkId"
                        :tracing-description="tracing.description"
                        :si-no="siNo"
                        :check-id="checkId"
                    />
                </div>
            </template>
            <div class="col-md-12 text-center" style="padding-top: 15px;">
                <center><b-btn variant="primary" @click.prevent="add">Agregar</b-btn></center>
            </div>
            <br>
        </div>
        <div v-if="showButtonTracings" class="col-md-12 text-center" style="padding-bottom: 10px;">
            <center><b-btn variant="primary" @click.prevent="oldTracingsHidden = !oldTracingsHidden">{{ oldTracingsHidden ? 'Mostrar registros pasados' : 'Ocultar registros pasados' }}</b-btn></center>
        </div>
        <div v-show="showOldTracings" class="tracing" v-for="(tracing, key) in tracings" :key="key">
            <vue-textarea
                name="tracing"
                class="col-md-12"
                v-model="tracing.description"
                :label="`${tracing.made_by} / ${tracing.updated_at}`"
                :disabled="disabled || !editableTracings"
            >
            </vue-textarea>
            <b-form-row v-if="config == 'harinera'">
                <vue-ajax-advanced-select-tag-unic class="col-md-12" v-model="tracing.informant_role" :disabled="disabled || !editableTracings" name="informant_role"  label="Rol informante" placeholder="Seleccione el rol del informante" :url="tagsInformantRoleDataUrl" :multiple="false" :allowEmpty="true" :taggable="true">
                        </vue-ajax-advanced-select-tag-unic>
            </b-form-row>
            <tracing-generate-pdf-button
                v-if="generatePdf"
                :tracing="tracing"
                :tracing-description="tracing.description"
                :si-no="siNo"
                :check-id="checkId"
                style="padding-bottom: 10px;"
            />
        </div>
        <h5 v-if="showNoTracingsRegisteredLabel" class="col-md-12 text-center">No hay información registrada</h5>
    </div>
</template>

<script>
import VueTextarea from "@/components/Inputs/VueTextarea.vue";
import TracingGeneratePdfButton from './TracingGeneratePdfButton.vue';
import VueAjaxAdvancedSelectTagUnic from "@/components/Inputs/VueAjaxAdvancedSelectTagUnic.vue";

export default {
    props: {
        config: {
            type: String,
            default: ''
        },
        label: {
            type: String,
            default: 'Seguimiento'
        },
        oldTracings: {
            type: Array,
            default() {
                return [];
            }
        },
        disabled: {
            type: Boolean,
            default: false
        },
        editableTracings: {
            type: Boolean, default: false
        },
        siNo: {
            type: Array,
            default: function() {
                return [];
            }
        },
        checkId: { 
            type: Number, 
            default: 0
        },
        generatePdf: {
            type: Boolean,
            default: true
        },
        tagsInformantRoleDataUrl: {
            type: String,
            default: ''
        }
    },
    components: {
        VueTextarea,
        TracingGeneratePdfButton,
        VueAjaxAdvancedSelectTagUnic
    },
    computed: {
        showOldTracings() {
            if (this.disabled) {
                return true;
            }
            return !this.oldTracingsHidden;
        },
        showButtonTracings() {
            if (this.disabled) {
                return false;
            }
            return this.oldTracings.length;
        },
        showNoTracingsRegisteredLabel() {
            return this.disabled && !this.oldTracings.length;
        }
    },
    data () {
        return {
            newTracing: [],
            oldTracingsHidden: true,
            tracings: []
        };
    },
    methods: {
        getNewTracing() {
            return this.newTracing;
        },
        getOldTracings() {
            return this.editableTracings ? this.tracings : [];
        },
        add() {
            this.newTracing.push({
                key: new Date().getTime() + Math.round(Math.random() * 10000),
                description: '',
                informant_role: ''
            });
        },
        remove(index) {
            this.newTracing.splice(index, 1);
        }
    },
    created() {
        this.oldTracings.forEach(oldTracing => {
            this.tracings.push(oldTracing);
        });
    }
}
</script>