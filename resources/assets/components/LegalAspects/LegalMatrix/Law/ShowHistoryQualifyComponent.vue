<template>
    <b-modal @hidden="hideModal" ref="modalArticleFulfillment" :hideFooter="true" id="modals-default-fulfillment" class="modal-top" size="lg">
        <div slot="modal-title">
        Historial de cambios realizados
        </div>

        <b-card  bg-variant="transparent" title="" class="mb-3 box-shadow-none">
            <loading :display="!ready"/>
            <vue-table
                v-if="ready"
                configName="legalaspects-lm-article-fulfillment-histories"
                :modelId="id ? id : -1"
                ></vue-table>
        </b-card>
        <br>
        <div class="row float-right pt-12 pr-12y">
            <b-btn variant="primary" @click="hideModal">Cerrar</b-btn>
        </div>
    </b-modal>
</template>

<script>
import Loading from "@/components/Inputs/Loading.vue";

export default {
    components:{
        Loading
    },
    props: {
        id: { type: [Number, String] }
    },
    data() {
        return {
            ready: false
        };
    },
    mounted() {
        this.$refs.modalArticleFulfillment.show()
        setTimeout(() => {
          this.ready = true
        }, 3000)
    },
    methods: {
        hideModal() {
            this.$refs.modalArticleFulfillment.hide()
            this.$emit('close-modal-history')
        }   
    }
};
</script>