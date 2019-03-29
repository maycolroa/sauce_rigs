<template>
    <b-row>
        <b-col>
            <b-form :action="url" @submit.prevent="submit" autocomplete="off">
				      <b-card border-variant="primary" class="mb-3 box-shadow-none">
                <b-form-row>
                  <vue-input :disabled="viewOnly" class="col-md-6" v-model="form.name" label="Nombre" type="text" name="name" :error="form.errorsFor('name')" placeholder="Nombre"></vue-input>
                  <vue-datepicker :disabled="viewOnly" class="col-md-6" v-model="form.expirationDate" label="Fecha de vencimiento" :full-month-name="true" placeholder="Seleccione la fecha de vencimiento" :error="form.errorsFor('expirationDate')" name="expirationDate" :disabled-dates="disabledDates">
                  </vue-datepicker>
                </b-form-row>

                    <b-form-row>
                        <vue-file-simple :help-text="`Para descargar el archivo actual, haga click <a href='/legalAspects/fileUpload/download/${this.$route.params.id}' target='blank'>aqui</a> `" v-if="!viewOnly" :disabled="viewOnly" class="col-md-12" v-model="form.file" label="Archivo" name="file" :error="form.errorsFor('file')" placeholder="Seleccione un archivo"></vue-file-simple>
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
import Form from "@/utils/Form.js";

export default {
	components: {
    VueInput,
    VueFileSimple,
    VueDatepicker
	},
	props: {
		url: { type: String },
		method: { type: String },
		cancelUrl: { type: [String, Object], required: true },
		isEdit: { type: Boolean, default: false },
		viewOnly: { type: Boolean, default: false },
		fileUpload: {
			default() {
				return {
          id:'',
					name: '',
					expirationDate: '',
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
			loading: this.isEdit,
      form: Form.makeFrom(this.fileUpload, this.method),
      
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
