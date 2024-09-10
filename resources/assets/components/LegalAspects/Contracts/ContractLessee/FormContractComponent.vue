<template>
    <b-row>
        <b-col>
            <b-form :action="url" @submit.prevent="submit" autocomplete="off">
				<b-card border-variant="primary" title="Datos Responsable SST Empresa Contratista" class="mb-3 box-shadow-none" v-if="!isEdit && !viewOnly">
                    <b-form-row>
                        <vue-input :disabled="viewOnly" class="col-md-6" v-model="form.name" label="Nombre" type="text" name="name" :error="form.errorsFor('name')" placeholder="Nombre"></vue-input>
                        <vue-input :disabled="viewOnly" class="col-md-6" v-model="form.document" label="Documento" type="text" name="document" :error="form.errorsFor('document')" placeholder="Documento"></vue-input>
                    </b-form-row>

                    <b-form-row>
						<vue-input :disabled="viewOnly" class="col-md-12" v-model="form.email" label="Email" type="text" name="email" :error="form.errorsFor('email')" placeholder="Email" help-text="El correo electronico a ingresar preferiblemente debe ser un correo corporativo"></vue-input>
                    </b-form-row>
            	</b-card>
				<b-card border-variant="primary" title="Datos Responsable SST Empresa Contratista" class="mb-3 box-shadow-none" v-if="isEdit || viewOnly">
                    <b-form-row>
                        <vue-input :disabled="true" class="col-md-6" v-model="form.sst_user.name" label="Nombre" type="text" name="name" :error="form.errorsFor('name')" placeholder="Nombre"></vue-input>
                        <vue-input :disabled="true" class="col-md-6" v-model="form.sst_user.document" label="Documento" type="text" name="document" :error="form.errorsFor('document')" placeholder="Documento"></vue-input>
                    </b-form-row>
                    <b-form-row>
						<vue-input :disabled="true" class="col-md-12" v-model="form.sst_user.email" label="Email" type="text" name="email" :error="form.errorsFor('email')" placeholder="Email"></vue-input>
                    </b-form-row>
                    <b-form-row>						
						<vue-radio :disabled="viewOnly" class="col-md-6 offset-md-3" v-model="form.change_sst" :options="siNo" name="high_risk_work" :error="form.errorsFor('high_risk_work')" label="¿Desea cambiar el responsable SST?"></vue-radio>
                    </b-form-row>
                    <b-form-row v-if="form.change_sst == 'SI'">			
						<vue-ajax-advanced-select :disabled="viewOnly" class="col-md-12" v-model="form.sst_user_id" :error="form.errorsFor('sst_user_id')" :selected-object="form.multiselect_sst_user" :multiple="false" :allowEmpty="true" name="sst_user_id" label="Usuarios" placeholder="Seleccione al nuevo responsable" :url="usersDataUrl">
						</vue-ajax-advanced-select>
                    </b-form-row>

            	</b-card>
				<b-card border-variant="primary" title="Datos empresariales" class="mb-3 box-shadow-none">
					<b-form-row>
						<vue-advanced-select :disabled="viewOnly" class="col-md-6" v-model="form.type" :error="form.errorsFor('type')" name="type" label="Tipo" placeholder="Seleccione el tipo" :options="roles">
                        </vue-advanced-select>
                        <vue-input :disabled="viewOnly" class="col-md-6" v-model="form.business_name" label="Nombre Comercial" type="text" name="business_name" :error="form.errorsFor('business_name')" placeholder="Nombre Comercial"></vue-input>
                    </b-form-row>
					<b-form-row>
						<vue-input :disabled="viewOnly" class="col-md-6" v-model="form.nit" label="Nit" type="number" name="nit" :error="form.errorsFor('nit')" placeholder="Nit o número de indetificación"></vue-input>
						<vue-input :disabled="viewOnly" class="col-md-6" v-model="form.social_reason" label="Razón social" type="text" name="social_reason" :error="form.errorsFor('social_reason')" placeholder="Razón social"></vue-input>
					</b-form-row>
					<b-form-row>
						<vue-advanced-select v-if="form.type == 'Contratista' || form.type == 'Proveedor'" :disabled="viewOnly" class="col-md-6" v-model="form.classification" :error="form.errorsFor('classification')" name="classification" label="Clasificación" placeholder="Seleccione una clasificación" :options="contractClassifications">
                        </vue-advanced-select>
						<vue-radio :disabled="viewOnly" :checked="form.high_risk_work" class="col-md-6 offset-md-3" v-model="form.high_risk_work" :options="siNo" name="high_risk_work" :error="form.errorsFor('high_risk_work')" label="¿La empresa realiza tareas de alto riesgo?"></vue-radio>
					</b-form-row>
					<b-form-row v-show="form.high_risk_work == 'SI'">
						<vue-ajax-advanced-select :disabled="viewOnly" class="col-md-6 offset-md-3" v-model="form.high_risk_type_id" :error="form.errorsFor('high_risk_type_id')" :selected-object="form.multiselect_high_risk_type" :multiple="true" :allowEmpty="true" name="high_risk_type_id" label="Tareas de riesgos" placeholder="Seleccione las tareas de alto riesgo" :url="highRiskTypeUrl">
						</vue-ajax-advanced-select>
					</b-form-row>
					<b-form-row>
						<vue-ajax-advanced-select :disabled="viewOnly" class="col-md-12" v-model="form.activity_id" :error="form.errorsFor('activity_id')" :selected-object="form.multiselect_activity" :multiple="true" :allowEmpty="true" name="activity_id" label="Actividades" placeholder="Seleccione las actividades a asignar" :url="activitiesUrl">
						</vue-ajax-advanced-select>
					</b-form-row>

					<b-form-row v-if="auth.proyectContract == 'SI'">
						<vue-ajax-advanced-select :disabled="viewOnly" class="col-md-12" v-model="form.proyects_id" :error="form.errorsFor('proyects_id')" :selected-object="form.multiselect_proyect" :multiple="true" :allowEmpty="true" name="proyects_id" label="Proyectos" placeholder="Seleccione las proyectos a asignar" :url="proyectsUrl">
						</vue-ajax-advanced-select>
					</b-form-row>

					<b-form-row v-if="isEdit || viewOnly">
						<vue-checkbox-simple style="padding-top: 20px;" :disabled="viewOnly" class="col-md-6" v-model="form.active" label="¿Activo?" :checked="form.active" name="active" checked-value="SI" unchecked-value="NO"></vue-checkbox-simple>
					</b-form-row>
					<b-form-row>
						<vue-advanced-select :disabled="viewOnly" class="col-md-12" v-model="form.users_responsibles" :selected-object="form.multiselect_users_responsibles" :error="form.errorsFor('users_responsibles')" name="users_responsibles" label="Responsable del contratista" placeholder="Seleccione el responsable del contratista" :options="usersResponsibles" :multiple="true" :searchable="true">
						</vue-advanced-select>
					</b-form-row>
            	</b-card>

				<b-card border-variant="primary" class="mb-3 box-shadow-none">
                    <b-form-row>
                        <vue-input :disabled="true" class="col-md-6" v-model="form.address" label="Dirección" type="text" name="address" :error="form.errorsFor('address')" placeholder="Ej: Calle 123 #12-34"></vue-input>
                        <vue-input :disabled="true" class="col-md-6" v-model="form.phone" label="Teléfono" type="number" name="phone" :error="form.errorsFor('phone')" placeholder="Teléfono"></vue-input>
                    </b-form-row>

					<b-form-row>
						<vue-input :disabled="true" class="col-md-6" v-model="form.email_contract" label="Email Contratista" type="text" name="email_contract" :error="form.errorsFor('email_contract')" placeholder="Email Contratista"></vue-input>
                        <vue-input :disabled="true" class="col-md-6" v-model="form.legal_representative_name" label="Nombre del representante legal" type="text" name="legal_representative_name" :error="form.errorsFor('legal_representative_name')" placeholder="Nombre del representante legal"></vue-input>
                    </b-form-row>

					<b-form-row>
                        <vue-input :disabled="true" class="col-md-6" v-model="form.SG_SST_name" label="Nombre del responsable del SG-SST" type="text" name="SG_SST_name" :error="form.errorsFor('SG_SST_name')" placeholder="Nombre del responsable del SG-SST"></vue-input>
                        <vue-input :disabled="true" class="col-md-6" v-model="form.environmental_management_name" label="Nombre del encargado de gestión ambiental" type="text" name="environmental_management_name" :error="form.errorsFor('environmental_management_name')" placeholder="Nombre del encargado de gestión ambiental"></vue-input>
                    </b-form-row>

                    <b-form-row>
                        <vue-input :disabled="true" class="col-md-6" v-model="form.human_management_coordinator" label="Coordinador de gestión humana" type="text" name="human_management_coordinator" :error="form.errorsFor('human_management_coordinator')" placeholder="Coordinador de gestión humana"></vue-input>
                        <vue-input :disabled="true" class="col-md-6" v-model="form.economic_activity_of_company" label="Actividad económica de la empresa" type="text" name="economic_activity_of_company" :error="form.errorsFor('economic_activity_of_company')" placeholder="Actividad económica de la empresa"></vue-input>
                    </b-form-row>

					<b-form-row>
						<vue-ajax-advanced-select-tag-unic :disabled="true" class="col-md-6" v-model="form.arl" name="arl" :error="form.errorsFor('arl')" label="ARL" placeholder="Seleccione la ARL" :url="tagsCtArlDataUrl" :multiple="false" :allowEmpty="true" :taggable="true">
						</vue-ajax-advanced-select-tag-unic>
						<vue-input :disabled="true" class="col-md-6" v-model="form.number_workers"  type="number" :error="form.errorsFor('number_workers')" name="number_workers" label="Número de trabajadores" placeholder="Número de trabajadores"></vue-input>
                    </b-form-row>

					<b-form-row>
                        <vue-advanced-select :disabled="true" class="col-md-6" v-model="form.risk_class" :error="form.errorsFor('risk_class')" name="risk_class" label="Clase de riesgo" placeholder="Seleccione la clase riesgo" :options="kindsRisks">
                        </vue-advanced-select>
						<vue-input :disabled="true" class="col-md-6" v-model="form.email_training_employee"  type="text" :error="form.errorsFor('email_training_employee')" name="email_training_employee" label="Correo para envio de capacitaciones de los empleados (Opcional)" placeholder="Correo para envio de capacitaciones de los empleados"></vue-input>
                    </b-form-row>

					<b-form-row>
						<vue-ajax-advanced-select :disabled="true" class="col-md-6" v-model="form.height_training_centers" name="height_training_centers" :error="form.errorsFor('height_training_centers')" label="Centro de entrenamiento de alturas" placeholder="Seleccione el centro"  :url="tagsCtHeightTrainingCenterDataUrl" :multiple="true" :allowEmpty="true" :taggable="true">
						</vue-ajax-advanced-select>
						<vue-ajax-advanced-select-tag-unic :disabled="true" class="col-md-6" v-model="form.social_security_payment_operator" name="social_security_payment_operator" :error="form.errorsFor('social_security_payment_operator')" label="Operador de pago seguridad social" placeholder="Seleccione el operador" :url="tagsCtSocialSecurityDataUrl" :multiple="false" :allowEmpty="true" :taggable="true">
						</vue-ajax-advanced-select-tag-unic>
                    </b-form-row>

					<b-form-row>
						<vue-ajax-advanced-select :disabled="true" class="col-md-6" v-model="form.ips" name="ips" :error="form.errorsFor('ips')" label="IPS para examenes medicos" placeholder="Seleccione la IPS" :url="tagsCtIpsDataUrl" :multiple="true" :allowEmpty="true" :taggable="true">
						</vue-ajax-advanced-select>

                        <vue-input class="col-md-6" :disabled="true" v-model="form.social_security_working_day" label="Dia habil seguridad social" type="number" name="social_security_working_day" :error="form.errorsFor('social_security_working_day')" placeholder="Dia habil seguridad social"></vue-input>
                    </b-form-row>

            	</b-card>

				<b-card border-variant="primary" v-show="isEdit || viewOnly" title="Usuarios del contratista" class="mb-3 box-shadow-none">
					<table class="table table-bordered table-sm table-striped table-hover" style="width: 100%; font-size: 10px;">
						<tbody>
							<tr v-for="(user, index) in usersContract" :key="user.id">
								<td style='text-center align-middle'>
									{{ index + 1 }} . {{ user.name }} - {{ user.email}}
								</td>
								<td v-if="form.user_sst_id > 0 ? form.user_sst_id == user.id : index == 0">
									<b-btn @click="editUser(user.id)" variant="outline-success icon-btn borderless" size="xs" v-b-tooltip.top title="Editar Usuario"><span class="ion ion-md-create"></span></b-btn>
								</td>
							</tr>
						</tbody>
					</table>
				</b-card>

				<div class="row float-right pt-10 pr-10">
                    <template>
                        <b-btn variant="default" :to="cancelUrl" :disabled="loading">{{ viewOnly ? "Atras" : "Cancelar"}}</b-btn>&nbsp;&nbsp;
                        <b-btn type="submit" :disabled="loading" variant="primary" v-if="!viewOnly">Finalizar</b-btn>
                    </template>
                </div>
            </b-form>
        </b-col>
    </b-row>
