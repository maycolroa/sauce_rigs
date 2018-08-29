<template>

    <b-form :action="url" @submit.prevent="submit">
        <b-form-row>
            <vue-datepicker class="col-md-6"
                v-model="form.date"
                label="Fecha"
                :full-month-name="true"
                placeholder="Seleccione la fecha"
                :error="form.errorsFor('date')"
                name="date"
                :disabled-dates="disabledDates"></vue-datepicker>

                <vue-ajax-advanced-select class="col-md-6" v-model="form.employee_id"
                    :error="form.errorsFor('employee_id')"
                    name="employee_id"
                    label="Empleado"
                    placeholder="Seleccione el empleado"
                    :url="employeesDataUrl"
                    >
                </vue-ajax-advanced-select>
        </b-form-row>
        <vue-textarea v-model="form.previews_events"
            label="Eventos previos"
            :error="form.errorsFor('previews_events')"
            name="previews_events"
            placeholder="Eventos previos"
        ></vue-textarea>


        <b-form-row>
            <vue-input  class="col-md-6"
                v-model="form.type"
                label="Tipo"
                type="text"
                name="type"
                 :error="form.errorsFor('type')"
                placeholder="Tipo"
            ></vue-input>
             <vue-input  class="col-md-6"
                v-model="form.work_zone_noise"
                label="Ruido de la zona de trabajo"
                type="text"
                name="work_zone_noise"
                 :error="form.errorsFor('work_zone_noise')"
                placeholder="Ruido de la zona de trabajo"
            ></vue-input>
        </b-form-row>
        <b-form-row>
             <vue-input  class="col-md-6"
                v-model="form.exposition_level"
                label="Nivel de exposicion"
                type="text"
                name="exposition_level"
                 :error="form.errorsFor('exposition_level')"
                placeholder="Nivel de exposicion"
            ></vue-input>
             <vue-input  class="col-md-6"
                v-model="form.epp"
                label="EPP"
                type="text"
                name="epp"
                 :error="form.errorsFor('epp')"
                placeholder="EPP"
            ></vue-input>
        </b-form-row>
        <b-form-row>
            <vue-input class="col-md-4"
                v-model="form.left_500"
                label="Izquierda 500"
                type="number"
                name="left_500"
                 :error="form.errorsFor('left_500')"
                placeholder="Izquierda 500"
            ></vue-input>
            <vue-input class="col-md-4"
                v-model="form.left_1000"
                label="Izquierda 1000"
                type="number"
                name="left_1000"
                 :error="form.errorsFor('left_1000')"
                placeholder="Izquierda 1000"
            ></vue-input>
            <vue-input class="col-md-4"
                v-model="form.left_2000"
                label="Izquierda 2000"
                type="number"
                name="left_2000"
                 :error="form.errorsFor('left_2000')"
                placeholder="Izquierda 2000"
            ></vue-input>
        </b-form-row>
        <b-form-row>
            <vue-input class="col-md-4"
                v-model="form.left_3000"
                label="Izquierda 3000"
                type="number"
                name="left_3000"
                 :error="form.errorsFor('left_3000')"
                placeholder="Izquierda 3000"
            ></vue-input>
            <vue-input class="col-md-4"
                v-model="form.left_4000"
                label="Izquierda 4000"
                type="number"
                name="left_4000"
                 :error="form.errorsFor('left_4000')"
                placeholder="Izquierda 4000"
            ></vue-input>
            <vue-input class="col-md-4"
                v-model="form.left_6000"
                label="Izquierda 6000"
                type="number"
                name="left_6000"
                 :error="form.errorsFor('left_6000')"
                placeholder="Izquierda 6000"
            ></vue-input>
        </b-form-row>
        <b-form-row>
            <vue-input class="col-md-4"
                v-model="form.left_8000"
                label="Izquierda 8000"
                type="number"
                name="left_8000"
                 :error="form.errorsFor('left_8000')"
                placeholder="Izquierda 8000"
            ></vue-input>
            <vue-input class="col-md-4"
                v-model="form.right_500"
                label="Derecha 500"
                type="number"
                name="right_500"
                 :error="form.errorsFor('right_500')"
                placeholder="Derecha 500"
            ></vue-input>
            <vue-input class="col-md-4"
                v-model="form.right_1000"
                label="Derecha 1000"
                type="number"
                name="right_1000"
                 :error="form.errorsFor('right_1000')"
                placeholder="Derecha 1000"
            ></vue-input>
        </b-form-row>   
        <b-form-row>
            <vue-input class="col-md-4"
                v-model="form.right_2000"
                label="Derecha 2000"
                type="number"
                name="right_2000"
                 :error="form.errorsFor('right_2000')"
                placeholder="Derecha 2000"
            ></vue-input>
            <vue-input class="col-md-4"
                v-model="form.right_3000"
                label="Derecha 3000"
                type="number"
                name="right_3000"
                 :error="form.errorsFor('right_3000')"
                placeholder="Derecha 3000"
            ></vue-input>
            <vue-input class="col-md-4"
                v-model="form.right_4000"
                label="Derecha 4000"
                type="number"
                name="right_4000"
                 :error="form.errorsFor('right_4000')"
                placeholder="Derecha 4000"
            ></vue-input>
        </b-form-row>   
        <b-form-row>
            <vue-input class="col-md-4"
                v-model="form.right_6000"
                label="Derecha 6000"
                type="number"
                name="right_6000"
                 :error="form.errorsFor('right_6000')"
                placeholder="Derecha 6000"
            ></vue-input>
            <vue-input class="col-md-4"
                v-model="form.right_8000"
                label="Derecha 8000"
                type="number"
                name="right_8000"
                 :error="form.errorsFor('right_8000')"
                placeholder="Derecha 8000"
            ></vue-input>
            <vue-input class="col-md-4"
                v-model="form.test_score"
                label="Resultado de la prueba"
                type="number"
                name="test_score"
                 :error="form.errorsFor('test_score')"
                placeholder="Resultado de la prueba"
            ></vue-input>
        </b-form-row>    
        <b-form-row>
            <vue-input class="col-md-6"
                v-model="form.left_clasification"
                label="Clasificacion Izquierda"
                type="text"
                name="left_clasification"
                 :error="form.errorsFor('left_clasification')"
                placeholder="Clasificacion Izquierda"
            ></vue-input>
            <vue-input class="col-md-6"
                v-model="form.right_clasification"
                label="Clasificacion Derecha"
                type="text"
                name="right_clasification"
                 :error="form.errorsFor('right_clasification')"
                placeholder="Clasificacion Derecha"
            ></vue-input>
        </b-form-row> 
        <vue-textarea v-model="form.recommendations"
            label="Recomendaciones"
            :error="form.errorsFor('recommendations')"
            name="recommendations"
            placeholder="Recomendaciones"
        ></vue-textarea>

        <vue-textarea v-model="form.obs"
            label="OBS"
            :error="form.errorsFor('obs')"
            name="obs"
            placeholder="OBS"
        ></vue-textarea>

        <b-btn type="submit" variant="primary">Guardar</b-btn>
        <b-btn variant="default" :to="cancelUrl" :disabled="loading">Cancelar</b-btn>
    </b-form>
