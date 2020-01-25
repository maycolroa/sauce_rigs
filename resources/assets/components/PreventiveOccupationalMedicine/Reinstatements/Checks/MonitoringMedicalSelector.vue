<template>
    <div class="monitoring-selector">
        <b-form-row>
            <div class="col-md-6">
                <label class="label"><slot name="monitoring-label"></slot></label>
            </div>
            <div class="col-md-6">
                <label class="label"><slot name="conclusion-label"></slot></label>
            </div>
        </b-form-row>

        <template v-for="(monitoring, index) in monitoringList">
            <div :key="monitoring.key">
                <b-form-row>
                    <div class="col-md-12" v-if="!disabled">
                        <div class="float-right">
                            <b-btn variant="outline-primary icon-btn borderless" size="sm" v-b-tooltip.top title="Eliminar" @click.prevent="remove(monitoring, index)"><span class="ion ion-md-close-circle"></span></b-btn>
                        </div>
                    </div>
                </b-form-row>
                <b-form-row>
                    <vue-datepicker :disabled="disabled" class="col-md-6" v-model="monitoring.set_at" label="" :full-month-name="true" name="medical_monitoring"> </vue-datepicker>
                    <vue-advanced-select :disabled="disabled" class="col-md-6" v-model="monitoring.conclusion" :multiple="false" :options="options" :hide-selected="false" name="medical_conclusion" label="">
                        </vue-advanced-select>
                    <vue-radio :disabled="disabled" :checked="monitoring.has_monitoring_content" class="col-md-6" v-model="monitoring.has_monitoring_content" :options="siNo" name="has_monitoring_content" label="¿Tiene seguimiento en el content?"></vue-radio>
                </b-form-row>
            </div>
        </template>

        <div v-if="!disabled" class="col-md-12 text-center">
            <center><b-btn variant="primary" @click.prevent="add">Agregar</b-btn></center>
        </div>
        <h5 v-if="showNoMonitoringsRegisteredLabel" class="col-md-12 text-center">No hay seguimientos registrados</h5>
    </div>
</template>

<script>
import VueAdvancedSelect from "@/components/Inputs/VueAdvancedSelect.vue";
import VueDatepicker from "@/components/Inputs/VueDatepicker.vue";
import VueRadio from "@/components/Inputs/VueRadio.vue";

export default {
    props: {
        options: { type: Array, required: true },
        monitoringRegistered: { type: Array, default() { return []; } },
        disabled: { type: Boolean, default: false },
        siNo: {
          type: Array,
          default: function() {
            return [];
        }
        }
    },
    components: {
        VueAdvancedSelect,
        VueDatepicker,
        VueRadio
    },
    computed: {
        showNoMonitoringsRegisteredLabel() {
            return this.disabled && !this.monitoringList.length;
        }
    },
    data() {
        return {
            monitoringList: []
        }
    },
    methods: {
        add() {
            this.monitoringList.push({
                key: new Date().getTime() + Math.round(Math.random() * 10000),
                set_at: '',
                conclusion: '',
                has_monitoring_content: ''
            });
        },
        remove(monitoring, index) {
            this.monitoringList.splice(index, 1);
        },
        getMonitoringList() {
            return this.monitoringList;
        },
        monitoringListIsValid() {
            let isValid = true;
            this.monitoringList.forEach(monitoring => {
                if (!monitoring.set_at || !monitoring.conclusion) {
                    isValid = false;
                }
            });
            return isValid;
        }
    },
    created() {
        this.monitoringRegistered.forEach(monitoring => {
            this.monitoringList.push({
                key: new Date().getTime() + Math.round(Math.random() * 10000),
                set_at: monitoring.set_at,
                conclusion: monitoring.conclusion,
                has_monitoring_content: monitoring.has_monitoring_content
            });
        });
    }
};
</script>