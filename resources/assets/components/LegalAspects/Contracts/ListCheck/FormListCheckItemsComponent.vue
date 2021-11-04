<template>
    <b-form :action="url" @submit.prevent="submit" autocomplete="off">
		<b-modal ref="modalTransfer" :hideFooter="true" id="modals-historial" class="modal-top" size="md">
			<div slot="modal-title">
				<h4>INFORMACIÓN</h4>
			</div>
			<p> Se clonaron solo las calificaciones, debe diligenciar de nuevo los archivos y planes de acción según sea caso </p>

			<div class="row float-right pt-12 pr-12y">						
				<b-btn variant="primary" @click="$refs.modalTransfer.hide()">Cerrar</b-btn>
			</div>
		</b-modal>

		<element-fixed-component>
		  <list-check-items-resumen-component :items="form.items"/>
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

			</b-card-header>
			<b-card-body>
				<div class="rounded ui-bordered p-3 mb-3"  v-for="(item, index) in form.items" :key="item.id">
					<p class="my-1">{{ index + 1 }} - {{ item.item_name }}</p> 
					<b-col>
                		<div class="float-right" style="padding-right: 10px;">
                    		<b-btn v-b-popover.hover.focus.left="item.verification_mode" title="Modo de verificación" variant="primary" class="btn-circle-micro"><span class="fas fa-info"></span></b-btn>
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
										:action-plan="item.actionPlan"/>
								</b-card>
								<br>
								<div class="row float-right pt-12 pr-12y">
									<b-btn variant="primary" @click="hideModal(`modalPlan${index}`)">Cerrar</b-btn>
								</div>
							</b-modal>
							<!------------------------------->

							<!--CUMPLE -->
							<b-btn v-if="item.qualification == 'C'" @click="showModal(`modalFile${index}`)" variant="primary"><span class="lnr lnr-bookmark"></span> Adjuntar archivos</b-btn>

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
					<div class="col-md-12">
						<vue-textarea @onBlur="saveQualification(`${index}`)" :disabled="viewOnly" class="col-md-12" v-model="item.observations" label="Observaciones" name="observations" placeholder="Observaciones" :error="form.errorsFor(`observations`)"></vue-textarea>
					</div>
					<div v-if="existError(`items.${index}.`)">
							<b-form-feedback class="d-block" style="padding-bottom: 10px;">
				                Este item contiene errores en sus datos
				             </b-form-feedback>
					</div>
				</div>
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
		message: { type: Boolean, default: false },
		viewOnly: { type: Boolean, default: false },
		contractId: {type: [String, Number] },
		qualificationListId: {type: [String, Number] },
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
		if (this.message)
			this.$refs.modalTransfer.show()
	},
	data() {
		return {
			loading: this.isEdit,
			form: Form.makeFrom(this.items, this.method, false, false),
			required: ''
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
                    }).catch(error => {
                        Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
                    });
		}
	}
};
</script>
