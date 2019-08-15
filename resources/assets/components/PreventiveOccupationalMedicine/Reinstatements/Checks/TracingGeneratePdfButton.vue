<template>
    <div>
        <div class="text-center">
            <center><b-btn variant="primary" @click="showModal()">Generar Carta</b-btn></center>
        </div>
        <b-modal ref="tracingGeneratePdf" :hideFooter="true" id="tracingGeneratePdf" class="modal-top" size="lg">
            <div slot="modal-title">
                {{ tracing.made_by }} / {{ tracing.updated_at }}
            </div>

            <b-card  bg-variant="transparent"  title="" class="mb-3 box-shadow-none">
                <b-form-row>
                    <vue-radio class="col-md-12" v-model="has_tracing" :checked="has_tracing" :options="siNo" :name="`has_tracing${Math.round(Math.random() * 10000)}`" label="¿Requiere nuevo seguimiento?"></vue-radio>
                </b-form-row>
                <b-form-row>
                    <vue-datepicker v-show="has_tracing == 'SI'" class="col-md-6 offset-md-3" v-model="new_date_tracing" label="Fecha Propuesta" :full-month-name="true"  name="new_date_tracing">
                  </vue-datepicker>
                </b-form-row>
            </b-card>
            <br>
            <div class="row float-right pt-12 pr-12y">
                <b-btn variant="default" @click="hideModal()">Cerrar</b-btn>&nbsp;&nbsp;
                <b-btn variant="primary" @click="generate()">Generar</b-btn>
            </div>
        </b-modal>
    </div>
</template>

<script>
import VueRadio from "@/components/Inputs/VueRadio.vue";
import VueDatepicker from "@/components/Inputs/VueDatepicker.vue";

export default {
    props: {
        tracing: {
            type: Object,
            default() {
                return {
                    made_by: 'Nuevo Seguimiento',
                    updated_at: ''
                }
            }
        },
        tracingDescription: {
            type: String,
            required: true
        },
        siNo: {
            type: Array,
            default: function() {
                return [];
            }
        },
        checkId: {
            type: Number,
            required: true
        }
    },
    components: {
        VueRadio,
        VueDatepicker
    },
    data() {
        return {
            has_tracing: 'NO',
            new_date_tracing: ''
        }
    },
    methods: {
        generate() {
            
            window.open(`/biologicalmonitoring/reinstatements/check/generateTracing?check_id=${this.checkId}&tracing_description=${this.tracingDescription.replace(/\r?\n/g, "<br>")}&has_tracing=${this.has_tracing}&new_date_tracing=${this.new_date_tracing}`, '_blank')

            this.hideModal()
        },
        showModal() {
			this.$refs.tracingGeneratePdf.show()
		},
		hideModal() {
			this.$refs.tracingGeneratePdf.hide()
		},
    }
}
</script>