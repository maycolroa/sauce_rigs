<template>
    <b-form :action="url" @submit.prevent="submit" autocomplete="off">
        <b-form-row>
            <vue-input :disabled="true" class="col-md-12" v-model="plate_vehicle" label="Placa del vehiculo" type="text" name="plate_vehicle" :error="form.errorsFor('plate_vehicle')" placeholder="Placa del vehiculo"></vue-input> 
        </b-form-row>
        <b-form-row>
            <vue-datepicker :disabled="viewOnly" class="col-md-6" v-model="form.date" label="Fecha de mantenimiento" :full-month-name="true" placeholder="Fecha de mantenimiento" :error="form.errorsFor('date')" name="date" :disabled-dates="disabledExpirationDateFrom()" >
                      </vue-datepicker>
            <vue-radio :disabled="viewOnly" :checked="form.type" class="col-md-6" v-model="form.type" :options="typeOption" name="type" :error="form.errorsFor('type')" label="Tipo de mantenimiento"></vue-radio>
        </b-form-row>
        <b-form-row>
            <vue-input :disabled="viewOnly" class="col-md-6" v-model="form.km" label="KM del vehículo al momento del mantenimiento" type="number" name="km" :error="form.errorsFor('km')" placeholder="KM del vehículo al momento del mantenimiento"></vue-input> 
            <vue-textarea :disabled="viewOnly" class="col-md-6" v-model="form.description" label="Descripción" :error="form.errorsFor('description')"  name="description" placeholder="Descripción" rows="2"></vue-textarea>
        </b-form-row>            
        <b-form-row>
            <vue-input :disabled="viewOnly" class="col-md-6" v-model="form.responsible" label="Responsable del mantenimiento" type="text" name="responsible" :error="form.errorsFor('responsible')" placeholder="REsponsable del mantenimiento"></vue-input> 
            <vue-radio :disabled="viewOnly" :checked="form.apto" class="col-md-6" v-model="form.apto" :options="aptoOption" name="apto" :error="form.errorsFor('apto')" label="¿El vehículo es apto para retomar marcha?"></vue-radio>
        </b-form-row>
        <b-form-row>
            <vue-textarea v-if="form.apto == 'NO'" :disabled="viewOnly" class="col-md-6" v-model="form.reason" label="Observaciones" :error="form.errorsFor('reason')"  name="reason" placeholder="Observaciones" rows="2"></vue-textarea>
            <vue-datepicker :disabled="viewOnly" class="col-md-6" v-model="form.next_date" label="Fecha de próximo mantenimiento" :full-month-name="true" placeholder="Fecha de próximo mantenimiento" :error="form.errorsFor('next_date')" name="next_date" :disabled-dates="disabledExpirationDateTo()">
                      </vue-datepicker>
        </b-form-row>
        <b-card border-variant="primary" title="Evidencias" class="mb-3 box-shadow-none">
            <b-card-body>
                <template v-for="(evidence, index) in form.evidences">
                    <div :key="evidence.key">
                        <b-form-row>
                            <div class="col-md-12">
                                <div class="float-right">
                                    <b-btn variant="outline-primary icon-btn borderless" size="sm" v-b-tooltip.top title="Eliminar" @click.prevent="removeFile(index)"><span class="ion ion-md-close-circle" v-if="!viewOnly"></span></b-btn>
                                </div>
                            </div>
                        </b-form-row>
                        <vue-file-simple :disabled="viewOnly" :help-text="evidence.id ? `Para descargar el archivo actual, haga click <a href='/industrialSecurity/roadsafety/vehiclesMaintenance/download/${evidence.id}' target='blank'>aqui</a> ` : null" class="col-md-12" v-model="evidence.file" label="Archivo" name="file" placeholder="Seleccione un archivo" :error="form.errorsFor(`form.evidences.${index}.file`)" :maxFileSize="20"/>
                    </div>
                </template>
                <div class="col-md-12 text-center" style="padding-top: 15px;">
                    <center><b-btn variant="primary" @click.prevent="addFile">Agregar</b-btn></center>
                </div>
            </b-card-body>
        </b-card>

        <div class="row float-right pt-10 pr-10">
            <template>
                <b-btn variant="default" @click="$router.go(-1)" :disabled="loading">{{ viewOnly ? "Atras" : "Cancelar"}}</b-btn>&nbsp;&nbsp;
                <b-btn type="submit" :disabled="loading" variant="primary" v-if="!viewOnly">Finalizar</b-btn>
            </template>
        </div>
    </b-form>
