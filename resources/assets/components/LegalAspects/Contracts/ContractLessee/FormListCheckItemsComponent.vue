<template>
    <b-form :action="url" @submit.prevent="submit" autocomplete="off">
		<element-fixed-component>
		  <list-check-items-resumen-component :items="form.items" :validate_qualificacion="validate_qualificacion"/>
		</element-fixed-component>
		
		<b-card border-variant="primary" no-body class="mb-4">
			<b-card-header header-tag="h6" class="with-elements">
				<div class="card-header-title">Lista de estándares mínimos | {{ form.items[0].name }} | <b-btn variant="primary" size="sm" @click="$refs.modalHistorial.show()" ><span class="ion ion-md-eye"></span> Ver historial de cambios </b-btn></div>

					<b-modal ref="modalHistorial" :hideFooter="true" id="modals-historial" class="modal-top" size="lg">
						<div slot="modal-title">
							Historial de cambios realizados
						</div>

						<b-card  bg-variant="transparent"  title="" class="mb-3 box-shadow-none">
							<vue-table
									configName="legalaspects-contractor-list-check-history"
									:modelId="qualificationListId ? qualificationListId : -1"
									></vue-table>
						</b-card>
						<br>
						<div class="row float-right pt-12 pr-12y">
							<b-btn variant="primary" @click="$refs.modalHistorial.hide()">Cerrar</b-btn>
						</div>
					</b-modal>

					<vue-advanced-select @change="resetReloadShowItems" class="col-md-4 offset-md-1" v-model="filterQualification" :options="filterQualificationOptions" :hide-selected="false" name="filterQualification" label="Filtrar por calificación" placeholder="Seleccione la Calificación">
                    </vue-advanced-select>

			</b-card-header>
			<b-card-body>
				<template v-for="(item, index) in form.items">
					<div :key="item.id" v-if="item.show">
					<p class="my-1">{{ index + 1 }} - {{ item.item_name }}</p>
					<b-col v-if="validate_qualificacion">
                		<div class="float-right" style="padding-right: 10px;">
                    		<b-btn v-if="item.state_aprove_qualification == 'PENDIENTE'"  variant="warning" class="btn-circle-micro" title="Calificación Pendiente"><span class="fas fa-question"></span></b-btn>
							<b-btn v-if="item.state_aprove_qualification == 'APROBADA'"  variant="success" class="btn-circle-micro" title="Calificación Aprobada"><span class="fas fa-check"></span></b-btn>
							<b-btn v-if="item.state_aprove_qualification == 'RECHAZADA'"  variant="primary" class="btn-circle-micro" title="Calificación Rechazada"><span class="fas fa-times"></span></b-btn>						
                		</div>
              		</b-col>
					<span class="text-muted">{{ item.criterion_description}}</span>
					<div class="media align-items-center mt-3">
						<div class="media-body ml-2">

							<!-- NO CUMPLE -->
							<b-btn v-if="item.qualification == 'NC'" @click="showModal(`modalPlan${index}`)" variant="primary"><span class="lnr lnr-bookmark"></span> Plan de acción</b-btn>

							<b-modal v-show="item.qualification == 'NC'" :ref="`modalPlan${index}`" :hideFooter="true" :id="`modals-default-${index+1}`" class="modal-top" size="lg" @hidden="saveQualification(index)">
								<div slot="modal-title">
									Plan de acción <span class="font-weight-light">Contratistas</span><br>
									<small class="text-muted">Crea planes de acción para tu justificación.</small>
								</div>

								<b-card  bg-variant="transparent"  title="" class="mb-3 box-shadow-none">
									<action-plan-component
										:is-edit="!viewOnly"
										:view-only="viewOnly"
										:form="form"
										:prefix-index="`items.${index}.`"
										:action-plan-states="actionPlanStates"
										v-model="item.actionPlan"
										:action-plan="item.actionPlan"
										module="Contratista"/>
								</b-card>
								<br>
								<vue-textarea class="col-md-12" v-model="item.general_observations_ac" label="Observaciones generales (El contenido de este campo se replicara en el campo de observaciones de todas las actividades contenidas en el Plan de acción)" name="general_observations_ac" placeholder="Observaciones generales" :error="form.errorsFor(`general_observations_ac`)"></vue-textarea>
								<br>
								<div v-if="item.general_observations_ac">
									<center>
										<p> ¿Esta seguro de que desea continuar, el contenido del campo Observaciones generales sobreescribira la informacion de observaciones de todas las actividades contenidas en el Plan de acción? </p>
										<br>
										<div>
											<b-btn @click="hideModal(`modalPlan${index}`, index)" :disabled="loading" variant="primary">SI</b-btn>&nbsp;&nbsp;
											<b-btn variant="primary" @click="clearObs(index)">NO</b-btn>
										</div>
									</center>
								</div>
								<br>
								<div class="row float-right pt-12 pr-12y">
									<b-btn variant="primary" @click="hideModal(`modalPlan${index}`, index)">Cerrar</b-btn>
								</div>
							</b-modal>
							<!------------------------------->

							<!--CUMPLE -->
							<b-btn v-if="item.qualification == 'C'" @click="showModal(`modalFile${index}`)" variant="primary"><span class="lnr lnr-bookmark"></span> Consultar archivos</b-btn>

							<b-modal v-show="item.qualification == 'C'" :ref="`modalFile${index}`" :hideFooter="true" :id="`modals-file-${index+1}`" class="modal-top" size="lg" @hidden="saveQualification(index)">
								<div slot="modal-title">
									Subir Archivos <span class="font-weight-light">Contratistas</span><br>
									<small class="text-muted">Selecciona archivos pdf's para este item.</small>
								</div>

								<div v-if="existError(`items.${index}.`)">
										<b-form-feedback class="d-block" style="padding-bottom: 10px; text-align: center;">
							                Archivos requeridos
							             </b-form-feedback>
								</div>

								<b-card  bg-variant="transparent"  title="" class="mb-3 box-shadow-none">
									<form-upload-file-list-item-component
										:form="form"
										:view-only="viewOnly"
										:prefix-index="`items.${index}.`"
										v-model="form.items[index].files"
										@removeFile="pushRemoveFile"/>
								</b-card>
								<br>
								<div class="row float-right pt-12 pr-12y">
									<b-btn variant="primary" @click="hideModal(`modalFile${index}`)">Cerrar</b-btn>
								</div>
							</b-modal>
							<!------------------------------->
						</div>
						<div class="text-muted small text-nowrap">	
								<vue-radio :disabled="viewOnly" v-model="item.qualification" label="Calificación" :name="`items${item.id}`" :error="form.errorsFor(`items.${index}`)" :options="qualifications" :checked="item.qualification" @input="changeActionFiles(item.qualification, `${index}`)"></vue-radio>
						</div>						
					</div>
					<div v-if="validate_qualificacion" class="col-md-12">
						<vue-textarea @onBlur="saveQualification(`${index}`)" :disabled="viewOnly" class="col-md-12" v-model="item.observations" label="Observaciones" name="observations" placeholder="Observaciones" :error="form.errorsFor(`observations`)"></vue-textarea>
						<center>
							<b-btn @click="aproveQualification(item.id, index)" variant="primary"><span class="lnr lnr-checkmark-circle"></span> Aprobar Calificación</b-btn>
							<b-btn @click="showModal(`modalRechazo${index}`)" variant="primary"><span class="lnr lnr-cross-circle"></span> Rechazar Calificación</b-btn>
						</center>

						<b-modal :ref="`modalRechazo${index}`" :hideFooter="true" :id="`modals-rechazo-${index+1}`" class="modal-top" size="lg" @hidden="desaproveQualification(item.id, item.reason_rejection, index)">
							<div slot="modal-title">
								Motivo de Rechazo <span class="font-weight-light">de la calificación</span>
							</div>
							<br>
							<vue-textarea class="col-md-12" v-model="item.reason_rejection" label="Motivo del rechazo" name="reason_rejection" placeholder="Motivo" :error="form.errorsFor(`reason_rejection`)"></vue-textarea>
							<br>
							<div class="row float-right pt-12 pr-12y">
								<b-btn variant="primary" @click="hideModal(`modalRechazo${index}`)">Cerrar</b-btn>
							</div>
						</b-modal>
					</div>
					<div v-else class="col-md-12">
						<vue-textarea @onBlur="saveQualification(`${index}`)" :disabled="viewOnly" class="col-md-12" v-model="item.observations" label="Observaciones" name="observations" placeholder="Observaciones" :error="form.errorsFor(`observations`)"></vue-textarea>
					</div>
					<div v-if="existError(`items.${index}.`)">
							<b-form-feedback class="d-block" style="padding-bottom: 10px;">
				                Este item contiene errores en sus datos
				             </b-form-feedback>
					</div>
					</div>
				</template>
			</b-card-body>
		</b-card>
		<br><br>
		<div class="row float-right pt-10 pr-10" style="padding-bottom: 40px;">
			<template>
				<b-btn variant="default" :to="cancelUrl" :disabled="loading">Atras</b-btn>&nbsp;&nbsp;
			</template>
		</div>
	</b-form>
