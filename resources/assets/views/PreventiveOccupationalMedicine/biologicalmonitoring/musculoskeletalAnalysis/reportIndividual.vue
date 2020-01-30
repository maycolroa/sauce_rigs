<template>
  <div>
    <header-module
        title="ANÁLISIS OSTEOMUSCULAR"
        subtitle="REPORTE INDIVIDUAL"
        url="biologicalmonitoring-musculoskeletalanalysis"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
            <b-row align-h="end" style="padding-bottom: 15px;">
                <b-col cols="1">
                    <b-btn variant="default" :to="{name: 'biologicalmonitoring-musculoskeletalanalysis'}">Atras</b-btn>
                </b-col>
            </b-row>
            <b-row>
                <b-col>
                    <b-card bg-variant="transparent" border-variant="dark" title="" class="mb-3 box-shadow-none">
                        <vue-ajax-advanced-select class="col-md-12" v-model="patient_identification"  name="patient_identification" label="Paciente" placeholder="Seleccione el paciente" :url="patienteDataUrl">
                            </vue-ajax-advanced-select>
                    </b-card>
                </b-col>
            </b-row>
            <b-row>
                <b-col>
                    <b-card no-body class="mb-2 border-secondary" :key="key" style="width: 100%;" template v-for="(item, key) in dataAnalysis">
                        <b-card-header class="bg-secondary">
                            <b-row>
                                <b-col cols="10" class="d-flex justify-content-between"> Fecha: {{ item.date }}</b-col>
                                <b-col cols="2">
                                    <div class="float-right">
                                        <b-button-group>
                                            <b-btn href="javascript:void(0)" v-b-toggle="`accordion-${key}`" variant="link">
                                                <span class="collapse-icon"></span>
                                            </b-btn>
                                        </b-button-group>
                                    </div>
                                </b-col>
                            </b-row>
                        </b-card-header>
                        <b-collapse :id="`accordion-${key}`" visible :accordion="`accordion`">
                            <b-card-body>
                                <musculoskeletal-analysis-form
                                    :analisy="item"
                                    :view-only="true"
                                    :in-form="false"
                                    :cancel-url="{ name: 'biologicalmonitoring-musculoskeletalanalysis'}"/>
                            </b-card-body>
                        </b-collapse>
                    </b-card>
                </b-col>
            </b-row>
            <b-form-row v-if="patient_identification">
                <div class="col-md-12" style="padding-bottom: 20px;">
                    <tracing-inserter
                        :key="keyTrancing"
                        :disabled="false"
                        :old-tracings="oldTracings"
                        ref="tracingInserter"
                    >
                    </tracing-inserter>
                </div>
                <div class="col-md-12">
                    <center>
                        <b-btn type="submit" :disabled="isLoading" variant="primary" @click.prevent="saveTracing">Guardar Seguimiento</b-btn>
                    </center>
                </div>
            </b-form-row>
            <b-row class="float-right">
                <b-col>
                    <b-btn variant="default" :to="{name: 'biologicalmonitoring-musculoskeletalanalysis'}">Atras</b-btn>
                </b-col>
            </b-row>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>

<script>
import VueAjaxAdvancedSelect from "@/components/Inputs/VueAjaxAdvancedSelect.vue";
import MusculoskeletalAnalysisForm from '@/components/PreventiveOccupationalMedicine/BiologicalMonitoring/MusculoskeletalAnalysis/MusculoskeletalAnalysisForm.vue';
import TracingInserter from '@/components/PreventiveOccupationalMedicine/BiologicalMonitoring/MusculoskeletalAnalysis/TracingInserter.vue';
import Alerts from '@/utils/Alerts.js';

export default {
    name: 'biologicalmonitoring-respiratoryanalysis-report-individual',
    metaInfo: {
        title: 'Análisis Osteomuscular - Reporte Individual'
    },
    components:{
        VueAjaxAdvancedSelect,
        MusculoskeletalAnalysisForm,
        TracingInserter
    },
    data () {
        return {
            isLoading: false,
            patient_identification: '',
            patienteDataUrl: '/selects/bm_musculoskeletalPacient',
            dataAnalysis: [],
            new_tracing: '',
            oldTracings: [],
            keyTrancing: true
        }
    },
    watch:{
        patient_identification () {
            if (!this.isLoading)
                this.fetch()
        },
        oldTracings() {
            this.keyTrancing = !this.keyTrancing
        }
    },
    methods: {
        fetch()
        {
            this.isLoading = true;

            axios.post('/biologicalmonitoring/musculoskeletalAnalysis/reportIndividual', {
                patient_identification: this.patient_identification
            })
            .then(data => {
                this.update(data);
                this.isLoading = false;
            })
            .catch(error => {
                this.isLoading = false;
                Alerts.error('Error', 'Hubo un problema recolectando la información');
            });
        },
        update(data) {
            _.forIn(data.data, (value, key) => {
                if (this[key]) {
                    this[key] = value;
                }
            });
        },
        saveTracing() {

            this.isLoading = true;

            axios.post('/biologicalmonitoring/musculoskeletalAnalysis/saveTracing', {
                identification: this.patient_identification,
                new_tracing: this.$refs.tracingInserter.getNewTracing(),
                oldTracings: this.$refs.tracingInserter.getOldTracings()
            })
            .then(data => {
                this.oldTracings = data.data.oldTracings;
                this.$refs.tracingInserter.refresh()
                this.isLoading = false;
                Alerts.success('Exito', 'Seguimientos actualizados');
            })
            .catch(error => {
                this.isLoading = false;
                Alerts.error('Error', 'Hubo un problema almacenando la información');
            });
        }
    }
}
</script>