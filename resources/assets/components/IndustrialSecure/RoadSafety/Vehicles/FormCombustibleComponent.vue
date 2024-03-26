<template>
    <b-form :action="url" @submit.prevent="submit" autocomplete="off">
        <b-form-row>
            <vue-input :disabled="true" class="col-md-12" v-model="plate_vehicle" label="Placa del vehiculo" type="text" name="plate_vehicle" :error="form.errorsFor('plate_vehicle')" placeholder="Placa del vehiculo"></vue-input> 
        </b-form-row>
        <b-form-row>
            <vue-datepicker :disabled="viewOnly" class="col-md-6" v-model="form.date" label="Fecha de tanqueo" :full-month-name="true" placeholder="Fecha de tanqueo" :error="form.errorsFor('date')" name="date" :disabled-dates="disabledExpirationDateFrom()" >
                      </vue-datepicker>
            <vue-input :disabled="viewOnly" class="col-md-6" v-model="form.cylinder_capacity" label="Cilindraje" type="number" name="cylinder_capacity" :error="form.errorsFor('cylinder_capacity')" placeholder="Cilindraje"></vue-input>   
        </b-form-row>
        <b-form-row>
            <vue-ajax-advanced-select :disabled="viewOnly" class="col-md-12" v-model="form.driver_id" :error="form.errorsFor('driver_id')" :selected-object="form.multiselect_driver" name="driver_id" label="Conductor" placeholder="Seleccione una opción" :url="driverDataUrl">
          </vue-ajax-advanced-select>
            <vue-input :disabled="viewOnly" class="col-md-6" v-model="form.km" label="Kilometraje a la fecha" type="number" name="km" :error="form.errorsFor('km')" placeholder="Kilometraje a la fecha"></vue-input>
        </b-form-row>            
        <b-form-row>
            <vue-input :disabled="viewOnly" class="col-md-6" v-model="form.quantity_galons" label="Cantidad de galones tanqueados" type="number" name="quantity_galons" :error="form.errorsFor('quantity_galons')" placeholder="Cantidad de galones tanqueados"></vue-input>
            <vue-input :disabled="viewOnly" class="col-md-6" v-model="form.price_galon" label="Valor por galon" type="number" name="price_galon" :error="form.errorsFor('price_galon')" placeholder="Valor por galon"></vue-input>
        </b-form-row>
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
import VueAjaxAdvancedSelect from "@/components/Inputs/VueAjaxAdvancedSelect.vue";

export default {
    props: {
        url: { type: String },
        method: { type: String },
        cancelUrl: { type: [String, Object], required: true },
        isEdit: { type: Boolean, default: false },
        viewOnly: { type: Boolean, default: false },
        vehicle: { type: [Number, String], default: ''},
        combustible: {
            type: [Array, Object],
            default() {
                return {
                    vehicle_id: '',
                    driver_id: '',
                    date: '',
                    cylinder_capacity: '',
                    km: '',
                    quantity_galons: '',
                    price_galon: ''
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
        VueTextarea,
        VueAjaxAdvancedSelect
    },
    computed: {
    },
    watch: {
        combustible() {
            this.loading = false;
            this.form = Form.makeFrom(this.combustible, this.method);
        }
    },
    data () {
        return {
            loading: this.isEdit,
            form: Form.makeFrom(this.combustible, this.method),
            driverDataUrl: "/selects/drivers",
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
            axios.get(`/industrialSecurity/roadsafety/vehicles/${this.vehicle ? this.vehicle : this.form.vehicle_id}`)
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