<template>
    <b-row>
        <b-col>
            <b-form :action="url" @submit.prevent="submit" autocomplete="off">
				<b-card border-variant="primary" class="mb-3 box-shadow-none">
					<b-form-row v-if="!auth.hasRole['Arrendatario'] && !auth.hasRole['Contratista']">
						<vue-ajax-advanced-select :disabled="viewOnly" class="col-md-12" v-model="form.contract_id" :selected-object="form.multiselect_contract_id" name="contract_id" label="Contratistas" placeholder="Seleccione las contratistas" :url="contractDataUrl" :error="form.errorsFor('contract_id')" :multiple="true" :allowEmpty="true">
                            </vue-ajax-advanced-select>
					</b-form-row>

					<div v-if="form.apply_file == 'SI'">
						<b-form-row>
							<vue-input :disabled="viewOnly" class="col-md-6" v-model="form.name" label="Nombre" type="text" name="name" :error="form.errorsFor('name')" placeholder="Nombre" help-text="El nombre no debe contener caracteres especiales como '/', '.'"></vue-input>
							<vue-datepicker :disabled="viewOnly" class="col-md-6" v-model="form.expirationDate" label="Fecha de vencimiento" :full-month-name="true" placeholder="Seleccione la fecha de vencimiento" :error="form.errorsFor('expirationDate')" name="expirationDate" :disabled-dates="disabledDates">
							</vue-datepicker>
						</b-form-row>

						<b-form-row>
							<vue-file-simple v-if="isEdit || viewOnly" :help-text="`Para descargar el archivo actual, haga click <a href='/legalAspects/fileUpload/download/${this.$route.params.id}' target='blank'>aqui</a> `" :disabled="viewOnly" class="col-md-12" v-model="form.file" label="Archivo" name="file" :error="form.errorsFor('file')" placeholder="Seleccione un archivo" :maxFileSize="20"></vue-file-simple>
							<vue-file-simple v-else help-text="El tamaño del archivo no debe ser mayor a 15MB." :disabled="viewOnly" class="col-md-12" v-model="form.file" label="Archivo" name="file" :error="form.errorsFor('file')" placeholder="Seleccione un archivo" :maxFileSize="20"></vue-file-simple>
						</b-form-row>
					</div>

					<div v-if="form.apply_file == 'NO'">
						<b-form-row>
							<vue-textarea class="col-md-12" v-model="form.apply_motive" label="Motivo por el cual no aplica" name="apply_motive" :error="form.errorsFor('apply_motive')" placeholder="Motivo por el cual no aplica" :disabled="true"></vue-textarea>
						</b-form-row>
					</div>

                    <b-form-row v-if="!auth.hasRole['Arrendatario'] && !auth.hasRole['Contratista']">
						<vue-advanced-select class="col-md-6" v-model="form.state"  name="state" label="Estado del documento" placeholder="Seleccione el estado" :options="states" :error="form.errorsFor('state')" :multiple="false" :allow-empty="false" :disabled="viewOnly">
						</vue-advanced-select>
						<vue-textarea v-if="form.state == 'RECHAZADO'" class="col-md-6" v-model="form.reason_rejection" label="Motivo del rechazo" name="reason_rejection" :error="form.errorsFor('reason_rejection')" placeholder="Motivo del rechazo" :disabled="viewOnly"></vue-textarea>
						<vue-textarea class="col-md-12" v-model="form.observations" label="Observaciones" name="observations" :error="form.errorsFor('observations')" placeholder="Observaciones" :disabled="viewOnly"></vue-textarea>
					</b-form-row>
					<b-form-row v-else>
						<vue-advanced-select class="col-md-6" v-model="form.state"  name="state" label="Estado del documento" placeholder="Seleccione el estado" :options="states" :error="form.errorsFor('state')" :multiple="false" :allow-empty="false" :disabled="true">
						</vue-advanced-select>
						<vue-textarea v-if="form.state == 'RECHAZADO'" class="col-md-6" v-model="form.reason_rejection" label="Motivo del rechazo" name="reason_rejection" :error="form.errorsFor('reason_rejection')" placeholder="Motivo del rechazo" :disabled="true"></vue-textarea>
					</b-form-row>
					<b-form-row v-if="form.file && form.type == 'pdf'">
						<b-card border-variant="primary" class="mb-3 box-shadow-none" style="width: 100%;">
							<iframe style="width: 100%; height: 700px;" frameborder="0" id="frame_imprimir_rendicion" title="Archivo" :src="form.path"></iframe>
						</b-card>
					</b-form-row>
					<b-form-row v-if="form.file && (form.type == 'png' || form.type == 'jpg' || form.type == 'jpeg')">
						<b-card border-variant="primary" class="mb-3 box-shadow-none" style="width: 100%;">
						 	<img class="mw-100" :src="`${form.path}`" alt="Max-width 100%">
						</b-card>
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

import VueInput from "@/components/Inputs/VueInput.vue";
import VueFileSimple from "@/components/Inputs/VueFileSimple.vue";
import VueDatepicker from "@/components/Inputs/VueDatepicker.vue";
import VueAjaxAdvancedSelect from "@/components/Inputs/VueAjaxAdvancedSelect.vue";
import VueAdvancedSelect from "@/components/Inputs/VueAdvancedSelect.vue";
import VueTextarea from "@/components/Inputs/VueTextarea.vue";
import Form from "@/utils/Form.js";

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
					id:'',
					contract_id: '',
					name: '',
					expirationDate: '',
					file: '',
					state:'',
					reason_rejection: '',
					apply_file: 'SI',
					apply_motive: ''
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
		}
	}
};
</script>