</template>

<script>

import VueAdvancedSelect from "@/components/Inputs/VueAdvancedSelect.vue";
import VueInput from "@/components/Inputs/VueInput.vue";
import VueCheckbox from "@/components/Inputs/VueCheckbox.vue";
import VueRadio from "@/components/Inputs/VueRadio.vue";
import VueTextarea from "@/components/Inputs/VueTextarea.vue";
import ActionPlanComponent from '@/components/CustomInputs/ActionPlanComponent.vue';
import Form from "@/utils/Form.js";
import FormUploadFileListItemComponent from '@/components/LegalAspects/Contracts/ContractLessee/FormUploadFileListItemComponent.vue';
import ListCheckItemsResumenComponent from '@/components/LegalAspects/Contracts/ContractLessee/ListCheckItemsResumenComponent.vue';
import Alerts from '@/utils/Alerts.js';
import ElementFixedComponent from '@/components/General/ElementFixedComponent.vue';

export default {
	components: {
		VueAdvancedSelect,
		VueInput,
		VueCheckbox,
		VueRadio,
		VueTextarea,
		ActionPlanComponent,
		FormUploadFileListItemComponent,
		ListCheckItemsResumenComponent,
		ElementFixedComponent
	},
	props: {
		url: { type: String },
		method: { type: String },
		cancelUrl: { type: [String, Object], required: true },
		isEdit: { type: Boolean, default: false },
		viewOnly: { type: Boolean, default: false },
		contractId: {type: [String, Number] },
		qualificationListId: {type: [String, Number] },
		validate_qualificacion: { type: Boolean, default: false },
		qualifications: {
			type: [Array, Object],
			default: function() {
				return [];
			}
		},
		actionPlanStates: {
			type: Array,
			default: function() {
				return [
					{ name:'Pendiente', value:'Pendiente'},
					{ name:'Ejecutada', value:'Ejecutada'},
					{ name:'N/A', value:'N/A'}
				];
			}
		},
		items: {
			type: [Array, Object],
			default: function() {
				return {
					id: -1,
					items: [],
					delete: {
						files: []
					}
				};
			}
		},
	},
	watch: {
		items() {
			this.loading = false;
			this.form = Form.makeFrom(this.items, this.method, false, false);
		}
	},
	mounted() {
		this.resetReloadShowItems();
	},
	data() {
		return {
			loading: this.isEdit,
			form: Form.makeFrom(this.items, this.method, false, false),
			filterQualification: 'Todas',
			filterQualificationOptions: [
         		{name: 'Todas', value: 'Todas'},
				{name: 'Cumple', value: 'C'},
         		{name: 'No Cumple', value: 'NC'},
         		{name: 'No Aplica', value: 'NA'}
			]
		};
	},
	methods: {
		showModal(ref) {
			this.$refs[ref][0].show()
		},
		hideModal(ref) {
			this.$refs[ref][0].hide()
		},
		pushRemoveFile(value)
		{
			this.form.delete.files.push(value)
		},
		clearObs(index)
		{
			let item = this.form.items[index]
			item.general_observations_ac = '';
		},
		changeActionFiles(qualification, index)
		{
			if (qualification == 'C')
			{
				if (typeof this.form.items[index].actionPlan !== 'undefined')
				{
					this.form.items[index].actionPlan.activities.forEach((action, index2) => {
						if (action.id != '')
							this.form.items[index].actionPlan.activitiesRemoved.push(action)
					});

					this.form.items[index].actionPlan.activities = [];
				}
				
				this.verifyRequiredFile(this.form.items[index].id, index)
				//this.showModal(`modalFile${index}`)
			}
			else if (qualification == 'NC')
			{
				this.form.items[index].activities_defined.forEach((action, index2) => {
					this.form.items[index].actionPlan.activities.push({
							key: new Date().getTime() + Math.round(Math.random() * 10000),
							id: '',
							description: action,
							responsible_id: '',
							execution_date: '',
							expiration_date: '',
							state: '',
							editable: 'NO',
							observation: '',
							edit_all: true
            		})
				});

				this.form.items[index].files.forEach((file, index2) => {
					if (file.id !== undefined)
						this.form.delete.files.push(file)
				});

				this.form.items[index].files = [];

				if (this.form.items[index].actionPlan.activities.length > 0)
					this.showModal(`modalPlan${index}`)
				else
					this.saveQualification(index)
			}
			else
			{
				if (typeof this.form.items[index].actionPlan !== 'undefined')
				{
					this.form.items[index].actionPlan.activities.forEach((action, index2) => {
						if (action.id != '')
							this.form.items[index].actionPlan.activitiesRemoved.push(action)
					});
					
					this.form.items[index].actionPlan.activities = [];
				}

				this.form.items[index].files.forEach((file, index2) => {
					if (file.id !== undefined)
						this.form.delete.files.push(file)
				});

				this.form.items[index].files = [];

				this.saveQualification(index)
			}
		},
		existError(index) {
			let keys = Object.keys(this.form.errors.errors)
			let result = false

			if (keys.length > 0)
			{
				for (let i = 0; i < keys.length; i++)
				{
					if (keys[i].indexOf(index) > -1)
					{
						result = true
						break
					}
				}
			}

			return result
		},
		saveQualification(index)
    	{
			if (!this.viewOnly)
      		{
				this.loading = true;
        		let item = this.form.items[index]

				if (item.general_observations_ac)
				{
					item.actionPlan.activities.forEach((activity) => {
						activity.observation = item.general_observations_ac;
					});
				}
        
				let data = new FormData();
				data.append('id', item.id);
				data.append('category_id', item.category_id);
				data.append('item_name', item.item_name);
				data.append('criterion_description', item.criterion_description);
				data.append('verification_mode', item.verification_mode);
				data.append('percentage_weight', item.percentage_weight);
				data.append('created_at', item.created_at);
				data.append('updated_at', item.updated_at);
				data.append('name', item.name);
				data.append('observations', item.observations);
				data.append('activities_defined', JSON.stringify(item.activities_defined));
				data.append('qualification', item.qualification);
				data.append('files', JSON.stringify(item.files));
				data.append('actionPlan', JSON.stringify(item.actionPlan));
				data.append(`items[${index}]`, JSON.stringify({ files: item.files, actionPlan: item.actionPlan }));
				data.append('delete', JSON.stringify(this.form.delete));
				data.append('contract_id', this.contractId);
				data.append('list_qualification_id', this.qualificationListId);

				if(typeof item.files !== 'undefined')
				{
					if(item.files.length > 0)
					{
						item.files.forEach((file, keyFile) => {
							data.append(`files_binary[${keyFile}]`, file.file);
						});
					}
				}

				this.form.resetError()
				this.form
				.submit(this.url, false, data)
				.then(response => {
					_.forIn(response.data.data, (value, key) => {
					item[key] = value
					})

					this.loading = false;
					
				})
				.catch(error => {
					this.loading = false;
				});

				item.general_observations_ac = '';
			}
		},
		verifyRequiredFile(item_id, index)
		{
			this.postData = Object.assign({}, {item_id: item_id});

			axios.post('/legalAspects/listCheck/verifyRequiredFile', this.postData)
				.then(response => {
					this.required = response.data
					if (this.required == 'Requerido')
					{
						this.showModal(`modalFile${index}`)
					}
					else
						this.saveQualification(index)
				}).catch(error => {
					Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
				});
		},
		aproveQualification(item_id, index)
		{
			this.postData2 = Object.assign({}, {item_id: item_id}, {list_id: this.qualificationListId}, {contract_id: this.contractId});

			axios.post('/legalAspects/contracts/aproveQualification', this.postData2)
				.then(response => {
					this.form.items[index].state_aprove_qualification = 'APROBADA';
					this.form.items[index].reason_rejection = null;
					Alerts.success('Exito','Se actualizo la calificación');
				}).catch(error => {
					Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
				});
		},
		desaproveQualification(item_id, reason_rejection, index)
		{
			this.postData3 = Object.assign({}, {item_id: item_id}, {list_id: this.qualificationListId}, {contract_id: this.contractId}, {reason_rejection: reason_rejection});

			axios.post('/legalAspects/contracts/desaproveQualification', this.postData3)
				.then(response => {
					this.form.items[index].state_aprove_qualification = 'RECHAZADA';
					this.form.items[index].reason_rejection = reason_rejection;
					this.form.items[index].qualification = 'NC';
					Alerts.success('Exito','Se actualizo la calificación');
				}).catch(error => {
					Alerts.error('Error', 'Debe adicionar un motivo de rechazo');
				});
		},
		resetReloadShowItems() 
		{
			this.loading = true;
			_.forIn(this.form.items, (item, key) => {
				if (this.filterQualification == 'Todas')
				{
					item.show = true;
				}
				else if (this.filterQualification == item.qualification)
				{
					item.show = true;
				}
				else
				{
					item.show = false;
				}
			});
			this.loading = false;
		},
	}
};
</script>
