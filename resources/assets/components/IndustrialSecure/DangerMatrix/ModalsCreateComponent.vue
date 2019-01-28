<template>
    <div>
        <div>
            <b-btn variant="primary" @click="showFilterModal('activity')">Crear Actividad</b-btn>&nbsp;&nbsp;
            <b-btn variant="primary" @click="showFilterModal('danger')">Crear Peligro</b-btn>
        </div>

        <!-- Modal Activity -->
        <b-modal ref="activity" :hideFooter="true" id="modals-top" size="lg" class="modal-top">
            <div slot="modal-title">
                Crear Actividad
            </div>

            <div class="row" style="padding-bottom: 10px;">
                <div class="col-md">
                    <b-card no-body>
                        <b-card-body>
                            <activity-form
                            :modal="true"
                            @closeModal="hideFilterModal('activity')"/>
                        </b-card-body>
                    </b-card>
                </div>
            </div>
            <br>
            <div class="row float-right pt-12 pr-12y">
                <b-btn variant="primary" @click="hideFilterModal('activity')">Cerrar</b-btn>
            </div>
        </b-modal>

        <!-- Modal Danger -->
        <b-modal ref="danger" :hideFooter="true" id="modals-top" size="lg" class="modal-top">
            <div slot="modal-title">
                Crear Peligro
            </div>

            <div class="row" style="padding-bottom: 10px;">
                <div class="col-md">
                    <b-card no-body>
                        <b-card-body>
                            <danger-form
                            :modal="true"
                            @closeModal="hideFilterModal('danger')"/>
                        </b-card-body>
                    </b-card>
                </div>
            </div>
            <br>
            <div class="row float-right pt-12 pr-12y">
                <b-btn variant="primary" @click="hideFilterModal('danger')">Cerrar</b-btn>
            </div>
        </b-modal>
    </div>
</template>

<script>
import { SweetModal, SweetModalTab } from 'sweet-modal-vue';
import ActivityForm from '@/views/IndustrialSecure/activities/create.vue';
import DangerForm from '@/views/IndustrialSecure/dangers/create.vue';

export default {
    name: 'modals-create',
    metaInfo: {
        title: 'Modal - Crear'
    },
    components:{
        SweetModal,
        ActivityForm,
        DangerForm
    },
    data () {
        return {}
    },
    methods: {
        showFilterModal (ref) {
            this.$refs[ref].show()
        },
        hideFilterModal (ref) {
            this.$refs[ref].hide()
        }
    }
}
</script>