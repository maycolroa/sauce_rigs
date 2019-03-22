<template>
    <b-row>
        <b-col>
			<b-form :action="url" @submit.prevent="submit" autocomplete="off">
				<b-card border-variant="primary" no-body class="mb-4">
				<b-card-header header-tag="h6" class="with-elements">
					<div class="card-header-title">Lista de items | {{ name }}</div>
					<div class="card-header-elements ml-auto">
					<b-btn variant="default" class="btn-xs md-btn-flat">Show more</b-btn>
					</div>
				</b-card-header>
				<!-- <perfect-scrollbar style="height: 350px"> -->
					<b-card-body>
					<div class="rounded ui-bordered p-3 mb-3"  v-for="(item, index) in form.items" :key="item.id">
						<p class="my-1">{{ index+1 }} - {{ item.item_name }}</p>
						<span class="text-muted">{{ item.criterion_description}}</span>
						<div class="media align-items-center mt-3">
							<!-- <img src="/static/img/avatars/12-small.png" class="d-block ui-w-30 rounded-circle" alt> -->
							<div class="media-body ml-2">
								<b-btn v-if="item.qualification == 2" variant="primary" v-b-modal="`modals-default-${index+1}`">Plan de acción</b-btn>
								<!-- Modal template -->
								<!-- <b-modal :id="`modals-default-${index+1}`" cancel-title="Cancelar" ok-title="Aceptar" size="lg" @ok="add_plan_action(index)" :ok-disabled="action_plan.description != '' && action_plan.responsible != '' && action_plan.state != '' ? false : true"> -->
									
										
									<!-- <div slot="modal-title">
										Plan de acción <span class="font-weight-light">Contratistas</span><br>
										<small class="text-muted">Crea planes de acción para tu justificación</small>
									</div>

									<b-form-row>
										<vue-input class="col-md-12" v-model="action_plan.description" label="Descripción" type="text" name="description"  placeholder="Descripción"></vue-input>
									</b-form-row>

									<b-form-row>
										<vue-advanced-select class="col-md-6" v-model="action_plan.responsible"  name="responsible" label="Nombre del responsable" placeholder="Seleccione el responsable" :options="[{name: 'Juanito', value: 'Juanito'},{name: 'Jeferson', value: 'Jeferson'}]">
                        				</vue-advanced-select>
										<vue-advanced-select class="col-md-6" v-model="action_plan.state" name="state" label="Estado" placeholder="Seleccione el estado" :options="[{name: 'Pendiente', value: 'Pendiente'},{name: 'Ejecutada', value: 'Ejecutada'}]">
                        				</vue-advanced-select>
									</b-form-row> -->
								<!-- </b-modal> -->
							<!-- 	<b-card bg-variant="transparent" border-variant="secondary" title="" class="mb-3 box-shadow-none">
											<action-plan-component
											:is-edit="isEdit"
											:view-only="viewOnly"
											:form="contract"
											:prefix-index="`items.${index}.`"
											:action-plan-states="actionPlanStates"
											v-model="contract.actionPlan"
											:action-plan="contract.actionPlan"/>
										</b-card> -->
							</div>
							<div class="text-muted small text-nowrap">
								
								<vue-radio v-model="item.qualification" label="Calificación" :name="`items${item.id}`" :error="form.errorsFor('item.qualification')" :options="qualifications"></vue-radio>
								
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

export default {
	components: {
		VueAdvancedSelect,
		VueInput,
		VueCheckbox,
		VueRadio,
		ActionPlanComponent
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
				return {
			/* 		actionPlan: {
						activities: [],
						activitiesRemoved: []
					} */
				}
			}
		},
		actionPlanStates: {
			type: Array,
			default: function() {
				return [
					{ name: 'Pendiete', value: 'Pendiente'}
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
			form: Form.makeFrom(this.contract, this.method)
		};
	},
	methods: {
		//Guardar las calificaciones y validarlas en el backend con un request nuevo
		submit(e) {
			//Cambiar a form
			this.loading = true;
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
	}
};
</script>
