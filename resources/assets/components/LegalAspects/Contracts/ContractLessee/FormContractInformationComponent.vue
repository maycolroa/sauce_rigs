<template>
    <b-row>
        <b-col>
            <b-form :action="url" @submit.prevent="submit" autocomplete="off">
				<b-card bg-variant="transparent" border-variant="primary" title="Datos empresariales" class="mb-3 box-shadow-none">
					<b-row>
						<b-col>
							<div><b>Tipo:</b> {{ form.type }}</div>
							<div><b>Nombre de la empresa:</b> {{ form.business_name }}</div>
							<div><b>Nit:</b> {{ form.nit }}</div>
						</b-col>
						<b-col>
							<div><b>Razón social:</b> {{ form.social_reason }}</div>
							<div v-if="form.type == 'Contratista'"><b>Clasificación:</b> {{ form.classification }}</div>
							<div><b>¿Trabajo de alto riesgo?:</b> {{ form.high_risk_work }}</div>
						</b-col>
					</b-row>
					<br><br>
					<center v-if="auth.hasRole['Contratista'] && form.existsOthersContract">
			            <b-btn variant="primary" size="md" @click="$refs.modalTransfer.show()" >Transferencia de estandares mínimos</b-btn>
			         </center>
				</b-card>

				<b-modal ref="modalTransfer" :hideFooter="true" id="modals-historial" class="modal-top" size="lg">
					<div slot="modal-title">
						<h4>Seleccione el contratista del cual desea transferir los valores</h4>
					</div>
					<center>
						<vue-advanced-select class="col-md-12" v-model="contract_select" :error="form.errorsFor('contract_select')" name="contract_select" label="Contratista" placeholder="Seleccione el contratista" :options="form.multiselect_contracts">
                        </vue-advanced-select>
                	</center>

					<div class="row float-right pt-12 pr-12y">						
                        <b-btn @click="$refs.modalConfirmTransfer.show()" variant="primary">Copiar</b-btn>&nbsp;&nbsp;
						<b-btn variant="primary" @click="$refs.modalTransfer.hide()">Cerrar</b-btn>
					</div>
				</b-modal>

				<b-modal ref="modalConfirmTransfer" :hideFooter="true" id="modals-historial2" class="modal-top" size="lg">
					<div slot="modal-title">
						<h4>Confirmación</h4>
					</div>
					<center>
						<p> De continuar con la transferencia, todos los estandares mínimos diligenciados se borraran y se remplazaran por los transferidos. ¿Desea continuar? </p>
                	</center>

					<div class="row float-right pt-12 pr-12y">						
                        <b-btn @click="listCheckCopy()" variant="primary">SI</b-btn>&nbsp;&nbsp;
						<b-btn variant="primary" @click="$refs.modalConfirmTransfer.hide()">NO</b-btn>
					</div>
				</b-modal>

				<b-card border-variant="primary" class="mb-3 box-shadow-none">
                    <b-form-row>
                        <vue-input class="col-md-6" v-model="form.address" label="Dirección" type="text" name="address" :error="form.errorsFor('address')" placeholder="Ej: Calle 123 #12-34"></vue-input>
                        <vue-input class="col-md-6" v-model="form.phone" label="Teléfono" type="number" name="phone" :error="form.errorsFor('phone')" placeholder="Teléfono"></vue-input>
                    </b-form-row>

					<b-form-row>
						<vue-input class="col-md-6" v-model="form.email_contract" label="Email Contratista" type="text" name="email_contract" :error="form.errorsFor('email_contract')" placeholder="Email Contratista"></vue-input>
                        <vue-input class="col-md-6" v-model="form.legal_representative_name" label="Nombre del representante legal" type="text" name="legal_representative_name" :error="form.errorsFor('legal_representative_name')" placeholder="Nombre del representante legal"></vue-input>
                    </b-form-row>

					<b-form-row>
                        <vue-input class="col-md-6" v-model="form.SG_SST_name" label="Nombre del responsable del SG-SST" type="text" name="SG_SST_name" :error="form.errorsFor('SG_SST_name')" placeholder="Nombre del responsable del SG-SST"></vue-input>
                        <vue-input class="col-md-6" v-model="form.environmental_management_name" label="Nombre del encargado de gestión ambiental" type="text" name="environmental_management_name" :error="form.errorsFor('environmental_management_name')" placeholder="Nombre del encargado de gestión ambiental"></vue-input>
                    </b-form-row>

                    <b-form-row>
                        <vue-input class="col-md-6" v-model="form.human_management_coordinator" label="Coordinador de gestión humana" type="text" name="human_management_coordinator" :error="form.errorsFor('human_management_coordinator')" placeholder="Coordinador de gestión humana"></vue-input>
                        <vue-input class="col-md-6" v-model="form.economic_activity_of_company" label="Actividad económica de la empresa" type="text" name="economic_activity_of_company" :error="form.errorsFor('economic_activity_of_company')" placeholder="Actividad económica de la empresa"></vue-input>
                    </b-form-row>

					<b-form-row>
						<vue-ajax-advanced-select-tag-unic class="col-md-6" v-model="form.arl" name="arl" :error="form.errorsFor('arl')" label="ARL" placeholder="Seleccione la ARL" :url="tagsCtArlDataUrl" :multiple="false" :allowEmpty="true" :taggable="true">
						</vue-ajax-advanced-select-tag-unic>
						<vue-input class="col-md-6" v-model="form.number_workers"  type="number" :error="form.errorsFor('number_workers')" name="number_workers" label="Número de trabajadores" placeholder="Número de trabajadores"></vue-input>
                    </b-form-row>

					<b-form-row>
                        <vue-advanced-select class="col-md-6" v-model="form.risk_class" :error="form.errorsFor('risk_class')" name="risk_class" label="Clase de riesgo" placeholder="Seleccione la clase riesgo" :options="kindsRisks">
                        </vue-advanced-select>
						<vue-input class="col-md-6" v-model="form.email_training_employee"  type="text" :error="form.errorsFor('email_training_employee')" name="email_training_employee" label="Correo para envio de capacitaciones de los empleados (Opcional)" placeholder="Correo para envio de capacitaciones de los empleados"></vue-input>
                    </b-form-row>

					<b-form-row>
						<vue-ajax-advanced-select class="col-md-6" v-model="form.height_training_centers" name="height_training_centers" :error="form.errorsFor('height_training_centers')" label="Centro de entrenamiento de alturas" placeholder="Seleccione el centro" :url="tagsCtHeightTrainingCenterDataUrl" :multiple="true" :allowEmpty="true" :taggable="true">
						</vue-ajax-advanced-select>
						<vue-ajax-advanced-select-tag-unic class="col-md-6" v-model="form.social_security_payment_operator" name="social_security_payment_operator" :error="form.errorsFor('social_security_payment_operator')" label="Operador de pago seguridad social" placeholder="Seleccione el operador" :url="tagsCtSocialSecurityDataUrl" :multiple="false" :allowEmpty="true" :taggable="true">
						</vue-ajax-advanced-select-tag-unic>
                    </b-form-row>

					<b-form-row>
						<vue-ajax-advanced-select class="col-md-6" v-model="form.ips" name="ips" :error="form.errorsFor('ips')" label="IPS para examenes medicos" placeholder="Seleccione la IPS" :url="tagsCtIpsDataUrl" :multiple="true" :allowEmpty="true" :taggable="true">
						</vue-ajax-advanced-select>
                    </b-form-row>

            	</b-card>

            	<b-card border-variant="primary" title="Documentos solicitados">
				    <b-card bg-variant="transparent" border-variant="dark" :title="document.name" class="mb-3 box-shadow-none" v-for="(document, index) in form.documents" :key="document.key">
                		<b-row>
                 			<b-col>
                    			<div class="col-md-12">
									<b-form-row>
										<vue-radio :checked="document.apply_file" class="col-md-12" v-model="document.apply_file" :options="siNo" :name="`apply_file${document.id}`" :error="form.errorsFor(`documents.${index}.apply_file`)" label="¿Aplica el documento para este empleado?">
										</vue-radio>
									</b-form-row>
                    				<b-form-row>
								        <div class="col-md-12" v-if="document.apply_file == 'SI'">
				                          <div class="float-right" style="padding-top: 10px;">
				                            <b-btn variant="primary" @click.prevent="addFile(document)"><span class="ion ion-md-add-circle"></span> Añadir archivo</b-btn>
				                          </div>
				                        </div>
			                    	</b-form-row>
			                        <b-form-row style="padding-top: 15px;" v-if="document.apply_file == 'SI'">
				                        <template v-for="(file, indexFile) in document.files">
				                          <b-card no-body class="mb-2 border-secondary" :key="file.key" style="width: 100%;" >
				                            <b-card-header class="bg-secondary">
				                              <b-row>
				                                <b-col cols="10" class="d-flex justify-content-between"> Archivo #{{ indexFile + 1 }}</b-col>
				                                <b-col cols="2">
				                                  <div class="float-right">
				                                    <b-button-group>
				                                      <b-btn href="javascript:void(0)" v-b-toggle="'accordion'+ file.key +'-1'" variant="link">
				                                        <span class="collapse-icon"></span>
				                                      </b-btn>
				                                      <b-btn @click.prevent="removeFile(document, indexFile)"
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
				                            <b-collapse :id="`accordion${file.key}-1`" visible :accordion="`accordion-documents.${index}`">
				                              <b-card-body border-variant="primary" class="mb-3 box-shadow-none">
				                                <div class="rounded ui-bordered p-3 mb-3">
				                        
				                                  <b-form-row>
				                                    <vue-input class="col-md-6" v-model="file.name" label="Nombre" type="text" name="name"  placeholder="Nombre" :error="form.errorsFor(`documents.${index}.files.${indexFile}.name`)"/>
				                                    <vue-datepicker class="col-md-6" v-model="file.expirationDate" label="Fecha de vencimiento" :full-month-name="true" placeholder="Seleccione la fecha de vencimiento"  name="expirationDate" :disabled-dates="disabledDates"/>
				                                  </b-form-row>

				                                  <b-form-row>
				                                    <vue-file-simple :help-text="file.id ? `Para descargar el archivo actual, haga click <a href='/legalAspects/fileUpload/download/${file.id}' target='blank'>aqui</a> ` : 'El tamaño del archivo no debe ser mayor a 15MB.'" class="col-md-12" :maxFileSize="20" v-model="file.file" label="Archivo" name="file" placeholder="Seleccione un archivo" :error="form.errorsFor(`documents.${index}.files.${indexFile}.file`)"/>
				                                  </b-form-row>

				                                </div>
				                              </b-card-body>
				                            </b-collapse>
				                          </b-card>
				                        </template>
				                    </b-form-row>
									<b-form-row v-if="document.apply_file == 'NO'">
						            	<vue-textarea class="col-md-12" v-model="document.apply_motive" label="Motivo por el cual no aplica" name="apply_motive" :error="form.errorsFor('apply_motive')" placeholder="Motivo por el cual no aplica" :disabled="viewOnly"></vue-textarea>
                      				</b-form-row>
				    			</div>
				    		</b-col>
                		</b-row>
            		</b-card>
            	</b-card>

				<div class="row float-right pt-10 pr-10" style="padding-top: 20px">
                    <template>
                        <b-btn variant="default" :to="cancelUrl" :disabled="loading">Atras</b-btn>&nbsp;&nbsp;
                        <b-btn type="submit" :disabled="loading" variant="primary">Guardar</b-btn>
                    </template>
                </div>
            </b-form>
        </b-col>
    </b-row>
