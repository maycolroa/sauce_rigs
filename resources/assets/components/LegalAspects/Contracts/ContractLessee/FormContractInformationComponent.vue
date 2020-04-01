<template>
    <b-row>
        <b-col>
            <b-form :action="url" @submit.prevent="submit" autocomplete="off">
				<b-card bg-variant="transparent" border-variant="primary" title="Datos empresariales" class="mb-3 box-shadow-none">
					<b-row>
						<b-col>
							<div><b>Tipo:</b> {{ form.type }}</div>
							<div><b>Nombre de la empresa:</b> {{ form.business_name }}</div>
							<div><b>Nit:</b> {{ form.nit }}</div>
						</b-col>
						<b-col>
							<div><b>Razón social:</b> {{ form.social_reason }}</div>
							<div v-if="form.type == 'Contratista'"><b>Clasificación:</b> {{ form.classification }}</div>
							<div><b>¿Trabajo de alto riesgo?:</b> {{ form.high_risk_work }}</div>
						</b-col>
					</b-row>
					<br><br>
					<!--<center v-if="auth.hasRole['Contratista'] && form.existsOthersContract">
			            <b-btn variant="primary" size="md" @click="$refs.modalTransfer.show()" >Copiar valores de lista de chequeo desde otro contratista</b-btn>
			         </center>-->
				</b-card>

				<b-modal ref="modalTransfer" :hideFooter="true" id="modals-historial" class="modal-top" size="lg">
					<div slot="modal-title">
						<h4>Seleccione el contratista del cual desea copiar los valores</h4>
					</div>
					<center>
						<vue-advanced-select class="col-md-12" v-model="contract_select" :error="form.errorsFor('contract_select')" name="contract_select" label="Contratista" placeholder="Seleccione el contratista" :options="form.multiselect_contracts">
                        </vue-advanced-select>
                	</center>

					<div class="row float-right pt-12 pr-12y">						
                        <b-btn @click="listCheckCopy()" variant="primary">Copiar</b-btn>&nbsp;&nbsp;
						<b-btn variant="primary" @click="$refs.modalTransfer.hide()">Cerrar</b-btn>
					</div>
				</b-modal>

				<b-card border-variant="primary" class="mb-3 box-shadow-none">
                    <b-form-row>
                        <vue-input class="col-md-6" v-model="form.address" label="Dirección" type="text" name="address" :error="form.errorsFor('address')" placeholder="Ej: Calle 123 #12-34"></vue-input>
                        <vue-input class="col-md-6" v-model="form.phone" label="Teléfono" type="number" name="phone" :error="form.errorsFor('phone')" placeholder="Teléfono"></vue-input>
                    </b-form-row>

					<b-form-row>
                        <vue-input class="col-md-6" v-model="form.legal_representative_name" label="Nombre del representante legal" type="text" name="legal_representative_name" :error="form.errorsFor('legal_representative_name')" placeholder="Nombre del representante legal"></vue-input>
                        <vue-input class="col-md-6" v-model="form.SG_SST_name" label="Nombre del responsable del SG-SST" type="text" name="SG_SST_name" :error="form.errorsFor('SG_SST_name')" placeholder="Nombre del responsable del SG-SST"></vue-input>
                    </b-form-row>

					<b-form-row>
                        <vue-input class="col-md-6" v-model="form.environmental_management_name" label="Nombre del encargado de gestión ambiental" type="text" name="environmental_management_name" :error="form.errorsFor('environmental_management_name')" placeholder="Nombre del encargado de gestión ambiental"></vue-input>
                        <vue-input class="col-md-6" v-model="form.economic_activity_of_company" label="Actividad económica de la empresa" type="text" name="economic_activity_of_company" :error="form.errorsFor('economic_activity_of_company')" placeholder="Actividad económica de la empresa"></vue-input>
                    </b-form-row>

                    <b-form-row>
                        <vue-input class="col-md-6" v-model="form.arl" label="Arl" type="text" name="arl" :error="form.errorsFor('arl')" placeholder="Arl"></vue-input>
                        <vue-input class="col-md-6" v-model="form.number_workers"  type="number" :error="form.errorsFor('number_workers')" name="number_workers" label="Número de trabajadores" placeholder="Número de trabajadores"></vue-input>
                    </b-form-row>

					<b-form-row>
                        <vue-advanced-select class="col-md-6" v-model="form.risk_class" :error="form.errorsFor('risk_class')" name="risk_class" label="Clase de riesgo" placeholder="Seleccione la clase riesgo" :options="kindsRisks">
                        </vue-advanced-select>
                    </b-form-row>

            	</b-card>
				<div class="row float-right pt-10 pr-10">
                    <template>
                        <b-btn variant="default" :to="cancelUrl" :disabled="loading">Atras</b-btn>&nbsp;&nbsp;
                        <b-btn type="submit" :disabled="loading" variant="primary">Guardar</b-btn>
                    </template>
                </div>
            </b-form>
        </b-col>
    </b-row>
</template>

<script>

import VueAdvancedSelect from "@/components/Inputs/VueAdvancedSelect.vue";
import VueInput from "@/components/Inputs/VueInput.vue";
import Form from "@/utils/Form.js";
import Alerts from '@/utils/Alerts.js';

export default {
	components: {
		VueAdvancedSelect,
		VueInput
	},
	props: {
		url: { type: String },
		method: { type: String },
		cancelUrl: { type: [String, Object], required: true },
		kindsRisks: {
			type: Array,
			default: function() {
				return [];
			}
		},
		contract: {
			default() {
				return {
					address: '',
					phone: '',
					legal_representative_name: '',
					SG_SST_name: '',
					environmental_management_name: '',
					economic_activity_of_company: '',
					arl: '',
					number_workers: '',
					risk_class: '',
					multiselect_contracts: [],
					existsOthersContract: false
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
			loading: false,
			form: Form.makeFrom(this.contract, this.method),
			contract_select: ''
		};
	},
	methods: {
		submit(e) {
		this.loading = true;
		this.form
			.submit(e.target.action)
			.then(response => {
				this.loading = false;
				this.$router.push({ name: "legalaspects-contracts" });
			})
			.catch(error => {
				this.loading = false;
			});
		},
		listCheckCopy() {

			if (!this.contract_select)
			{
				Alerts.error('Error', 'Debe seleccionar un contratista');
				return;
			}

	      axios.post('/legalAspects/contracts/listCheckCopy', {contract_selected: this.contract_select})
	        .then(response => {
	          Alerts.warning('Información', 'Estimado usuario, se le notificara a su correo electronico cuando finalice el proceso.');
	          this.$refs.modalTransfer.hide()
	        }).catch(error => {
	          Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
	        });
    	}
	}
};
</script>
