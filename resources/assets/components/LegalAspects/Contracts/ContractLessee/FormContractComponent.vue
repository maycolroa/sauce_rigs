<template>
    <b-row>
        <b-col>
            <b-form :action="url" @submit.prevent="submit" autocomplete="off">
				<b-card border-variant="primary" title="Datos personales" class="mb-3 box-shadow-none" v-if="!isEdit && !viewOnly">
                    <b-form-row>
                        <vue-input :disabled="viewOnly" class="col-md-6" v-model="form.name" label="Nombre" type="text" name="name" :error="form.errorsFor('name')" placeholder="Nombre"></vue-input>
                        <vue-input :disabled="viewOnly" class="col-md-6" v-model="form.document" label="Documento" type="text" name="document" :error="form.errorsFor('document')" placeholder="Documento"></vue-input>
                    </b-form-row>

                    <b-form-row>
						<vue-input :disabled="viewOnly" class="col-md-12" v-model="form.email" label="Email" type="text" name="email" :error="form.errorsFor('email')" placeholder="Email"></vue-input>
                    </b-form-row>
            	</b-card>
				<b-card border-variant="primary" title="Datos empresariales" class="mb-3 box-shadow-none">
					<b-form-row>
						<vue-advanced-select :disabled="viewOnly" class="col-md-6" v-model="form.type" :error="form.errorsFor('type')" name="type" label="Tipo" placeholder="Seleccione el tipo" :options="roles">
                        </vue-advanced-select>
                        <vue-input :disabled="viewOnly" class="col-md-6" v-model="form.business_name" label="Nombre de la empresa" type="text" name="business_name" :error="form.errorsFor('business_name')" placeholder="Nombre de la empresa"></vue-input>
                    </b-form-row>
					<b-form-row>
						<vue-input :disabled="viewOnly" class="col-md-6" v-model="form.nit" label="Nit" type="number" name="nit" :error="form.errorsFor('nit')" placeholder="Nit o número de indetificación"></vue-input>
						<vue-input :disabled="viewOnly" class="col-md-6" v-model="form.social_reason" label="Razón social" type="text" name="social_reason" :error="form.errorsFor('social_reason')" placeholder="Razón social"></vue-input>
					</b-form-row>
					<b-form-row>
						<vue-advanced-select v-if="form.type == 'Contratista'" :disabled="viewOnly" class="col-md-6" v-model="form.classification" :error="form.errorsFor('classification')" name="classification" label="Clasificación" placeholder="Seleccione una clasificación" :options="contractClassifications">
                        </vue-advanced-select>
						<vue-checkbox-simple style="padding-top: 30px;" :disabled="viewOnly" class="col-md-6" v-model="form.high_risk_work" label="¿Trabajo de alto riesgo?" :checked="form.high_risk_work" name="high_risk_work" checked-value="SI" unchecked-value="NO"></vue-checkbox-simple>
					</b-form-row>
					<b-form-row v-if="isEdit || viewOnly">
						<vue-checkbox-simple style="padding-top: 20px;" :disabled="viewOnly" class="col-md-6" v-model="form.active" label="¿Activo?" :checked="form.active" name="active" checked-value="SI" unchecked-value="NO"></vue-checkbox-simple>
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
import VueCheckboxSimple from "@/components/Inputs/VueCheckboxSimple.vue";
import Form from "@/utils/Form.js";

export default {
	components: {
		VueAdvancedSelect,
		VueInput,
		VueCheckboxSimple
	},
	props: {
		url: { type: String },
		method: { type: String },
		cancelUrl: { type: [String, Object], required: true },
		isEdit: { type: Boolean, default: false },
		viewOnly: { type: Boolean, default: false },
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
					high_risk_work: ''
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
		}
	}
};
</script>
