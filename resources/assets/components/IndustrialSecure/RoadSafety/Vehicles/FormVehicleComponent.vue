<template>

  <b-form :action="url" @submit.prevent="submit" autocomplete="off">
    <b-card border-variant="primary" title="General" class="mb-3 box-shadow-none">
        <b-card-body>
          <b-form-row>
            <vue-input :disabled="viewOnly" class="col-md-6" v-model="form.plate" label="Placa" type="text" name="plate" :error="form.errorsFor('plate')" placeholder="Placa"></vue-input>
            <vue-ajax-advanced-select-tag-unic :disabled="viewOnly" class="col-md-6" v-model="form.name_propietary" name="name_propietary" :error="form.errorsFor('name_propietary')" label="Nombre del propietario" placeholder="Seleccione el propietario" :url="tagsNamePropietaryDataUrl" :multiple="false" :allowEmpty="true" :taggable="true">
            </vue-ajax-advanced-select-tag-unic>
          </b-form-row>

          <b-form-row>
            <vue-input :disabled="viewOnly" class="col-md-6" v-model="form.registration_number" label="Número de matricula" type="text" name="registration_number" :error="form.errorsFor('registration_number')" placeholder="Número de matricula"></vue-input>
            <vue-datepicker :disabled="viewOnly" class="col-md-6" v-model="form.registration_number_date" label="Fecha de expedición de matrícula" :full-month-name="true" placeholder="Fecha de expedición de matrícula" :error="form.errorsFor('registration_number_date')" name="registration_number_date">
                      </vue-datepicker>
          </b-form-row>

          <b-form-row>
              <location-level-component
                :is-edit="isEdit"
                :view-only="viewOnly"
                v-model="form.locations"
                :location-level="vehicles.locations"
                :form="form"
                application="industrialSecure"
                module="roadSafety"
                @configLocation="setConfigLocation"/>
          </b-form-row>

          <b-form-row>            
            <vue-ajax-advanced-select :disabled="viewOnly" class="col-md-6" v-model="form.type_vehicle" name="type_vehicle" :error="form.errorsFor('type_vehicle')" label="Tipo de vehículo" placeholder="Seleccione el tipo" :url="typeVehicleDataUrl" :multiple="false" :allowEmpty="true" :selectedObject="form.multiselect_type" :btnLabelPopover="createHelp()">
            </vue-ajax-advanced-select>         
            <vue-advanced-select :disabled="viewOnly || !form.type_vehicle" class="col-md-6" v-model="form.year_vehicle" :multiple="false" :options="years" :hide-selected="false" name="year_vehicle" :error="form.errorsFor('year_vehicle')" label="Año" placeholder="Seleccione el año" :searchable="true">
              </vue-advanced-select>
            <vue-input :disabled="viewOnly" class="col-md-6" v-model="form.code_vehicle" label="Código del vehiculo" type="text" name="code_vehicle" :error="form.errorsFor('code_vehicle')" placeholder="Código del vehiculo"></vue-input>
          </b-form-row>    
        </b-card-body>
    </b-card>

    <b-card border-variant="primary" title="Información de acuerdo a la matricula del vehículo" class="mb-3 box-shadow-none">
        <b-card-body>
          <b-form-row>
            <vue-ajax-advanced-select-tag-unic :disabled="viewOnly" class="col-md-6" v-model="form.mark" name="mark" :error="form.errorsFor('mark')" label="Marca" placeholder="Seleccione la marca" :url="tagsMarkDataUrl" :multiple="false" :allowEmpty="true" :taggable="true">
            </vue-ajax-advanced-select-tag-unic>
            <vue-ajax-advanced-select-tag-unic :disabled="viewOnly" class="col-md-6" v-model="form.line" name="line" :error="form.errorsFor('line')" label="Linea" placeholder="Seleccione la linea" :url="tagsLineDataUrl" :multiple="false" :allowEmpty="true" :taggable="true">
            </vue-ajax-advanced-select-tag-unic>
          </b-form-row>

          <b-form-row>
            <vue-ajax-advanced-select-tag-unic :disabled="viewOnly" class="col-md-6" v-model="form.model" name="model" :error="form.errorsFor('model')" label="Modelo" placeholder="Seleccione la modelo" :url="tagsModelDataUrl" :multiple="false" :allowEmpty="true" :taggable="true">
            </vue-ajax-advanced-select-tag-unic>
            <vue-input :disabled="viewOnly" class="col-md-6" v-model="form.cylinder_capacity" label="Cilindraje" type="number" name="cylinder_capacity" :error="form.errorsFor('cylinder_capacity')" placeholder="Cilindraje"></vue-input>       
          </b-form-row>

          <b-form-row>     
            <vue-ajax-advanced-select-tag-unic :disabled="viewOnly" class="col-md-6" v-model="form.color" name="color" :error="form.errorsFor('color')" label="Color" placeholder="Seleccione el color" :url="tagsColorDataUrl" :multiple="false" :allowEmpty="true" :taggable="true">
            </vue-ajax-advanced-select-tag-unic>
            <vue-input :disabled="viewOnly" class="col-md-6" v-model="form.chassis_number" label="Número de chasis" type="text" name="chassis_number" :error="form.errorsFor('chassis_number')" placeholder="Número de chasis"></vue-input>
          </b-form-row>    

          <b-form-row>
            <vue-input :disabled="viewOnly" class="col-md-6" v-model="form.engine_number" label="Número de motor" type="text" name="engine_number" :error="form.errorsFor('engine_number')" placeholder="Número de motor"></vue-input>
            <vue-input :disabled="viewOnly" class="col-md-6" v-model="form.passenger_capacity" label="Capacidad de pasajeros" type="number" name="passenger_capacity" :error="form.errorsFor('passenger_capacity')" placeholder="Capacidad de pasajeros"></vue-input>
          </b-form-row>  
          <b-form-row>
            <vue-ajax-advanced-select-tag-unic :disabled="viewOnly" class="col-md-6" v-model="form.loading_capacity" name="loading_capacity" :error="form.errorsFor('loading_capacity')" label="Capacidad de carga" placeholder="Seleccione la capacidad" :url="tagsCapacityLoadingDataUrl" :multiple="false" :allowEmpty="true" :taggable="true">
            </vue-ajax-advanced-select-tag-unic>
            <vue-radio :disabled="viewOnly" :checked="form.state" class="col-md-6" v-model="form.state" :options="activeInactive" name="state" :error="form.errorsFor('state')" label="Estado del vehiculo"></vue-radio>
          </b-form-row>  
        </b-card-body>
    </b-card>

    <b-card v-if="form.type_vehicle && [1,2,3,4].includes(form.type_vehicle)" border-variant="primary" title="Información SOAT" class="mb-3 box-shadow-none">
        <b-card-body>
          <b-form-row>
            <vue-input :disabled="viewOnly" class="col-md-6" v-model="form.soat_number" label="Número del SOAT" type="number" name="soat_number" :error="form.errorsFor('soat_number')" placeholder="Número del SOAT"></vue-input>            
            <vue-input :disabled="viewOnly" class="col-md-6" v-model="form.insurance" label="Aseguradora" type="text" name="insurance" :error="form.errorsFor('insurance')" placeholder="Aseguradora"></vue-input>   
          </b-form-row>

          <b-form-row>
            <vue-datepicker :disabled="viewOnly" class="col-md-6" v-model="form.expedition_date_soat" label="Fecha de expedición" :full-month-name="true" placeholder="Fecha de expedición" :error="form.errorsFor('expedition_date_soat')" name="expedition_date_soat" :disabled-dates="disabledExpirationDateFrom()">
                      </vue-datepicker>
            <vue-datepicker :disabled="viewOnly" class="col-md-6" v-model="form.due_date_soat" label="Fecha de vencimiento" :full-month-name="true" placeholder="Fecha de vencimiento" :error="form.errorsFor('due_date_soat')" name="due_date_soat" :disabled-dates="disabledExpirationDateTo('expedition_date_soat')">
                      </vue-datepicker>
          </b-form-row>    

          <b-form-row>        
            <template v-if="isEdit || viewOnly">
						  <vue-file-simple :help-text="form.old_file_soat ? `Para descargar el archivo actual, haga click <a href='/industrialSecurity/roadsafety/vehicles/downloadSoat/${this.$route.params.id}' target='blank'>aqui</a> `: null" :disabled="viewOnly" class="col-md-6" v-model="form.file_soat" label="Evidencia" name="file_soat" :error="form.errorsFor('file_soat')" placeholder="Seleccione un archivo" :maxFileSize="20"></vue-file-simple>
            </template>    
						<vue-file-simple v-else :disabled="viewOnly" class="col-md-6" v-model="form.file_soat" label="Evidencia" name="file_soat" :error="form.errorsFor('file_soat')" placeholder="Seleccione un archivo" :maxFileSize="20"></vue-file-simple>
          </b-form-row>  

          <b-form-row v-if="isEdit && showChangeSoat"> 
              <vue-textarea :disabled="viewOnly" class="col-md-12" v-model="form.change_soat" label="Descripción del cambio realizado" :error="form.errorsFor('change_soat')"  name="description" placeholder="Descripción del cambio realizado" rows="2"></vue-textarea>
          </b-form-row>  
          <b-form-row v-if="viewOnly">
            <div class="col-md-12">
              <h6 class="font-weight-bold mb-1">
                Historial de cambios realizados
              </h6>
              <div class="col-md">
                <b-card no-body>
                  <b-card-body>
                      <vue-table
                          configName="industrialsecure-road-safety-history-soat"
                          :modelId="form.id ? form.id : -1"
                      ></vue-table>
                  </b-card-body>
              </b-card>
              </div>
            </div>
          </b-form-row>
        </b-card-body>
    </b-card>

    <b-card v-if="(form.type_vehicle && [1,2,3,4].includes(form.type_vehicle) && yearValid())" border-variant="primary" title="Información Tecno mecánica" class="mb-3 box-shadow-none">
        <b-card-body>
          <b-form-row>
            <vue-input :disabled="viewOnly" class="col-md-6" v-model="form.mechanical_tech_number" label="Número del Tecno mecánica" type="number" name="mechanical_tech_number" :error="form.errorsFor('mechanical_tech_number')" placeholder="Número del Tecno mecánica"></vue-input>            
            <vue-input :disabled="viewOnly" class="col-md-6" v-model="form.issuing_entity" label="Entidad que expide" type="text" name="issuing_entity" :error="form.errorsFor('issuing_entity')" placeholder="Entidad que expide"></vue-input>   
          </b-form-row>

          <b-form-row>
            <vue-datepicker :disabled="viewOnly" class="col-md-6" v-model="form.expedition_date_mechanical_tech" label="Fecha de expedición" :full-month-name="true" placeholder="Fecha de expedición" :error="form.errorsFor('expedition_date_mechanical_tech')" name="expedition_date_mechanical_tech" :disabled-dates="disabledExpirationDateFrom()">
                      </vue-datepicker>
            <vue-datepicker :disabled="viewOnly" class="col-md-6" v-model="form.due_date_mechanical_tech" label="Fecha de vencimiento" :full-month-name="true" placeholder="Fecha de vencimiento" :error="form.errorsFor('due_date_mechanical_tech')" name="due_date_mechanical_tech" :disabled-dates="disabledExpirationDateTo('expedition_date_mechanical_tech')">
                      </vue-datepicker>
          </b-form-row>    

          <b-form-row>        
            <template v-if="isEdit || viewOnly">
						  <vue-file-simple :help-text="form.old_file_mechanical_tech ? `Para descargar el archivo actual, haga click <a href='/industrialSecurity/roadsafety/vehicles/downloadMechanicalTech/${this.$route.params.id}' target='blank'>aqui</a> `: null" :disabled="viewOnly" class="col-md-6" v-model="form.file_mechanical_tech" label="Evidencia" name="file_mechanical_tech" :error="form.errorsFor('file_mechanical_tech')" placeholder="Seleccione un archivo" :maxFileSize="20"></vue-file-simple>
            </template>    
						<vue-file-simple v-else :disabled="viewOnly" class="col-md-6" v-model="form.file_mechanical_tech" label="Evidencia" name="file_mechanical_tech" :error="form.errorsFor('file_mechanical_tech')" placeholder="Seleccione un archivo" :maxFileSize="20"></vue-file-simple>
          </b-form-row>  
          <b-form-row v-if="isEdit && showChangeMecanical">   
              <vue-textarea :disabled="viewOnly" class="col-md-12" v-model="form.change_mechanical" label="Descripción del cambio realizado" :error="form.errorsFor('change_mechanical')"  name="description" placeholder="Descripción del cambio realizado" rows="2"></vue-textarea>
          </b-form-row>  
          <b-form-row v-if="viewOnly">
            <div class="col-md-12">
              <h6 class="font-weight-bold mb-1">
                Historial de cambios realizados
              </h6>
              <div class="col-md">
                <b-card no-body>
                  <b-card-body>
                      <vue-table
                          configName="industrialsecure-road-safety-history-mechanical"
                          :modelId="form.id ? form.id : -1"
                      ></vue-table>
                  </b-card-body>
              </b-card>
              </div>
            </div>
          </b-form-row>
        </b-card-body>
    </b-card>

    <b-card border-variant="primary" title="Póliza responsabilidad civil" class="mb-3 box-shadow-none">
        <b-card-body>
          <b-form-row>
            <vue-radio :disabled="viewOnly" :checked="form.policy_responsability" class="col-md-12" v-model="form.policy_responsability" :options="siNo" name="policy_responsability" :error="form.errorsFor('policy_responsability')" label="¿Tiene poliza de responsabilidad civil?"></vue-radio>
          </b-form-row>
          <b-form-row>
            <vue-input :disabled="viewOnly || form.policy_responsability == 'NO'" class="col-md-6" v-model="form.policy_number" label="Número de póliza" type="number" name="policy_number" :error="form.errorsFor('policy_number')" placeholder="Número de póliza"></vue-input>            
            <vue-input :disabled="viewOnly || form.policy_responsability == 'NO'" class="col-md-6" v-model="form.policy_entity" label="Entidad que expide" type="text" name="policy_entity" :error="form.errorsFor('policy_entity')" placeholder="Entidad que expide"></vue-input>   
          </b-form-row>

          <b-form-row>
            <vue-datepicker :disabled="viewOnly || form.policy_responsability == 'NO'" class="col-md-6" v-model="form.expedition_date_policy" label="Fecha de expedición" :full-month-name="true" placeholder="Fecha de expedición" :error="form.errorsFor('expedition_date_policy')" name="expedition_date_policy" :disabled-dates="disabledExpirationDateFrom()">
                      </vue-datepicker>
            <vue-datepicker :disabled="viewOnly || form.policy_responsability == 'NO'" class="col-md-6" v-model="form.due_date_policy" label="Fecha de vencimiento" :full-month-name="true" placeholder="Fecha de vencimiento" :error="form.errorsFor('due_date_policy')" name="due_date_policy" :disabled-dates="disabledExpirationDateTo('expedition_date_policy')">
                      </vue-datepicker>
          </b-form-row>    

          <b-form-row>        
            <template v-if="isEdit || viewOnly">
						  <vue-file-simple :help-text="form.old_file_policy ? `Para descargar el archivo actual, haga click <a href='/industrialSecurity/roadsafety/vehicles/downloadPolicy/${this.$route.params.id}' target='blank'>aqui</a> `: null" :disabled="viewOnly || form.policy_responsability == 'NO'" class="col-md-6" v-model="form.file_policy" label="Evidencia" name="file_policy" :error="form.errorsFor('file_policy')" placeholder="Seleccione un archivo" :maxFileSize="20"></vue-file-simple>
            </template>    
						<vue-file-simple v-else :disabled="viewOnly || form.policy_responsability == 'NO'" class="col-md-6" v-model="form.file_policy" label="Evidencia" name="file_policy" :error="form.errorsFor('file_policy')" placeholder="Seleccione un archivo" :maxFileSize="20"></vue-file-simple>
          </b-form-row>  
          <b-form-row v-if="isEdit && showChangeResponsability">      
              <vue-textarea :disabled="viewOnly" class="col-md-12" v-model="form.change_resposability" label="Descripción del cambio realizado" :error="form.errorsFor('change_resposability')"  name="description" placeholder="Descripción del cambio realizado" rows="2"></vue-textarea>
          </b-form-row>  
          <b-form-row v-if="viewOnly">
            <div class="col-md-12">
              <h6 class="font-weight-bold mb-1">
                Historial de cambios realizados
              </h6>
              <div class="col-md">
                <b-card no-body>
                  <b-card-body>
                      <vue-table
                          configName="industrialsecure-road-safety-history-responsability"
                          :modelId="form.id ? form.id : -1"
                      ></vue-table>
                  </b-card-body>
              </b-card>
              </div>
            </div>
          </b-form-row>
        </b-card-body>
    </b-card>

    <div class="row float-right pt-10 pr-10">
      <template>
        <b-btn variant="default" :to="cancelUrl" :disabled="loading">{{ viewOnly ? "Atras" : "Cancelar"}}</b-btn>&nbsp;&nbsp;
        <b-btn type="submit" :disabled="loading" variant="primary" v-if="!viewOnly">Finalizar</b-btn>
      </template>
    </div>
  </b-form>
