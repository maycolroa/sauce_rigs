<template>
    <b-row>
        <b-col>
            <b-form :action="url" @submit.prevent="submit" autocomplete="off">
				<b-card border-variant="primary" class="mb-3 box-shadow-none">
					<b-form-row>
						<vue-ajax-advanced-select :disabled="viewOnly" class="col-md-12" v-model="form.contract_id" :selected-object="form.multiselect_contract_id" name="contract_id" label="Contratistas" placeholder="Seleccione las contratistas" :url="contractDataUrl" :error="form.errorsFor('contract_id')" :multiple="false" :allowEmpty="true">
                            </vue-ajax-advanced-select>
					</b-form-row>

					<b-form-row>
						<vue-ajax-advanced-select :disabled="viewOnly || !form.contract_id" class="col-md-12" v-model="form.employee_id" :selected-object="form.multiselect_employee_id" name="employee_id" label="Empleados" placeholder="Seleccione el empleado" :url="employeeContractDataUrl" :error="form.errorsFor('employee_id')" :multiple="false" :allowEmpty="true" :parameters="{contract_id: form.contract_id}">
                            </vue-ajax-advanced-select>
					</b-form-row>

					<b-form-row>
						<vue-ajax-advanced-select :disabled="viewOnly || !form.contract_id || !form.employee_id" class="col-md-12" v-model="form.documents_id" :selected-object="form.multiselect_documents_id" name="documents_id" label="Documentos" placeholder="Seleccione los documentos" :url="employeeDocumentContractDataUrl" :error="form.errorsFor('documents_id')" :multiple="true" :allowEmpty="true" :parameters="{contract_id: form.contract_id, employee_id: form.employee_id}">
                            </vue-ajax-advanced-select>
					</b-form-row>

					<b-form-row v-if="form.contract_id && form.employee_id && form.documents_id">
						<template>
							<b-btn class="col-md-2 offset-md-5" variant="primary" @click="descargarPdf()">Descargar</b-btn>&nbsp;&nbsp;
						</template>
					</b-form-row>

            	</b-card>
				<div class="row float-right pt-10 pr-10">
                    <template>
                        <b-btn variant="default" :to="cancelUrl" :disabled="loading">Atras</b-btn>&nbsp;&nbsp;
                    </template>
                </div>
            </b-form>
        </b-col>
    </b-row>
</template>

<script>

import VueInput from "@/components/Inputs/VueInput.vue";
import VueFileSimple from "@/components/Inputs/VueFileSimple.vue";
import VueDatepicker from "@/components/Inputs/VueDatepicker.vue";
import VueAjaxAdvancedSelect from "@/components/Inputs/VueAjaxAdvancedSelect.vue";
import VueAdvancedSelect from "@/components/Inputs/VueAdvancedSelect.vue";
import VueTextarea from "@/components/Inputs/VueTextarea.vue";
import Form from "@/utils/Form.js";
import Alerts from '@/utils/Alerts.js';

export default {
	components: {
		VueInput,
		VueFileSimple,
		VueDatepicker,
		VueAjaxAdvancedSelect,
		VueAdvancedSelect,
		VueTextarea
	},
	props: {
		url: { type: String },
		method: { type: String },
		cancelUrl: { type: [String, Object], required: true },
		isEdit: { type: Boolean, default: false },
		viewOnly: { type: Boolean, default: false },
		states: {
	      type: Array,
	      default: function() {
	        return [];
	      }
	    },
		fileUpload: {
			default() {
				return {
					contract_id: '',
					employee_id: '',
					documents_id: ''
				};
			}
		}
	},
	watch: {
		fileUpload() {
			this.loading = false;
			this.form = Form.makeFrom(this.fileUpload, this.method);
		}
	},
	data() {
		return {
			loading: this.isEdit,
			form: Form.makeFrom(this.fileUpload, this.method),
			contractDataUrl: '/selects/contractors',
			employeeContractDataUrl: '/selects/contracts/ctEmployeeContracts',
			employeeDocumentContractDataUrl: '/selects/contracts/ctEmployeeDocumentsContracts',
			disabledDates: {
				to: new Date()
			}
		};
	},
	methods: {
		submit(e) {
		this.loading = true;
		this.form
			.submit(e.target.action)
			.then(response => {
				this.loading = false;
				this.$router.push({ name: "legalaspects-upload-files" });
			})
			.catch(error => {
				this.loading = false;
			});
		},
		descargarPdf() {
			let postData = Object.assign({}, {contract_id: this.form.contract_id, employee_id: this.form.employee_id, documents_id: this.form.documents_id});

			axios.post('/legalAspects/fileUpload/downloadMerge', postData, { responseType: 'blob' })
			.then(response => {
				// create file link in browser's memory
				const href = URL.createObjectURL(response.data);

				// create "a" HTML element with href to file & click
				const link = document.createElement('a');
				link.href = href;
				link.setAttribute('download', response.headers['file-name']); //or any other extension
				document.body.appendChild(link);
				link.click();

				// clean up "a" element & remove ObjectURL
				document.body.removeChild(link);
				URL.revokeObjectURL(href);
			})
			.catch(error => {
				Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
			});
		}
	}
};
</script>
