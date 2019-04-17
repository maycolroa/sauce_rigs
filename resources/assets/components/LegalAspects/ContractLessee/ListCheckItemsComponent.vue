<template>
    <b-row>
        <b-col>
			<b-form :action="url" @submit.prevent="submit" autocomplete="off">
				<b-card border-variant="primary" no-body class="mb-4">
				<b-card-header header-tag="h6" class="with-elements">
					<div class="card-header-title">Lista de estándares mínimos | {{ name }}</div>
					<div class="card-header-elements ml-auto">
					<!-- <b-btn variant="default" class="btn-xs md-btn-flat">Show more</b-btn>
					     Aqui se debe poner el contador si es requerido -->
					</div>
				</b-card-header>
				<!-- <perfect-scrollbar style="height: 350px"> -->
					<b-card-body>
						<div class="rounded ui-bordered p-3 mb-3"  v-for="(item, index) in form.items" :key="item.id">
							<p class="my-1">{{ index+1 }} - {{ item.item_name }}</p>
							<span class="text-muted">{{ item.criterion_description}}</span>
							<div class="media align-items-center mt-3">
								<div class="media-body ml-2">

									<b-btn v-if="item.qualification == 2"  @click="modalsActionIndex=index" variant="primary"><span class="lnr lnr-bookmark"></span> Plan de acción</b-btn>
									<b-modal v-if="item.qualification == 2" v-model="item.qualification == 2 && index == modalsActionIndex" :id="`modals-default-${index+1}`" cancel-title="Cancelar" ok-title="Aceptar" size="lg" @hide="modalsActionIndex = null">
											
										<div slot="modal-title">
											Plan de acción <span class="font-weight-light">Contratistas</span><br>
											<small class="text-muted">Crea planes de acción para tu justificación.</small>
										</div>
											<!-- border-variant="secondary" -->
											<b-card  bg-variant="transparent"  title="" class="mb-3 box-shadow-none">
													<action-plan-contract-component
													:is-edit="isEdit"
													:view-only="viewOnly"
													:form="form"
													:prefix-index="`items.${index}.`"
													:action-plan-states="actionPlanStates"
													v-model="item.actionPlan"
													:action-plan="item.actionPlan"
													:item-id="item.id == undefined ? 0 : item.id"
													:defined-activities="item.activities_defined"/>
											</b-card>
									</b-modal>

									<b-btn v-if="item.qualification == 1" @click="modalsFilesIndex=index" variant="primary"><span class="lnr lnr-paperclip"></span> Adjuntar archivos</b-btn>
									<b-modal v-if="item.qualification == 1" v-model="item.qualification == 1 && index == modalsFilesIndex" :id="`modals-file-${index+1}`"  cancel-title="Cancelar" ok-title="Aceptar" size="lg" @hide="modalsFilesIndex = null">
											
										<div slot="modal-title">
											Subir Archivos <span class="font-weight-light">Contratistas</span><br>
											<small class="text-muted">Selecciona archivos pdf's para este item.</small>
										</div>

										<form-upload-file-list-item-component
										:is-edit="isEdit"
										:view-only="viewOnly"
										:form="form"
										:prefix-index="`items.${index}.`"
										:item-id="item.id == undefined ? 0 : item.id"
										v-model="item.files"/>
											
									</b-modal>
								
								</div>
								<div class="text-muted small text-nowrap">
									
									<vue-radio v-model="item.qualification" label="Calificación" :name="`items${item.id}`" :error="form.errorsFor(`items.${index}`)" :options="qualifications" :checked="item.qualification" @input="changeActionFiles(item.qualification, `${index}`)"></vue-radio>
									
								</div>
							</div>
						</div>
					</b-card-body>
				<!-- </perfect-scrollbar> -->
				</b-card>
				<br><br>
				<div class="row float-right pt-10 pr-10">
					<template>
						<b-btn variant="default" :to="cancelUrl" :disabled="loading">{{ viewOnly ? "Atras" : "Cancelar"}}</b-btn>&nbsp;&nbsp;
						<b-btn type="submit" :disabled="loading" variant="primary">Finalizar</b-btn>
					</template>
				</div>
			</b-form>
        </b-col>
    </b-row>
</template>

<script>

import VueAdvancedSelect from "@/components/Inputs/VueAdvancedSelect.vue";
import VueInput from "@/components/Inputs/VueInput.vue";
import VueCheckbox from "@/components/Inputs/VueCheckbox.vue";
import VueRadio from "@/components/Inputs/VueRadio.vue";
import ActionPlanContractComponent from '@/components/CustomInputs/ActionPlanContractComponent.vue';
import Form from "@/utils/Form.js";
import FormUploadFileListItemComponent from '@/components/LegalAspects/ContractLessee/FormUploadFileListItemComponent.vue';
import Alerts from '@/utils/Alerts.js';

export default {
	components: {
		VueAdvancedSelect,
		VueInput,
		VueCheckbox,
		VueRadio,
		ActionPlanContractComponent,
		FormUploadFileListItemComponent
	},
	props: {
		url: { type: String },
		method: { type: String },
		cancelUrl: { type: [String, Object], required: true },
		isEdit: { type: Boolean, default: false },
		viewOnly: { type: Boolean, default: false },
		qualifications: { type: Object, required: true },
		name: { type: String, required: true },
		contract: {type: Object,
			default() {
				return {}
			}
		},
		actionPlanStates: {
			type: Array,
			default: function() {
				return [
					{ name:'Pendiente', value:'Pendiente'},
					{ name:'Ejecutada', value:'Ejecutada'}
				];
			}
		}
	},
	watch: {
		'contract.items'(newVal) {
			this.loading = false;
			this.form = Form.makeFrom(this.contract, this.method);
		}
	},
	data() {
		return {
			loading: this.isEdit,
			form: Form.makeFrom(this.contract, this.method),
			modalsFilesIndex: null,
			modalsActionIndex: null,
		};
	},
	methods: {
		
		submit(e) {
			let data = new FormData();
        	this.form.items.forEach((element, index) => {
			
				if(typeof element.files !== 'undefined'){
					if(element.files.length > 0){
						element.filesIndex = `files_${index}`;
						element.files.forEach(file => {
							data.append(`files_${index}[]`, file.file);
						});
					}
				}
            	element = JSON.stringify(element);
				data.append('items[]', element);
				  
			});
	
			this.loading = true;
			this.form.submit(e.target.action, false, data).then(response => {
				this.loading = false;
				// this.$router.push({ name: "legalaspects-contracts" });
			})
			.catch(error => {
				this.loading = false;
				console.log(error.errors);
				// Alerts.error('Error', 'Debes calificar por lo menos un item para poder guardar la lista de estándares');
			});
		},
		changeActionFiles(qualification, index){
			this.modalsActionIndex = index;
			if(qualification == 1) {
				if (typeof this.form.items[index].actionPlan !== 'undefined') {
					this.form.items[index].actionPlan.activities = [];
					this.form.items[index].actionPlan.activitiesRemoved = [];
				}
			} else if(qualification == 2) {
				this.form.items[index].files = [];
			} else {
				if (typeof this.form.items[index].actionPlan !== 'undefined') {
					this.form.items[index].actionPlan.activities = [];
					this.form.items[index].actionPlan.activitiesRemoved = [];
				}
				this.form.items[index].files = [];
			}
		}
	}
};
</script>
