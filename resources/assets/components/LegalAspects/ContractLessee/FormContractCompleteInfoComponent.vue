<template>
    <b-row>
        <b-col>
            <b-form :action="url" @submit.prevent="submit" autocomplete="off">
				<b-card border-variant="primary" class="mb-3 box-shadow-none">
                    <b-form-row>
                        <vue-input :disabled="viewOnly" class="col-md-6" v-model="form.address" label="Dirección" type="text" name="address" :error="form.errorsFor('address')" placeholder="Ej: Calle 123 #12-34"></vue-input>
                        <vue-input :disabled="viewOnly" class="col-md-6" v-model="form.phone" label="Teléfono" type="number" name="phone" :error="form.errorsFor('phone')" placeholder="Teléfono"></vue-input>
                    </b-form-row>

					<b-form-row>
                        <vue-input :disabled="viewOnly" class="col-md-6" v-model="form.legal_representative_name" label="Nombre del representante legal" type="text" name="legal_representative_name" :error="form.errorsFor('legal_representative_name')" placeholder="Nombre del representante legal"></vue-input>
                        <vue-input :disabled="viewOnly" class="col-md-6" v-model="form.SG_SST_name" label="Nombre del responsable del SG-SST" type="text" name="SG_SST_name" :error="form.errorsFor('SG_SST_name')" placeholder="Nombre del responsable del SG-SST"></vue-input>
                    </b-form-row>

					<b-form-row>
                        <vue-input :disabled="viewOnly" class="col-md-6" v-model="form.environmental_management_name" label="Nombre del encargado de gestión ambiental" type="text" name="environmental_management_name" :error="form.errorsFor('environmental_management_name')" placeholder="Nombre del encargado de gestión ambiental"></vue-input>
                        <vue-input :disabled="viewOnly" class="col-md-6" v-model="form.economic_activity_of_company" label="Actividad económica de la empresa" type="text" name="economic_activity_of_company" :error="form.errorsFor('economic_activity_of_company')" placeholder="Actividad económica de la empresa"></vue-input>
                    </b-form-row>

                    <b-form-row>
                        <vue-input :disabled="viewOnly" class="col-md-6" v-model="form.arl" label="Arl" type="text" name="arl" :error="form.errorsFor('arl')" placeholder="Arl"></vue-input>
                        <vue-input :disabled="viewOnly" class="col-md-6" v-model="form.number_workers"  type="number" :error="form.errorsFor('number_workers')" name="number_workers" label="Número de trabajadores" placeholder="Número de trabajadores"></vue-input>
                    </b-form-row>

					<b-form-row>
                        <vue-advanced-select :disabled="viewOnly" class="col-md-6" v-model="form.risk_class" :error="form.errorsFor('risk_class')" name="risk_class" label="Clase de riesgo" placeholder="Seleccione el clase riesgo" :options="form.risk_class_list">
                        </vue-advanced-select>
                    </b-form-row>

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
import VueInput from "@/components/Inputs/VueInput.vue";
import Form from "@/utils/Form.js";

export default {
	components: {
		VueAdvancedSelect,
		VueInput
	},
	props: {
		url: { type: String },
		method: { type: String },
		cancelUrl: { type: [String, Object], required: true },
		isEdit: { type: Boolean, default: false },
		viewOnly: { type: Boolean, default: false },
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
					risk_class_list: [
						{ name: 'Clase de riesgo I, II, III', value: 'Clase de riesgo I, II, III'},
						{ name: 'Clase de riesgo IV y V', value: 'Clase de riesgo IV y V'},
						{ name: 'Cualquier clase de riesgo', value: 'Cualquier clase de riesgo'}
					]
				};
			}
		}
	},
	watch: {
		user() {
			this.loading = false;
			this.form = Form.makeFrom(this.contract, this.method);
		}
	},
	data() {
		return {
			loading: this.isEdit,
			form: Form.makeFrom(this.contract, this.method),
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
		}
	}
};
</script>
