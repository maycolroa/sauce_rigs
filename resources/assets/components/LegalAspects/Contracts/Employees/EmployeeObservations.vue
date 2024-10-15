<template>
    <div class="observation-inserter">
        <div v-if="!disabled" class="observation">
            <template v-for="(observation, index) in newObservations">
                <div :key="observation.key">
                    <b-form-row>
                        <div class="col-md-12" v-if="!disabled">
                            <div class="float-right">
                                <b-btn variant="outline-primary icon-btn borderless" size="sm" v-b-tooltip.top title="Eliminar" @click.prevent="remove(index)"><span class="ion ion-md-close-circle"></span></b-btn>
                            </div>
                        </div>
                    </b-form-row>
                    <vue-textarea class="col-md-12" v-model="observation.description" label="Nuevo Registro" name="new_observation"></vue-textarea>
                </div>
            </template>
            <div class="col-md-12 text-center" style="padding-top: 15px;">
                <center><b-btn variant="primary" @click.prevent="add">Agregar</b-btn></center>
            </div>
            <br>
        </div>
        <div v-show="!showNoObservationsRegisteredLabel" class="col-md-12 text-center" style="padding-bottom: 10px;">
            <center><b-btn variant="primary" @click.prevent="showOldRegisters()">{{ oldObservationsHidden ? 'Mostrar registros pasados' : 'Ocultar registros pasados' }}</b-btn></center>
        </div>
        <div v-show="showOld" class="observation" v-for="(observation, key) in observations" :key="key">
            <vue-textarea
                name="observation"
                class="col-md-12"
                v-model="observation.description"
                :label="`${observation.made_by} / ${observation.updated_at}`"
                :disabled="true"
            >
            </vue-textarea>
        </div>
        <h5 v-if="showNoObservationsRegisteredLabel" class="col-md-12 text-center">No hay información registrada</h5>
    </div>
</template>

<script>
import VueTextarea from "@/components/Inputs/VueTextarea.vue";

export default {
    props: {
        oldObservations: {
            type: Array,
            default() {
                return [];
            }
        },
        disabled: {
            type: Boolean,
            default: false
        },
        editableObservations: {
            type: Boolean, default: false
        },
        employeeId: { 
            type: Number, 
            default: 0
        },
        userContract: {
            type: Boolean,
            default: false
        },
    },
    components: {
        VueTextarea
    },
    computed: {
        showNoObservationsRegisteredLabel() {
            return this.userContract && !this.observations.length;
        }
    },
    data () {
        return {
            newObservations: [],
            oldObservationsHidden: true,
            observations: [],
            showOld: false,
            observOld: []
        };
    },
    methods: {
        showOldRegisters() {
            this.showOld = !this.showOld;
            this.oldObservationsHidden = !this.oldObservationsHidden;

            return !this.oldObservationsHidden;
        },
        getNewObservations() {
            return this.newObservations;
        },
        getOldObservations() {
            return this.editableObservations ? this.observations : [];
        },
        add() {
            this.newObservations.push({
                key: new Date().getTime() + Math.round(Math.random() * 10000),
                description: '',
                informant_role: ''
            });
        },
        remove(index) {
            this.newObservations.splice(index, 1);
        }
    },
    created() {
        this.oldObservations.forEach(oldObservation => {
            this.observations.push(oldObservation);
        });
    }
}
</script>