</template>

<script>

import VueAdvancedSelect from "@/components/Inputs/VueAdvancedSelect.vue";
import VueInput from "@/components/Inputs/VueInput.vue";
import VueDatepicker from "@/components/Inputs/VueDatepicker.vue";
import VueFileSimple from "@/components/Inputs/VueFileSimple.vue";
import VueAjaxAdvancedSelectTagUnic from "@/components/Inputs/VueAjaxAdvancedSelectTagUnic.vue";
import VueAjaxAdvancedSelect from "@/components/Inputs/VueAjaxAdvancedSelect.vue";
import Form from "@/utils/Form.js";
import Alerts from '@/utils/Alerts.js';
import VueRadio from "@/components/Inputs/VueRadio.vue";
import VueTextarea from "@/components/Inputs/VueTextarea.vue";

export default {
	components: {
		VueAdvancedSelect,
		VueInput,
		VueDatepicker,
		VueFileSimple,
		VueAjaxAdvancedSelectTagUnic,
		VueRadio,
		VueTextarea,
		VueAjaxAdvancedSelect
	},
	props: {
		url: { type: String },
		method: { type: String },
		cancelUrl: { type: [String, Object], required: true },
		kindsRisks: {
			type: Array,
			default: function() {
				return [];
			}
		},
		contract: {
			default() {
				return {
					address: '',
					phone: '',
					legal_representative_name: '',
					SG_SST_name: '',
					environmental_management_name: '',
					human_management_coordinator: '',
					economic_activity_of_company: '',
					arl: '',
					number_workers: '',
					risk_class: '',
					email_training_employee: '',
					multiselect_contracts: [],
					existsOthersContract: false,
					documents: []
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
			loading: false,
			form: Form.makeFrom(this.contract, this.method),
			contract_select: '',
			disabledDates: {
		        to: new Date()
		    },
			tagsCtHeightTrainingCenterDataUrl: '/selects/tagsCtHeightTrainingCenter',
			tagsCtSocialSecurityDataUrl: '/selects/tagsCtSocialSecurity',
			tagsCtIpsDataUrl: '/selects/tagsCtIps',
			tagsCtArlDataUrl: '/selects/tagsCtArl',
			siNo: [
				{text: 'SI', value: 'SI'},
				{text: 'NO', value: 'NO'}
			],
		};
	},
	methods: {
		submit(e) {
		this.loading = true;

		this.form.clearFilesBinary();
        _.forIn(this.form.documents, (documento, keyDocument) => {
          _.forIn(documento.files, (file, keyFile) => {
            if (file.file)
              this.form.addFileBinary(`${keyDocument}_${keyFile}`, file.file);
          });
        });

		this.form
			.submit(e.target.action)
			.then(response => {
				this.loading = false;
				this.$router.push({ name: "legalaspects-contracts" });
			})
			.catch(error => {
				this.loading = false;
			});
		},
		listCheckCopy() {

			if (!this.contract_select)
			{
				Alerts.error('Error', 'Debe seleccionar un contratista');
				return;
			}

	      axios.post('/legalAspects/contracts/listCheckCopy', {contract_selected: this.contract_select})
	        .then(response => {
	          Alerts.warning('Información', 'Estimado usuario, se le notificara a su correo electronico cuando finalice el proceso.');
	          this.$refs.modalConfirmTransfer.hide()
	        }).catch(error => {
	          Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
	        });
    	},
    	addFile(documento) {
	      documento.files.push({
	        key: new Date().getTime(),
	        name: '',
	        expirationDate: '',
	        file: ''
	      });
	    },
	    removeFile(documento, index) {
	      if (documento.files[index].id != undefined)
	        this.form.delete.files.push(documento.files[index].id)
	        
	      documento.files.splice(index, 1);
	    }
	}
};
</script>
