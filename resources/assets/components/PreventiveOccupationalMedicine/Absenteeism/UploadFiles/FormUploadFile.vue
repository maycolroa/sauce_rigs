<template>
    <b-row>
        <b-col>
            <b-form :action="url" @submit.prevent="submit" autocomplete="off">
				<b-card border-variant="primary" class="mb-3 box-shadow-none">
                    
					<b-form-row v-if="!talend">
						<vue-input :disabled="viewOnly" class="col-md-6" v-model="form.name" label="Nombre" type="text" name="name" :error="form.errorsFor('name')" placeholder="Nombre"></vue-input>
						<vue-input v-show="viewOnly" :disabled="viewOnly" class="col-md-6" v-model="form.created_at" label="Fecha de Subida" type="text" name="created_at" :error="form.errorsFor('created_at')"></vue-input>
						<vue-input v-show="viewOnly" :disabled="viewOnly" class="col-md-6" v-model="form.user_name" label="Usuario" type="text" name="user_name" :error="form.errorsFor('user_name')"></vue-input>
						<vue-file-simple v-show="!viewOnly" :disabled="viewOnly" class="col-md-6" v-model="form.file" :maxFileSize=20000000 label="Archivo" name="file" :error="form.errorsFor('file')" placeholder="Seleccione un archivo"></vue-file-simple>
                    </b-form-row>
					
					<b-form-row v-else>
						<vue-file-simple accept=".zip" class="col-md-6" v-model="form.file" label="Archivo" name="file" :error="form.errorsFor('file')" placeholder="Seleccione un archivo zip con el talend"></vue-file-simple>
                    </b-form-row>
            	
				</b-card>

				<div class="row float-right pt-10 pr-10">
                    <template>
                
				        <b-btn v-show="viewOnly" variant="primary" :href="`/biologicalmonitoring/absenteeism/fileUpload/download/${this.$route.params.id}`" target="blank" v-b-tooltip.top title="Descargar Archivo"><i class="fas fa-cloud-download-alt"></i></b-btn>&nbsp;&nbsp;
				
						<b-btn variant="default" :to="cancelUrl" :disabled="loading">{{ viewOnly ? "Atras" : "Cancelar"}}</b-btn>&nbsp;&nbsp;
						<b-btn type="submit" :disabled="loading" variant="primary" v-show="!viewOnly">Finalizar</b-btn>
                    </template>
                </div>
            </b-form>
        </b-col>
    </b-row>
</template>

<script>

import VueInput from "@/components/Inputs/VueInput.vue";
import VueFileSimple from "@/components/Inputs/VueFileSimple.vue";
import Form from "@/utils/Form.js";

export default {
	components: {
		VueInput,
		VueFileSimple,
	},
	props: {
		url: { type: String },
		method: { type: String },
		cancelUrl: { type: [String, Object], required: true },
		viewOnly: { type: Boolean, default: false },
		talend: { type: Boolean, default: false },
		fileUpload: {
			default() {
				return {
					name: '',
					file: ''
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