</template>

<script>
import VueAjaxAdvancedSelect from "@/components/Inputs/VueAjaxAdvancedSelect.vue";
import VueInput from "@/components/Inputs/VueInput.vue";
import VueDatepicker from "@/components/Inputs/VueDatepicker.vue";
import VueTextarea from "@/components/Inputs/VueTextarea.vue";
import Form from "@/components/utils/Form.js";

export default {
  components: {
    VueAjaxAdvancedSelect,
    VueInput,
    VueDatepicker,
    VueTextarea
  },

  props: {
    url: { type: String, required: true },
    method: { type: String, required: true },
    cancelUrl: { type: [String,Object], required: true },
    isEdit: { type: Boolean, default: false },
    employeesDataUrl: { type: String, required: true },
    audiometry: {
      default() {
        return {
          date: "",
          previews_events: "",
          type: "",
          employee_id: "",
          work_zone_noise: "",
          exposition_level: "",
          left_500: "",
          left_1000: "",
          left_2000: "",
          left_3000: "",
          left_4000: "",
          left_6000: "",
          left_8000: "",
          right_500: "",
          right_1000: "",
          right_2000: "",
          right_3000: "",
          right_4000: "",
          right_6000: "",
          right_8000: "",
          left_clasification: "",
          right_clasification: "",
          recommendations: "",
          obs: "",
          test_score: "",
          epp: ""
        };
      }
    }
  },

  data() {
    return {
      loading: false,
      form: Form.makeFrom(this.audiometry, this.method),

      disabledDates: {
        from: new Date()
      }
    };
  },
    computed:{

    },

  methods: {
    submit(e) {
      this.loading = true;
          this.form.submit(e.target.action)
          .then(response => {
              console.log(response);
              this.loading = false;
              //location.href = response.data.url;
          })
          .catch(error => {
            this.loading = false;
              this.$notify({
                group: 'app',
                title: 'Error',
                text: "Error al iniciar sesion"
              });
          });
    }
  }
};
</script>
