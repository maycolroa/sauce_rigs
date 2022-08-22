<template>
    <b-row>
        <b-col>
            <b-form :action="url" @submit.prevent="submit" autocomplete="off">
            	<b-card border-variant="primary" title="Causas del accidente o incidente">
				    <div class="col-md-12">
                        <b-form-row>
                            <div class="col-md-12">
                                <div class="float-right" style="padding-top: 10px;">
                                <b-btn variant="primary" @click.prevent="addCause()"><span class="ion ion-md-add-circle"></span>&nbsp;&nbsp;Agregar Causa Principal</b-btn>
                                </div>
                            </div>
                        </b-form-row>
                        <b-form-row style="padding-top: 15px;">
                            <b-form-feedback class="d-block" v-if="form.errorsFor(`causes`)" style="padding-bottom: 10px;">
                            {{ form.errorsFor(`causes`) }}
                            </b-form-feedback>
                            <perfect-scrollbar :options="{ wheelPropagation: true }" class="mb-4" style="height: 500px; padding-right: 15px; width: 100%;">
                                <template v-for="(cause, index) in form.causes">
                                <b-card no-body class="mb-2 border-secondary" :key="cause.key" style="width: 100%;">
                                    <b-card-header class="bg-secondary">
                                    <b-row>
                                        <b-col cols="10" class="d-flex justify-content-between"> {{ form.causes[index].description ? form.causes[index].description : `Nuevo Causa principal ${index + 1}` }}</b-col>
                                        <b-col cols="2">
                                        <div class="float-right">
                                            <b-button-group>
                                            <b-btn href="javascript:void(0)" v-b-toggle="'accordion' + cause.key+'-1'" variant="link">
                                                <span class="collapse-icon"></span>
                                            </b-btn>
                                            <b-btn @click.prevent="removeCause(index)" 
                                               
                                                size="sm" 
                                                variant="secondary icon-btn borderless"
                                                v-b-tooltip.top title="Eliminar Tema">
                                                <span class="ion ion-md-close-circle"></span>
                                            </b-btn>
                                            </b-button-group>
                                        </div>
                                        </b-col>
                                    </b-row>
                                    </b-card-header>
                                    <b-collapse :id="`accordion${cause.key}-1`" visible :accordion="`accordion-123`">
                                    <b-card-body>
                                        <b-form-row>
                                            <vue-textarea class="col-md-12" v-model="form.causes[index].description" label="Descripción" name="description" placeholder="Descripción" :error="form.errorsFor(`causes.${index}.description`)" rows="1"></vue-textarea>
                                        </b-form-row>
                                        <b-form-row>
                                            <div class="col-md-12">
                                                <div class="float-right" style="padding-top: 10px;">
                                                <b-btn variant="primary" @click.prevent="addCauseSecondary(index)"><span class="ion ion-md-add-circle"></span>&nbsp;&nbsp;Agregar Causa Secundaria</b-btn>
                                                </div>
                                            </div>
                                        </b-form-row>
                                        <b-form-row style="padding-top: 15px;">
                                            <b-form-feedback class="d-block" v-if="form.errorsFor(`causes.${index}.secondary`)" style="padding-bottom: 10px;">
                                                {{ form.errorsFor(`causes.${index}.secondary`) }}
                                            </b-form-feedback>
                                            <template v-for="(secondaryCause, index2) in cause.secondary" style="padding-right: 15px;">
                                                <b-card no-body class="mb-2 border-secondary" :key="secondaryCause.key" style="width: 100%;">
                                                    <b-card-header class="bg-secondary">
                                                        <b-row>
                                                            <b-col cols="10" class="d-flex justify-content-between"> {{ form.causes[index].secondary[index2].description ? form.causes[index].secondary[index2].description : `Nueva Causa Secundaria ${index2 + 1}` }}</b-col>
                                                            <b-col cols="2">
                                                                <div class="float-right">
                                                                    <b-button-group>
                                                                        <b-btn href="javascript:void(0)" v-b-toggle="'accordion' + secondaryCause.key+'-1'" variant="link">
                                                                        <span class="collapse-icon"></span>
                                                                        </b-btn>
                                                                        <b-btn @click.prevent="removeCauseSecondary(index, index2)" 
                                                                       
                                                                        size="sm" 
                                                                        variant="secondary icon-btn borderless"
                                                                        v-b-tooltip.top title="Eliminar Causa Secundaria">
                                                                            <span class="ion ion-md-close-circle"></span>
                                                                        </b-btn>
                                                                    </b-button-group>
                                                                </div>
                                                            </b-col>
                                                        </b-row>
                                                    </b-card-header>
                                                    <b-collapse :id="`accordion${secondaryCause.key}-1`" visible :accordion="`accordion-1234`">
                                                        <b-card-body>
                                                            <b-form-row>
                                                                <vue-textarea class="col-md-12" v-model="form.causes[index].secondary[index2].description" label="Descripción" name="description" placeholder="Descripción" :error="form.errorsFor(`causes.${index}.secondary.${index2}.description`)" rows="1"></vue-textarea>
                                                            </b-form-row>
                                                            <b-form-row>
                                                                <div class="col-md-12">
                                                                    <div class="float-right" style="padding-top: 10px;">
                                                                        <b-btn variant="primary" @click.prevent="addTertiary(index, index2)"><span class="ion ion-md-add-circle"></span>&nbsp;&nbsp;Agregar Causa Terciaria</b-btn>
                                                                    </div>
                                                                </div>
                                                            </b-form-row>
                                                            <b-form-row style="padding-top: 15px;">
                                                                <b-form-feedback class="d-block" v-if="form.errorsFor(`causes.${index}.secondary.${index2}.tertiary`)" style="padding-bottom: 10px;">
                                                                {{ form.errorsFor(`causes.${index}.secondary.${index2}.tertiary`) }}
                                                                </b-form-feedback>
                                                                <div class="table-responsive" style="padding-right: 15px;">
                                                                    <table class="table table-bordered table-hover" v-if="secondaryCause.tertiary.length > 0">
                                                                        <thead class="bg-secondary">
                                                                            <tr>
                                                                                <th scope="col" class="align-middle">#</th>
                                                                                <th scope="col" class="align-middle">Descripción</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            <template v-for="(item, index3) in secondaryCause.tertiary">
                                                                                <tr :key="index3">
                                                                                    <td class="align-middle">
                                                                                        <b-btn @click.prevent="removeTertiary(index, index2, index3)" 
                                                                                        size="xs" 
                                                                                        variant="outline-primary icon-btn borderless"
                                                                                        v-b-tooltip.top title="Eliminar Causa terciaria">
                                                                                        <span class="ion ion-md-close-circle"></span>
                                                                                        </b-btn>
                                                                                    </td>
                                                                                    <td style="padding: 0px;">
                                                                                        <vue-textarea class="col-md-12" v-model="form.causes[index].secondary[index2].tertiary[index3].description" label="" name="description" placeholder="Descripción" :error="form.errorsFor(`causes.${index}.secondary.${index2}.tertiary.${index3}.description`)" rows="1"></vue-textarea>
                                                                                    </td>
                                                                                </tr>
                                                                            </template>
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            </b-form-row>
                                                        </b-card-body>
                                                    </b-collapse>
                                                </b-card>
                                            </template>
                                        </b-form-row>
                                    </b-card-body>
                                    </b-collapse>
                                </b-card>
                                </template>
                            </perfect-scrollbar>
                        </b-form-row>
                    </div>
            	</b-card>
