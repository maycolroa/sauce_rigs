<template>
    <b-row>
        <b-col>
            <b-form :action="url" @submit.prevent="submit" autocomplete="off">
				<b-card border-variant="primary" title="Datos personales" class="mb-3 box-shadow-none">
                    <b-form-row>
                        <vue-input :disabled="viewOnly" class="col-md-6" v-model="form.name" label="Nombre" type="text" name="name" :error="form.errorsFor('name')" placeholder="Nombre"></vue-input>
                        <vue-input :disabled="viewOnly" class="col-md-6" v-model="form.email" label="Email" type="text" name="email" :error="form.errorsFor('email')" placeholder="Email"></vue-input>
                    </b-form-row>

                    <b-form-row>
                        <vue-input :disabled="viewOnly" class="col-md-6" v-model="form.document" label="Documento" type="text" name="document" :error="form.errorsFor('document')" placeholder="Documento"></vue-input>
                        <vue-advanced-select :disabled="viewOnly" class="col-md-6" v-model="form.role" :error="form.errorsFor('role')" name="role" label="Rol predefinido" placeholder="Seleccione el rol predefinido" :options="form.role_list">
                        </vue-advanced-select>
                    </b-form-row>
            	</b-card>
				<b-card border-variant="primary" title="Datos empresariales" class="mb-3 box-shadow-none">
					<b-form-row>
                        <vue-input :disabled="viewOnly" class="col-md-6" v-model="form.name_business" label="Nombre de la empresa" type="text" name="name_business" :error="form.errorsFor('name_business')" placeholder="Nombre de la empresa"></vue-input>
                        <vue-input :disabled="viewOnly" class="col-md-6" v-model="form.nit" label="Nit" type="number" name="nit" :error="form.errorsFor('nit')" placeholder="Nit o número de indetificación"></vue-input>
                    </b-form-row>
					<b-form-row>
						<vue-input :disabled="viewOnly" class="col-md-6" v-model="form.social_reason" label="Razón social" type="text" name="social_reason" :error="form.errorsFor('social_reason')" placeholder="Razón social"></vue-input>
						<vue-advanced-select v-if="form.role == 'Contratista'" :disabled="viewOnly" class="col-md-6" v-model="form.classification" :error="form.errorsFor('classification')" name="classification" label="Clasificación" placeholder="Seleccione una clasificación" :options="form.classification_list">
                        </vue-advanced-select>
					</b-form-row>
					<b-form-row>
						<vue-checkbox :disabled="viewOnly" class="col-md-6" v-model="form.high_risk" label="¿Trabajo de alto riesgo?" name="high_risk" :error="form.errorsFor('high_risk')" :options="['si']"></vue-checkbox>
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
import VueCheckbox from "@/components/Inputs/VueCheckbox.vue";
import Form from "@/utils/Form.js";

export default {
	components: {
		VueAdvancedSelect,
		VueInput,
		VueCheckbox
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
					name: '',
					email: '',
					document: '',
					role: '',
					role_list: [
						{ name: 'Arrendatario', value: 'Arrendatario'},
						{ name: 'Contratista', value: 'Contratista'}
					],
					name_business: '',
					nit: '',
					classification: '',
					classification_list: [
						{ name:'Unidad de producción agropecuaria', value:'upa' },
						{ name:'Empresa', value:'empresa' }
					],
					social_reason: '',
					high_risk: '',
					password: ''
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
