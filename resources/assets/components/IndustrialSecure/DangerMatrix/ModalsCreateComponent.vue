<template>
    <div>
        <div>
            <b-btn v-if="auth.can['activities_c']" class="btn-modals" variant="primary" @click="showFilterModal('activity')">Crear Proceso</b-btn>&nbsp;&nbsp;
            <b-btn v-if="auth.can['dangers_c']" class="btn-modals" variant="primary" @click="showFilterModal('danger')">Crear Peligro</b-btn>
            &nbsp;&nbsp;
            <b-btn v-if="confLocation.regional == 'SI' && auth.can['regionals_c']" class="btn-modals" variant="primary" @click="showFilterModal('regional')">Crear Regional</b-btn>
            &nbsp;&nbsp;
            <b-btn v-if="confLocation.headquarter == 'SI' && auth.can['headquarters_c']" class="btn-modals" variant="primary" @click="showFilterModal('headquarter')">Crear Sede</b-btn>
            &nbsp;&nbsp;
            <b-btn v-if="confLocation.area == 'SI' && auth.can['areas_c']" class="btn-modals" variant="primary" @click="showFilterModal('area')">Crear Área</b-btn>
            &nbsp;&nbsp;
            <b-btn v-if="confLocation.process == 'SI' && auth.can['processes_c']" class="btn-modals" variant="primary" @click="showFilterModal('process')">Crear Macroproceso</b-btn>
        </div>

        <!-- Modal Activity -->
        <b-modal ref="activity" :hideFooter="true" id="modals-top" size="lg" class="modal-top">
            <div slot="modal-title">
                Crear Proceso
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

        <!-- Modal Regional -->
        <b-modal ref="regional" :hideFooter="true" id="modals-top" size="lg" class="modal-top">
            <div slot="modal-title">
                Crear Regional
            </div>

            <div class="row" style="padding-bottom: 10px;">
                <div class="col-md">
                    <b-card no-body>
                        <b-card-body>
                            <regional-form
                            :modal="true"
                            @closeModal="hideFilterModal('regional')"/>
                        </b-card-body>
                    </b-card>
                </div>
            </div>
            <br>
            <div class="row float-right pt-12 pr-12y">
                <b-btn variant="primary" @click="hideFilterModal('regional')">Cerrar</b-btn>
            </div>
        </b-modal>

        <!-- Modal Sede -->
        <b-modal ref="headquarter" :hideFooter="true" id="modals-top" size="lg" class="modal-top">
            <div slot="modal-title">
                Crear Sede
            </div>

            <div class="row" style="padding-bottom: 10px;">
                <div class="col-md">
                    <b-card no-body>
                        <b-card-body>
                            <headquarter-form
                            :modal="true"
                            @closeModal="hideFilterModal('headquarter')"/>
                        </b-card-body>
                    </b-card>
                </div>
            </div>
            <br>
            <div class="row float-right pt-12 pr-12y">
                <b-btn variant="primary" @click="hideFilterModal('headquarter')">Cerrar</b-btn>
            </div>
        </b-modal>

        <!-- Modal Area -->
        <b-modal ref="area" :hideFooter="true" id="modals-top" size="lg" class="modal-top">
            <div slot="modal-title">
                Crear Área
            </div>

            <div class="row" style="padding-bottom: 10px;">
                <div class="col-md">
                    <b-card no-body>
                        <b-card-body>
                            <area-form
                            :modal="true"
                            @closeModal="hideFilterModal('area')"/>
                        </b-card-body>
                    </b-card>
                </div>
            </div>
            <br>
            <div class="row float-right pt-12 pr-12y">
                <b-btn variant="primary" @click="hideFilterModal('area')">Cerrar</b-btn>
            </div>
        </b-modal>

        <!-- Modal Proceso -->
        <b-modal ref="process" :hideFooter="true" id="modals-top" size="lg" class="modal-top">
            <div slot="modal-title">
                Crear Macroproceso
            </div>

            <div class="row" style="padding-bottom: 10px;">
                <div class="col-md">
                    <b-card no-body>
                        <b-card-body>
                            <process-form
                            :modal="true"
                            @closeModal="hideFilterModal('process')"/>
                        </b-card-body>
                    </b-card>
                </div>
            </div>
            <br>
            <div class="row float-right pt-12 pr-12y">
                <b-btn variant="primary" @click="hideFilterModal('process')">Cerrar</b-btn>
            </div>
        </b-modal>
    </div>
</template>

<style>
.btn-modals {
    margin-top: 15px;
}
</style>

<script>
import { SweetModal, SweetModalTab } from 'sweet-modal-vue';
import ActivityForm from '@/views/IndustrialSecure/activities/create.vue';
import DangerForm from '@/views/IndustrialSecure/dangers/create.vue';
import RegionalForm from '@/views/Administrative/regionals/create.vue';
import HeadquarterForm from '@/views/Administrative/headquarters/create.vue';
import AreaForm from '@/views/Administrative/areas/create.vue';
import ProcessForm from '@/views/Administrative/processes/create.vue';

export default {
    name: 'modals-create',
    metaInfo: {
        title: 'Modal - Crear'
    },
    components:{
        SweetModal,
        ActivityForm,
        DangerForm,
        RegionalForm,
        HeadquarterForm,
        AreaForm,
        ProcessForm
    },
    props: {
        confLocation: {
            default() {
                return {
                    regional: 'NO',
                    headquarter: 'NO',
                    area: 'NO',
                    process: 'NO'
                };
            }
        }
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