<br><br>
                            <div
                                class="flex-grow"
                            >
                                <flowy
                                    class="q-mx-auto"
                                    :nodes="form.nodes"
                                    id="tree_cause"
                                ></flowy>
                            </div>
<div class="html2canvas-container">

</div>
				<br>
				<div class="row float-right pt-10 pr-10">
                    <template>
                        <b-btn variant="default" :to="cancelUrl" :disabled="loading">{{"Cancelar"}}</b-btn>&nbsp;&nbsp;
                        <b-btn type="submit" :disabled="loading" variant="primary">Finalizar</b-btn>
                    </template>
                </div>
            </b-form>
        </b-col>
    </b-row>
</template>

<script>
import Form from "@/utils/Form.js";
import VueTextarea from "@/components/Inputs/VueTextarea.vue";
import VueInput from "@/components/Inputs/VueInput.vue";
import PerfectScrollbar from '@/vendor/libs/perfect-scrollbar/PerfectScrollbar';
import html2canvas from 'html2canvas';

export default {
    components: {
        VueTextarea,
        VueInput,
        PerfectScrollbar,
        html2canvas
    },
    props: {
        url: { type: String },
        method: { type: String },
        cancelUrl: { type: [String, Object], required: true },
        causes: {
			default() {
				return {
					causes: [],
                    accident_id: '',
                    isEdit: false,
                    delete: {
                        causes: [],
                        secondary: [],
                        tertiary: []
                    },
                    nodes: []
				};
			}
		}
    },
    watch: {
        causes() {
            this.loading = false;
            this.form = Form.makeFrom(this.causes, this.method);
        }
    },
    data() {
        return {
            loading: this.isEdit,
            form: Form.makeFrom(this.causes, this.method),
            
            holder: [],
            dragging: false,
            nodes: []
        };
    },
    methods: {
        submit(e) {
            this.loading = true;
            this.form
                .submit(e.target.action)
                .then(response => {
                    this.loading = false;
                    this.$router.push({ name: "industrialsecure-accidentswork" });
                })
                .catch(error => {
                    this.loading = false;
                });
        },
        addCause() {
            this.form.causes.push({
                key: new Date().getTime(),
                description: '',
                secondary: []
            })
        },
        removeCause(index)
        {
            if (this.form.causes[index].id != undefined)
                this.form.delete.causes.push(this.form.causes[index].id)

            this.form.causes.splice(index, 1)
        },
        addCauseSecondary(index)
        {
            this.form.causes[index].secondary.push({
                key: new Date().getTime(),
                description: '',
                tertiary: []
            })
        },
        removeCauseSecondary(indexObj, index)
        {
            if (this.form.causes[indexObj].secondary[index].id != undefined)
                this.form.delete.secondary.push(this.form.causes[indexObj].secondary[index].id)

            this.form.causes[indexObj].secondary.splice(index, 1)
        },
        addTertiary(indexObj, indexSub) 
        {
            this.form.causes[indexObj].secondary[indexSub].tertiary.push({
                key: new Date().getTime(),
                description: ''
            })
        },
        removeTertiary(indexObj, indexSub, index)
        {
            if (this.form.causes[indexObj].secondary[indexSub].tertiary[index].id != undefined)
                this.form.delete.tertiary.push(this.form.causes[indexObj].secondary[indexSub].tertiary[index].id)

            this.form.causes[indexObj].secondary[indexSub].tertiary.splice(index, 1)
        }
    },
    mounted() {
        setTimeout(() => {
            html2canvas(document.querySelector(".flowy-tree")).then(function(canvas) {
            document.querySelector(".html2canvas-container").appendChild(canvas);
        });    
        }, 5000);
        
    },
};
</script>

<style scoped>
.html2canvas-container { width: 3000px !important; height: 3000px !important; }

#tree_cause {
    overflow: visible !important;
}
</style>