</template>

<script>

import VueAdvancedSelect from "@/components/Inputs/VueAdvancedSelect.vue";
import VueAjaxAdvancedSelect from "@/components/Inputs/VueAjaxAdvancedSelect.vue";
import VueInput from "@/components/Inputs/VueInput.vue";
import VueCheckboxSimple from "@/components/Inputs/VueCheckboxSimple.vue";
import VueRadio from "@/components/Inputs/VueRadio.vue";
import VueAjaxAdvancedSelectTagUnic from "@/components/Inputs/VueAjaxAdvancedSelectTagUnic.vue";
import VueDatepicker from "@/components/Inputs/VueDatepicker.vue";
import Form from "@/utils/Form.js";

export default {
	components: {
		VueAdvancedSelect,
		VueAjaxAdvancedSelect,
		VueInput,
		VueCheckboxSimple,
		VueRadio,
		VueAjaxAdvancedSelectTagUnic,
		VueDatepicker
	},
	props: {
		url: { type: String },
		method: { type: String },
		cancelUrl: { type: [String, Object], required: true },
		isEdit: { type: Boolean, default: false },
		viewOnly: { type: Boolean, default: false },
		highRiskTypeUrl: { type: String, default: "" },		
		activitiesUrl: { type: String, default: "" },
		kindsRisks: {
			type: Array,
			default: function() {
				return [];
			}
		},
		roles: {
			type: Array,
			default: function() {
				return [];
			}
		},
		contractClassifications: {
			type: Array,
			default: function() {
				return [];
			}
		},
		usersResponsibles: {
			type: Array,
			default: function() {
				return [];
			}
		},
		usersContract: {
			type: Array,
			default: function() {
				return [];
			}
		},
		siNo: {
			type: Array,
			default: function() {
				return [];
			}
		},
		contract: {
			default() {
				return {
					name: '',
					email: '',
					document: '',
					type: '',
					business_name: '',
					nit: '',
					classification: '',
					social_reason: '',
					high_risk_work: '',
					high_risk_type_id: [],
					users_responsibles: [],
					activity_id: [],
					proyects_id: []
				};
			}
		}
	},
	watch: {
		contract() {
			this.loading = false;
			this.form = Form.makeFrom(this.contract, this.method);
		}
	},
	data() {
		return {
			loading: this.isEdit,
			form: Form.makeFrom(this.contract, this.method),
			proyectsUrl: '/selects/contracts/ctProyects',
			tagsCtHeightTrainingCenterDataUrl: '/selects/tagsCtHeightTrainingCenter',
			tagsCtSocialSecurityDataUrl: '/selects/tagsCtSocialSecurity',
			tagsCtIpsDataUrl: '/selects/tagsCtIps',
			tagsCtArlDataUrl: '/selects/tagsCtArl',
			usersDataUrl: '/selects/usersContracts',
		};
	},
	methods: {
		submit(e) {
		this.loading = true;
		this.form
			.submit(e.target.action)
			.then(response => {
				this.loading = false;
				this.$router.push({ name: "legalaspects-contractor" });
			})
			.catch(error => {
				this.loading = false;
			});
		},
		editUser(id)
		{
			this.$router.push({name: 'administrative-users-edit', params : { id }});
		}
	}
};
</script>
