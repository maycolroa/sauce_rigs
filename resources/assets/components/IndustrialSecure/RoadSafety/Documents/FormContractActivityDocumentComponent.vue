<template>
    <div class="document-inserter">
        <h5 class="col-md-12 text-center">Documentos a solicitar</h5>
        <div v-if="!disabled" class="document">
            <template v-for="(document, index) in documents">
                <div :key="document.key">
                    <b-form-row>
                        <div class="col-md-12" v-if="!disabled">
                            <div class="float-right">
                                <b-btn variant="outline-primary icon-btn borderless" size="sm" v-b-tooltip.top title="Eliminar" @click.prevent="remove(index)"><span class="ion ion-md-close-circle"></span></b-btn>
                            </div>
                        </div>
                    </b-form-row>
                    <vue-input class="col-md-12" v-model="document.name" label="Nuevo Documento" name="documents"></vue-input>
                </div>
            </template>
            <div class="col-md-12 text-center" style="padding-top: 15px;">
                <center><b-btn variant="primary" @click.prevent="add">Agregar</b-btn></center>
            </div>
            <br>
        </div>        
    </div>
</template>

<script>
import VueInput from "@/components/Inputs/VueInput.vue";

export default {
    props: {
        disabled: {
            type: Boolean,
            default: false
        },
        documents: {
            type: Array,
            default() {
                return [];
            }
        },
    },
    components: {
        VueInput
    },
    computed: {
    },
    data () {
        return {
            documents: []
        };
    },
    methods: {
        getNewDocument() {
            return this.documents;
        },
        add() {
            this.documents.push({
                key: new Date().getTime() + Math.round(Math.random() * 10000),
                name: ''
            });
        },
        remove(index) {
            this.documents.splice(index, 1);
        }
    },
}
</script>