<template>
    <div class="tracing-inserter">
        <h5 class="col-md-12 text-center">{{ label }} de otros reportes</h5>
        <div v-if="showButtonTracings" class="col-md-12 text-center" style="padding-bottom: 20px;">
            <center><b-btn variant="primary" @click.prevent="oldTracingsHidden = !oldTracingsHidden">{{ oldTracingsHidden ? 'Mostrar registros' : 'Ocultar registros' }}</b-btn></center>
        </div>
        <div v-show="showOldTracings" class="tracing" v-for="(tracing, key) in oldTracings" :key="key">
            <vue-textarea
                name="tracing"
                class="col-md-12"
                v-model="tracing.description"
                :label="`${tracing.made_by} / ${tracing.updated_at} / ${tracing.diagnosis} / ${tracing.disease_origin}`"
                :disabled="true"
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
        }
    },
    components: {
        VueTextarea
    },
    computed: {
        showOldTracings() {
            return !this.oldTracingsHidden;
        },
        showNoTracingsRegisteredLabel() {
            return !this.oldTracings.length;
        },
        showButtonTracings() {
            return this.oldTracings.length;
        }
    },
    data () {
        return {
            oldTracingsHidden: true,
            tracings: []
        };
    },
    created() {
        this.oldTracings.forEach(oldTracing => {
            this.tracings.push(oldTracing);
        });
    }
}
</script>