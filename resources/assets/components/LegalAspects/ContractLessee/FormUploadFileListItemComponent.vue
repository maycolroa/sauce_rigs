<template>
    <b-row>
        <b-col>
            <div v-for="(file, index) in files">
				<b-card border-variant="primary" class="mb-3 box-shadow-none">
                <b-form-row>
                  <vue-input :disabled="viewOnly" class="col-md-6" v-model="files[index].name" label="Nombre" type="text" name="name"  placeholder="Nombre"></vue-input>
                  <vue-datepicker :disabled="viewOnly" class="col-md-6" v-model="files[index].expirationDate" label="Fecha de vencimiento" :full-month-name="true" placeholder="Seleccione la fecha de vencimiento"  name="expirationDate" :disabled-dates="disabledDates">
                  </vue-datepicker>
                </b-form-row>

                <b-form-row>
                        <vue-file-simple :help-text="`Para descargar el archivo actual, haga click aqui`" v-if="!viewOnly" :disabled="viewOnly" class="col-md-12" v-model="files[0].file" label="Archivo" name="file" placeholder="Seleccione un archivo"></vue-file-simple>
                </b-form-row>
            	</b-card>
				      <!-- <div class="row float-right pt-10 pr-10">
                    <template>
                        <!-- <b-btn variant="default" :to="cancelUrl" :disabled="loading">{{ viewOnly ? "Atras" : "Cancelar"}}</b-btn>&nbsp;&nbsp; 
                        <b-btn type="button" :disabled="loading" variant="primary" v-if="!viewOnly" @click.prevent="addFile()">Finalizar</b-btn>
                    </template>
                </div> -->
            </div>
        </b-col>
    </b-row>
</template>

<script>

import VueInput from "@/components/Inputs/VueInput.vue";
import VueFileSimple from "@/components/Inputs/VueFileSimple.vue";
import VueDatepicker from "@/components/Inputs/VueDatepicker.vue";

export default {
	components: {
        VueInput,
        VueFileSimple,
        VueDatepicker
	},
	props: {
		isEdit: { type: Boolean, default: false },
		viewOnly: { type: Boolean, default: false },
		files: { 
			type: Array,
			default: function() {
                return [];
            }
		},
		// fileUpload: {
			// default() {
				// return {
                    // id:'',
					// name: '',
					// expirationDate: '',
					// file: ''
				// };
			// }
		// }
	},
	watch: {
		files()
        {
            this.$emit("input", this.files);
		}
	},
	created() {
		this.addFile();
	},
	data() {
		return {
			loading: this.isEdit,
            // form: Form.makeFrom(this.fileUpload, this.method),
            
            disabledDates: {
                to: new Date()
            }
		};
	},
	methods: {
		// addFile(e) {
		// 	console.log("addfile");
			// this.loading = true;
			// this.form
			// .submit(e.target.action)
			// .then(response => {
			// 	this.loading = false;
			// 	this.$router.push({ name: "legalaspects-upload-files" });
			// })
			// .catch(error => {
			// 	this.loading = false;
			// });
		// }
		addFile() {
            this.files.push({
				name: '',
				expirationDate: '',
				file: ''
			});
			console.log("adjunto un push");
        },
	}
};
</script>
