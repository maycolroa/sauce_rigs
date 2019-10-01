<template>
    <div class="tracing-inserter">
        <h5 class="col-md-12 text-center">{{ label }}</h5>
        <div v-if="!disabled" class="tracing">
            <vue-textarea class="col-md-12" v-model="newTracing" label="Nuevo Registro" name="new_tracing"></vue-textarea>
            <br>
        </div>
        <div v-if="showButtonTracings" class="col-md-12 text-center">
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
        </div>
        <h5 v-if="showNoTracingsRegisteredLabel" class="col-md-12 text-center">No hay información registrada</h5>
    </div>
</template>

<script>
import VueTextarea from "@/components/Inputs/VueTextarea.vue";

export default {
    props: {
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
            type: Boolean, default: true
        }
    },
    components: {
        VueTextarea
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
            newTracing: '',
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
        refresh() {
            this.tracings.splice(0)

            this.oldTracings.forEach(oldTracing => {
                this.tracings.push(oldTracing);
            });

            this.newTracing = ''
        }
    },
    created() {
        this.oldTracings.forEach(oldTracing => {
            this.tracings.push(oldTracing);
        });
    }
}
</script>