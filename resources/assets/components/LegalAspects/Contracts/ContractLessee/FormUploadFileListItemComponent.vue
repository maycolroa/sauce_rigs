<template>
	<div class="col-md-12">
		<b-form-row>
			<div class="col-md-12" v-if="!viewOnly">
				<div class="float-right" style="padding-top: 10px;">
					<b-btn variant="primary" @click.prevent="addFile()"><span class="ion ion-md-add-circle"></span> Añadir archivo</b-btn>
				</div>
			</div>
		</b-form-row>
		<b-form-row style="padding-top: 15px;">
			<perfect-scrollbar :options="{ wheelPropagation: true }" class="mb-4" style="height: 500px; padding-right: 15px; width: 100%;">
				<template v-for="(file, index) in value">
					<b-card no-body class="mb-2 border-secondary" :key="file.key" style="width: 100%;" >
						<b-card-header class="bg-secondary">
							<b-row>
								<b-col cols="10" class="d-flex justify-content-between text-white"> Archivo #{{ index + 1 }}</b-col>
								<b-col cols="2">
									<div class="float-right">
										<b-button-group>
											<b-btn href="javascript:void(0)" v-b-toggle="'accordion'+ file.key +'-1'" variant="link">
												<span class="collapse-icon"></span>
											</b-btn>
											<b-btn @click.prevent="removeFile(index)"
												v-if="!viewOnly"
												size="sm" 
												variant="secondary icon-btn borderless"
												v-b-tooltip.top title="Eliminar Archivo">
												<span class="ion ion-md-close-circle"></span>
											</b-btn>
										</b-button-group>
									</div>
								</b-col>
							</b-row>
						</b-card-header>
						<b-collapse :id="`accordion${file.key}-1`" visible :accordion="`accordion-${prefixIndex}`">
							<b-card-body border-variant="primary" class="mb-3 box-shadow-none">
								<div class="rounded ui-bordered p-3 mb-3">
				
									<b-form-row>
										<vue-input :disabled="viewOnly" class="col-md-6" v-model="value[index].name" label="Nombre" type="text" name="name"  placeholder="Nombre" :error="form.errorsFor(`${prefixIndex}files.${index}.name`)" help-text="El nombre no debe contener caracteres especiales como '/', '.'"></vue-input>
										<vue-datepicker :disabled="viewOnly" class="col-md-6" v-model="value[index].expirationDate" label="Fecha de vencimiento" :full-month-name="true" placeholder="Seleccione la fecha de vencimiento"  name="expirationDate" :disabled-dates="disabledDates">
										</vue-datepicker>
									</b-form-row>

									<b-form-row>
										<vue-file-simple :disabled="viewOnly" :help-text="value[index].id ? `Para descargar el archivo actual, haga click <a href='/legalAspects/fileUpload/download/${value[index].id}' target='blank'>aqui</a> ` : 'El tamaño del archivo no debe ser mayor a 15MB.'" class="col-md-12" v-model="value[index].file" label="Archivo" name="file" placeholder="Seleccione un archivo" :error="form.errorsFor(`${prefixIndex}files.${index}.file`)" :maxFileSize="20"></vue-file-simple>
									</b-form-row>
									
								</div>
							</b-card-body>
						</b-collapse>
					</b-card>
				</template>
			</perfect-scrollbar>
		</b-form-row>
	</div>
</template>

<script>

import VueInput from "@/components/Inputs/VueInput.vue";
import VueFileSimple from "@/components/Inputs/VueFileSimple.vue";
import VueDatepicker from "@/components/Inputs/VueDatepicker.vue";
import PerfectScrollbar from '@/vendor/libs/perfect-scrollbar/PerfectScrollbar';
import Alerts from '@/utils/Alerts.js';

export default {
	components: {
        VueInput,
        VueFileSimple,
		VueDatepicker,
		PerfectScrollbar
	},
	props: {
		viewOnly: { type: Boolean, default: false },
		prefixIndex: { type: String, default: ''},
		form: { type: Object, required: true },
		value: {type: [Array], default:[]},
	},
	data() {
		return {
			loading: this.isEdit,
            disabledDates: {
                to: new Date()
            }
		};
	},
	methods: {
		addFile() {
            this.value.push({
				key: new Date().getTime(),
				name: '',
				expirationDate: '',
				file: ''
			});
        },
		removeFile(index) {
			if (this.value[index].id != undefined)
				this.$emit('removeFile', this.value[index]);
				
            this.value.splice(index, 1);
        },
	}
};
</script>