</template>

<script>
import VueInput from "@/components/Inputs/VueInput.vue";
import VueDatepicker from "@/components/Inputs/VueDatepicker.vue";
import VueRadio from "@/components/Inputs/VueRadio.vue";
import VueFileSimple from "@/components/Inputs/VueFileSimple.vue";
import Form from "@/utils/Form.js";
import VueTextarea from "@/components/Inputs/VueTextarea.vue";

export default {
    props: {
        url: { type: String },
        method: { type: String },
        cancelUrl: { type: [String, Object], required: true },
        isEdit: { type: Boolean, default: false },
        viewOnly: { type: Boolean, default: false },
        vehicle: { type: [Number, String], default: ''},
        maintenance: {
            type: [Array, Object],
            default() {
                return {
                    vehicle_id: '',
                    date: '',
                    type: '',
                    km: '',
                    description: '',
                    evidences: [],
                    responsible: '',
                    apto: '',
                    reason: '',
                    next_date: '',
                    delete: {
                        files: []
                    }
                };
            }
        },
    },
    components: {
        VueInput,
        VueDatepicker,
        VueRadio,
        VueFileSimple,
        Form,
        VueTextarea
    },
    computed: {
    },
    watch: {
        maintenance() {
            this.loading = false;
            this.form = Form.makeFrom(this.maintenance, this.method);
        }
    },
    data () {
        return {
            loading: this.isEdit,
            form: Form.makeFrom(this.maintenance, this.method),
            typeOption: [
                {text: 'Correctivo', value: 'Correctivo'},
                {text: 'Preventivo', value: 'Preventivo'}
            ],
            aptoOption: [
                {text: 'SI', value: 'SI'},
                {text: 'NO', value: 'NO'}
            ],
            plate_vehicle: ''
        };
    },
    methods: {
        submit(e) {
            this.loading = true;

            if (!this.isEdit)
            {
                this.form.vehicle_id = this.vehicle;
            }

            this.form.clearFilesBinary();
                _.forIn(this.form.evidences, (file, keyFile) => {
                if (file.file)
                    this.form.addFileBinary(`${keyFile}`, file.file);
                });

            this.form
                .submit(e.target.action)
                .then(response => {
                    this.loading = false;
                    this.$router.push({ name: "industrialsecure-roadsafety-vehicles" });
                })
                .catch(error => {
                    this.loading = false;
                });
        },
        addFile() {
           this.form.evidences.push({
                key: new Date().getTime() + Math.round(Math.random() * 10000),
                file: ''
            });
        },
        removeFile(index) {
            if (this.form.evidences[index].id != undefined)
                this.form.delete.files.push(this.form.evidences[index].id)
                
           this.form.evidences.splice(index, 1);
        },
        disabledExpirationDateFrom() {

            let toDate = new Date()
            toDate = new Date(toDate.getFullYear(), toDate.getMonth(), toDate.getDate())

            return {
                from: toDate
            }
            
        },
        disabledExpirationDateTo() {

            let toDate = new Date()
            toDate = new Date(toDate.getFullYear(), toDate.getMonth(), toDate.getDate())

            return {
                to: toDate
            }
            
        },
        getInfoVehicle() {
            console.log(this.vehicle)
            axios.get(`/industrialSecurity/roadsafety/vehicles/${this.vehicle}`)
            .then(response => {
                this.plate_vehicle = response.data.data.plate;
            })
            .catch(error => {
                Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
                this.$router.go(-1);
            });
        }
    },
    created() {
        setTimeout(() => {
          this.getInfoVehicle()
      }, 5000)
    }
}
</script>