</template>

<script>
import VueInput from "@/components/Inputs/VueInput.vue";
import VueAjaxAdvancedSelect from "@/components/Inputs/VueAjaxAdvancedSelect.vue";
import VueAdvancedSelect from "@/components/Inputs/VueAdvancedSelect.vue";
import VueDatepicker from "@/components/Inputs/VueDatepicker.vue";
import VueAjaxAdvancedSelectTagUnic from "@/components/Inputs/VueAjaxAdvancedSelectTagUnic.vue";
import LocationLevelComponent from '@/components/CustomInputs/LocationLevelComponent.vue';
import Form from "@/utils/Form.js";
import VueRadio from "@/components/Inputs/VueRadio.vue";
import VueFileSimple from "@/components/Inputs/VueFileSimple.vue";
import VueTextarea from "@/components/Inputs/VueTextarea.vue";

export default {
  components: {
    VueInput,
    VueAdvancedSelect,
    VueAjaxAdvancedSelect,
    VueAjaxAdvancedSelectTagUnic,
    VueDatepicker,
    LocationLevelComponent,
    VueRadio,
    VueFileSimple,
    VueTextarea
  },
  props: {
    url: { type: String },
    method: { type: String },
    cancelUrl: { type: [String, Object], required: true },
    isEdit: { type: Boolean, default: false },
    viewOnly: { type: Boolean, default: false },
    positionsDataUrl: { type: String, default: "" },    
    years: {
      type: Array,
      default: function() {
        return [];
      }
    },
    vehicles: {
      default() {
        return {
            plate: '',
            name_propietary: '',
            registration_number: '',
            registration_number_date: '',
            locations: {
              employee_regional_id: '',
              employee_headquarter_id: '',
              employee_area_id: '',
              employee_process_id: ''
            },
            type_vehicle: '',
            code_vehicle: '',
            mark: '',
            line: '',
            model:'',
            cylinder_capacity: '',
            color: '',
            chassis_number: '',
            engine_number: '',
            passenger_capacity: '',
            loading_capacity: '',
            state: '',
            soat_number: '',
            insurance: '',
            expedition_date_soat: '',
            due_date_soat: '',
            file_soat: '',
            mechanical_tech_number: '',
            issuing_entity: '',
            expedition_date_mechanical_tech: '',
            due_date_mechanical_tech: '',
            file_mechanical_tech: '',
            policy_responsability: 'NO',
            policy_number: '',
            policy_entity: '',
            expedition_date_policy: '',
            due_date_policy: '',
            file_policy: '',
            change_soat: '',
            change_mechanical: '',
            change_resposability: '',
            activeChange: false,
            activeTech: false,
            multiselect_type: null
        };
      }
    }
  },
  watch: {
    vehicles() { 
      this.loading = false;
      this.form = Form.makeFrom(this.vehicles, this.method);
    },
    'form.soat_number' () 
    {
      if (this.isEdit && this.activeChange)
      {
        if (!this.showChangeSoat)
          this.showChangeSoat = true;
      }

    },
    'form.insurance' () 
    {
      if (this.isEdit && this.activeChange)
      {
        if (!this.showChangeSoat)
          this.showChangeSoat = true;
      }

    },
    'form.expedition_date_soat' () 
    {
      if (this.isEdit && this.activeChange)
      {
        if (!this.showChangeSoat)
          this.showChangeSoat = true;
      }

    },
    'form.due_date_soat' () 
    {
      if (this.isEdit && this.activeChange)
      {
        if (!this.showChangeSoat)
          this.showChangeSoat = true;
      }

    },
    'form.file_soat' () 
    {
      if (this.isEdit && this.activeChange)
      {
        if (!this.showChangeSoat)
          this.showChangeSoat = true;
      }
    },
    'form.mechanical_tech_number' () 
    {
      if (this.isEdit && this.activeChange)
      {
        if (!this.showChangeMecanical)
          this.showChangeMecanical = true;
      }
    },
    'form.issuing_entity' () 
    {
      if (this.isEdit && this.activeChange)
      {
        if (!this.showChangeMecanical)
          this.showChangeMecanical = true;
      }
    },
    'form.expedition_date_mechanical_tech' () 
    {
      if (this.isEdit && this.activeChange)
      {
        if (!this.showChangeMecanical)
          this.showChangeMecanical = true;
      }
    },
    'form.due_date_mechanical_tech' () 
    {
      if (this.isEdit && this.activeChange)
      {
        if (!this.showChangeMecanical)
          this.showChangeMecanical = true;
      }
    },
    'form.file_mechanical_tech' () 
    {
      if (this.isEdit && this.activeChange)
      {
        if (!this.showChangeMecanical)
          this.showChangeMecanical = true;
      }
    },
    'form.policy_responsability' () 
    {
      if (this.isEdit && this.activeChange)
      {
        if (!this.showChangeResponsability)
          this.showChangeResponsability = true;
      }

    },
    'form.policy_number' () 
    {
      if (this.isEdit && this.activeChange)
      {
        if (!this.showChangeResponsability)
          this.showChangeResponsability = true;
      }
    },
    'form.policy_entity' () 
    {
      if (this.isEdit && this.activeChange)
      {
        if (!this.showChangeResponsability)
          this.showChangeResponsability = true;
      }
    },
    'form.expedition_date_policy' () 
    {
      if (this.isEdit && this.activeChange)
      {
        if (!this.showChangeResponsability)
          this.showChangeResponsability = true;
      }
    },
    'form.due_date_policy' () 
    {
      if (this.isEdit && this.activeChange)
      {
        if (!this.showChangeResponsability)
          this.showChangeResponsability = true;
      }
    },
    'form.file_policy' () 
    {
      if (this.isEdit && this.activeChange)
      {
        if (!this.showChangeResponsability)
          this.showChangeResponsability = true;
      }
    },
  },
  data() {
    return {
      loading: this.isEdit,
      form: Form.makeFrom(this.vehicles, this.method),
      activeInactive: [
        {text: 'Activo', value: 'Activo'},
        {text: 'Inactivo', value: 'Inactivo'}
      ],
      siNo: [
        {text: 'SI', value: 'SI'},
        {text: 'NO', value: 'NO'}
      ],
      tagsColorDataUrl: '/selects/tagsRsColor',
      tagsLineDataUrl: '/selects/tagsRsLine',
      tagsModelDataUrl: '/selects/tagsRsModel',
      tagsMarkDataUrl: '/selects/tagsRsMark',
      tagsCapacityLoadingDataUrl: '/selects/tagsRsLoadingCapacity',
      tagsNamePropietaryDataUrl: '/selects/tagsRsNamePropietary',
      typeVehicleDataUrl: '/selects/tagsRsTypeVehicle',
      configLocation: {},
      showChangeSoat: false,
      showChangeMecanical: false,
      showChangeResponsability: false
      
    };
  },
  methods: {
    addDocument() {
        this.form.documents.push({
            key: new Date().getTime(),
            name: ''
        })
    },
    removeDocument(index)
    {
      if (this.form.documents[index].id != undefined)
        this.form.delete.documents.push(this.form.documents[index].id)

      this.form.documents.splice(index, 1)
    },
    submit(e) 
    {
      this.loading = true;
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
    setConfigLocation(value)
    {
      this.configLocation = value
    },
    disabledExpirationDateFrom() 
    {

      let toDate = new Date()
      toDate = new Date(toDate.getFullYear(), toDate.getMonth(), toDate.getDate())

      return {
          from: toDate
      }
            
    },
    disabledExpirationDateTo(key) {

        let toDate = new Date(this.form[key])
        toDate = new Date(toDate.getFullYear(), toDate.getMonth(), toDate.getDate())

        return {
            to: toDate
        }
        
    },
    yearValid() {
      if (this.form.type_vehicle && [1,3,4].includes(this.form.type_vehicle))
      {
        let currentDate = new Date();
        let currentYear = currentDate.getFullYear();

        let diff = currentYear - this.form.year_vehicle;

        this.form.activeTech = diff > 4;

        return diff > 4;
      }
      if (this.form.type_vehicle && [2].includes(this.form.type_vehicle))
      {
        let currentDate = new Date();
        let currentYear = currentDate.getFullYear();

        let diff = currentYear - this.form.year_vehicle;

        this.form.activeTech = diff > 2;

        return diff > 2;
      }
    },
    createHelp() {
      if (this.form.type_vehicle)
      {
        if (this.form.type_vehicle == 1)
        {
          return {
              title: "Automóviles",
              icon: 'fas fa-info',
              content: "Descripción: Vehículos motorizados de cuatro ruedas diseñados principalmente para el transporte de pasajeros. \n Incluye: \n- Sedanes. \n - Hatchbacks \n - SUVs y Crossovers \n - Camionetas pickup ligeras \n - Vehículos deportivos \n - Minivans"
          }
        }
        else if (this.form.type_vehicle == 2)
        {
          return {
              title: "Motocicletas",
              icon: 'fas fa-info',
              content: "Descripción: Vehículos de dos ruedas impulsados por motor. \n Incluye: \n- Motocicletas deportivas. \n - Motocicletas de turismo \n - Motocicletas cruiser \n - Scooters \n - Motocicletas off-road/todoterreno \n - Motocicletas naked/estándar \n - Motocicletas adventure/touring"
          }
        }
        else if (this.form.type_vehicle == 3)
        {
          return {
              title: "Vehículos Pesados",
              icon: 'fas fa-info',
              content: "Descripción:  Maquinaria y vehículos de gran tamaño utilizados principalmente para construcción, minería y agricultura. \n Incluye: \n- Excavadoras. \n - Retroexcavadoras \n - Cargadores frontales \n - Bulldozers \n - Grúas móviles \n - Compactadoras \n -  Tractores agrícolas"
          }
        }
        else if (this.form.type_vehicle == 4)
        {
          return {
              title: "Camiones",
              icon: 'fas fa-info',
              content: "Descripción: Vehículos motorizados diseñados principalmente para el transporte de mercancías y cargas. \n Incluye: \n- Camiones ligeros (hasta 3.5 toneladas). \n - Camiones medianos (3.5-12 toneladas) \n - Camiones pesados (más de 12 toneladas) \n - Tractocamiones \n - Vehículos especializados (bomberos, basura, cisterna, frigoríficos)."
          }
        }
        else if (this.form.type_vehicle == 5)
        {
          return {
              title: "Montacargas",
              icon: 'fas fa-info',
              content: "Descripción: Vehículos industriales especializados para levantar y transportar materiales en almacenes, centros de distribución y zonas industriales. \n Incluye: \n- Montacargas contrapesados. \n - Montacargas eléctricos. \n - Montacargas de combustión interna. \n - Apiladores. \n - Transpaletas manuales y eléctricas. \n - Carretillas elevadoras de alcance. \n - Montacargas para terrenos irregulares. \n - Montacargas telescópicos."
          }
        }
        else if (this.form.type_vehicle == 6)
        {
          return {
              title: "Vehículos Eléctricos",
              icon: 'fas fa-info',
              content: "Descripción: Vehículos impulsados por uno o más motores eléctricos con baterías recargables. \n Incluye: \n- Automóviles eléctricos. \n - SUVs y crossovers eléctricos. \n - Motocicletas eléctricas. \n - Camiones eléctricos. \n - Autobuses eléctricos. \n - Vehículos comerciales ligeros eléctricos. \n - Scooters eléctricos."
          }
        }
      }
      else
      {
        return {
              title: "Sin Seleccionar",
              icon: 'fas fa-info',
              content: "Descripción: "
          }
      }
    }
  },
  mounted() {
    setTimeout(() => {
      this.activeChange = true;
      this.createHelp()
      this.yearValid();
    }, 3000);
  },
};
</script>
