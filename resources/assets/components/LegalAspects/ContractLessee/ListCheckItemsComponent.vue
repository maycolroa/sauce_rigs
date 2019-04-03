<template>
    <b-row>
        <b-col>
			<b-form :action="url" @submit.prevent="submit" autocomplete="off">
				<b-card border-variant="primary" no-body class="mb-4">
				<b-card-header header-tag="h6" class="with-elements">
					<div class="card-header-title">Lista de items | {{ name }}</div>
					<div class="card-header-elements ml-auto">
					<!-- <b-btn variant="default" class="btn-xs md-btn-flat">Show more</b-btn> -->
					</div>
				</b-card-header>
				<!-- <perfect-scrollbar style="height: 350px"> -->
					<b-card-body>
						<div class="rounded ui-bordered p-3 mb-3"  v-for="(item, index) in form.items" :key="item.id">
							<p class="my-1">{{ index+1 }} - {{ item.item_name }}</p>
							<span class="text-muted">{{ item.criterion_description}}</span>
							<div class="media align-items-center mt-3">
								<div class="media-body ml-2">
									<b-btn v-if="item.qualification == 2" variant="primary" v-b-modal="`modals-default-${index+1}`"><span class="lnr lnr-book"></span> Plan de acción</b-btn>
									<b-btn v-b-modal="`modals-file-${index+1}`"><span class="lnr lnr-paperclip"></span> Adjuntar archivos</b-btn>
									<b-modal :id="`modals-default-${index+1}`" v-model="item.qualification == 2" cancel-title="Cancelar" ok-title="Aceptar" size="lg">
										<!-- <div v-if="items != undefined">
											{{ item.activities[0] }}
										</div> -->
											
										<div slot="modal-title">
											Plan de acción <span class="font-weight-light">Contratistas</span><br>
											<small class="text-muted">Crea planes de acción para tu justificación.</small>
										</div>
											<!-- border-variant="secondary" -->
											<b-card  bg-variant="transparent"  title="" class="mb-3 box-shadow-none">
													<action-plan-component
													:is-edit="isEdit"
													:view-only="viewOnly"
													:form="form"
													:prefix-index="`items.${index}.`"
													:action-plan-states="actionPlanStates"
													v-model="item.actionPlan"
													:action-plan="item.actionPlan"
													:defined-activities="item.activities_defined"/>
											</b-card>
									</b-modal>
									<b-modal :id="`modals-file-${index+1}`"  cancel-title="Cancelar" ok-title="Aceptar" size="lg" @hide="prueba">
										<!-- <div v-if="items != undefined">
											{{ item.activities[0] }}
										</div> -->
											
										<div slot="modal-title">
											Subir Archivo <span class="font-weight-light">Contratistas</span><br>
											<small class="text-muted">Selecciona archivos pdf's para este item.</small>
										</div>

										<form-upload-file-list-item-component
										:is-edit="isEdit"
										:view-only="viewOnly"
										v-model="item.files"/>
											
									</b-modal>
								
								</div>
								<div class="text-muted small text-nowrap">
									
									<vue-radio v-model="item.qualification" label="Calificación" :name="`items${item.id}`" :error="form.errorsFor(`items.${index}`)" :options="qualifications"></vue-radio>
									
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
import ActionPlanComponent from '@/components/CustomInputs/ActionPlanComponent.vue';
import Form from "@/utils/Form.js";
import FormUploadFileListItemComponent from '@/components/LegalAspects/ContractLessee/FormUploadFileListItemComponent.vue';


export default {
	components: {
		VueAdvancedSelect,
		VueInput,
		VueCheckbox,
		VueRadio,
		ActionPlanComponent,
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
				return [];
			}
		}
	},
	watch: {
		'contract.items'(newVal) {
			this.loading = false;
			this.form = Form.makeFrom(this.contract, this.method);
			// console.log(this.form.items);
		}
	},
	data() {
		return {
			loading: this.isEdit,
			form: Form.makeFrom(this.contract, this.method)
		};
	},
	methods: {
		//Guardar las calificaciones y validarlas en el backend con un request nuevo
		submit(e) {
			this.loading = true;
			// console.log(this.form);
			this.form
			.submit(e.target.action)
			.then(response => {
				this.loading = false;
				// this.$router.push({ name: "legalaspects-contracts" });
			})
			.catch(error => {
				this.loading = false;
			});
		},
		prueba(){
			console.log(this.form.items);
		}
	}
};
</script>
