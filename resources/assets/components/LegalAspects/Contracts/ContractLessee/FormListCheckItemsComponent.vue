<template>
    <b-form :action="url" @submit.prevent="submit" autocomplete="off">
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
									:modelId="form.id ? form.id : -1"
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
					<span class="text-muted">{{ item.criterion_description}}</span>
					<div class="media align-items-center mt-3">
						<div class="media-body ml-2">

							<!-- NO CUMPLE -->
							<b-btn v-if="item.qualification == 'NC'" @click="showModal(`modalPlan${index}`)" variant="primary"><span class="lnr lnr-bookmark"></span> Plan de acción</b-btn>

							<b-modal v-if="item.qualification == 'NC'" :ref="`modalPlan${index}`" :hideFooter="true" :id="`modals-default-${index+1}`" class="modal-top" size="lg">
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

							<b-modal v-if="item.qualification == 'C'" :ref="`modalFile${index}`" :hideFooter="true" :id="`modals-file-${index+1}`" class="modal-top" size="lg">
								<div slot="modal-title">
									Subir Archivos <span class="font-weight-light">Contratistas</span><br>
									<small class="text-muted">Selecciona archivos pdf's para este item.</small>
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
				</div>
			</b-card-body>
		</b-card>
		<br><br>
		<div class="row float-right pt-10 pr-10" style="padding-bottom: 40px;">
			<template>
				<b-btn variant="default" :to="cancelUrl" :disabled="loading">Atras</b-btn>&nbsp;&nbsp;
				<b-btn type="submit" v-if="!viewOnly" :disabled="loading" variant="primary">Guardar</b-btn>
			</template>
		</div>
	</b-form>
</template>

<script>

import VueAdvancedSelect from "@/components/Inputs/VueAdvancedSelect.vue";
import VueInput from "@/components/Inputs/VueInput.vue";
import VueCheckbox from "@/components/Inputs/VueCheckbox.vue";
import VueRadio from "@/components/Inputs/VueRadio.vue";
import ActionPlanComponent from '@/components/CustomInputs/ActionPlanComponent.vue';
import Form from "@/utils/Form.js";
import FormUploadFileListItemComponent from '@/components/LegalAspects/Contracts/ContractLessee/FormUploadFileListItemComponent.vue';
import ListCheckItemsResumenComponent from '@/components/LegalAspects/Contracts/ContractLessee/ListCheckItemsResumenComponent.vue';
import Alerts from '@/utils/Alerts.js';
import ElementFixedComponent from '@/components/ElementFixedComponent.vue';

export default {
	components: {
		VueAdvancedSelect,
		VueInput,
		VueCheckbox,
		VueRadio,
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
					{ name:'Ejecutada', value:'Ejecutada'}
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
			this.form = Form.makeFrom(this.items, this.method);
		}
	},
	data() {
		return {
			loading: this.isEdit,
			form: Form.makeFrom(this.items, this.method),
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
							edit_all: true
            })
				});

				this.form.items[index].files.forEach((file, index2) => {
					if (file.id !== undefined)
						this.form.delete.files.push(file)
				});

				this.form.items[index].files = [];
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
			}
		},
		submit(e) {
			this.loading = true;
			let data = new FormData();
			data.append('delete', JSON.stringify(this.form.delete));

			this.form.items.forEach((element, index) => {
				if(typeof element.files !== 'undefined')
				{
					if(element.files.length > 0)
					{
						element.filesIndex = `files_${index}`;
						element.files.forEach((file, index2) => {
							data.append(`files_${index}[${index2}]`, file.file);
						});
					}
				}
				
				element = JSON.stringify(element);
				data.append('items[]', element);
			});

			this.form
				.submit(e.target.action, false, data)
				.then(response => {
					this.loading = false;
					this.$router.push({ name: "legalaspects-contracts" });
				})
				.catch(error => {
					this.loading = false;
				});
		}
	}
};
</script>
