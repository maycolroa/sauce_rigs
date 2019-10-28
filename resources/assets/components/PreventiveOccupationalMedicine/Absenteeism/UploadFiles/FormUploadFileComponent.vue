<template>
	<b-form :action="url" @submit.prevent="submit" autocomplete="off">
		<b-form-row>
			<vue-input :disabled="viewOnly" class="col-md-6" v-model="form.name" label="Nombre" type="text" name="name" :error="form.errorsFor('name')" placeholder="Nombre"></vue-input>
			<vue-ajax-advanced-select :disabled="viewOnly" class="col-md-6" v-model="form.talend_id" :error="form.errorsFor('talend_id')" :selected-object="form.multiselect_talend" name="talend_id" label="Tipo" placeholder="Seleccione el tipo de archivo" :url="talendsDataUrl"></vue-ajax-advanced-select>
		</b-form-row>

		<b-form-row>
			<vue-file-simple :help-text="form.old_file ? `Para descargar el archivo actual, haga click <a href='/biologicalmonitoring/absenteeism/fileUpload/download/${form.id}' target='blank'>aqui</a> `: null" :disabled="viewOnly" class="col-md-12" v-model="form.file" label="Archivo (*.xls | *.xlsx | *.zip)" name="file" :error="form.errorsFor('file')" placeholder="Seleccione un archivo"></vue-file-simple>
		</b-form-row>

		<div class="row float-right pt-10 pr-10">
			<template>
				<b-btn variant="default" :to="cancelUrl" :disabled="loading">{{ viewOnly ? "Atras" : "Cancelar"}}</b-btn>&nbsp;&nbsp;
				<b-btn type="submit" :disabled="loading" variant="primary" v-if="!viewOnly">Finalizar</b-btn>
			</template>
		</div>
	</b-form>
</template>

<script>
import VueAjaxAdvancedSelect from "@/components/Inputs/VueAjaxAdvancedSelect.vue";
import VueInput from "@/components/Inputs/VueInput.vue";
import VueFileSimple from "@/components/Inputs/VueFileSimple.vue";
import Form from "@/utils/Form.js";

export default {
	components: {
		VueInput,
		VueFileSimple,
		VueAjaxAdvancedSelect
	},
	props: {
		url: { type: String },
		method: { type: String },
		cancelUrl: { type: [String, Object], required: true },
		isEdit: { type: Boolean, default: false },
		viewOnly: { type: Boolean, default: false },
		talendsDataUrl: { type: String, default: "" },
		fileUpload: {
			default() {
				return {
					name: '',
					file: '',
					talend_id: ''
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
			loading: false,
			form: Form.makeFrom(this.fileUpload, this.method),
		};
	},
	methods: {
		submit(e) {
		this.loading = true;
		this.form
			.submit(e.target.action)
			.then(response => {
				this.loading = false;
				this.$router.push({ name: "absenteeism-upload-files" });
			})
			.catch(error => {
				this.loading = false;
			});
		}
	}
};
</script>
