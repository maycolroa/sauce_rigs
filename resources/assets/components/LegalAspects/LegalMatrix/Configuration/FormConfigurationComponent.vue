<template>

  <b-form :action="url" @submit.prevent="submit" autocomplete="off">
    <b-form-row>
      <label>Selecciona el color que correspondera a cada calificación:</label>
    </b-form-row>
    <br>
    <b-form-row>
     <vue-advanced-select class="col-md-4" v-model="form.sin_calificar" :error="form.errorsFor('sin_calificar')" :multiple="false" :options="colors" :hide-selected="false" name="sin_calificar" label="Sin Calificar" placeholder="Seleccione el color">
          </vue-advanced-select> 
      <vue-advanced-select class="col-md-4" v-model="form.cumple" :error="form.errorsFor('cumple')" :multiple="false" :options="colors" :hide-selected="false" name="cumple" label="Cumple" placeholder="Seleccione el color">
      </vue-advanced-select> 
      <vue-advanced-select class="col-md-4" v-model="form.no_cumple" :error="form.errorsFor('no_cumple')" :multiple="false" :options="colors" :hide-selected="false" name="no_cumple" label="No cumple" placeholder="Seleccione el color">
      </vue-advanced-select>           
    </b-form-row>

    <b-form-row>
     <vue-advanced-select class="col-md-4" v-model="form.en_estudio" :error="form.errorsFor('en_estudio')" :multiple="false" :options="colors" :hide-selected="false" name="en_estudio" label="En estudio" placeholder="Seleccione el color">
          </vue-advanced-select> 
      <vue-advanced-select class="col-md-4" v-model="form.parcial" :error="form.errorsFor('parcial')" :multiple="false" :options="colors" :hide-selected="false" name="parcial" label="Parcial" placeholder="Seleccione el color">
      </vue-advanced-select> 
      <vue-advanced-select class="col-md-4" v-model="form.no_aplica" :error="form.errorsFor('no_aplica')" :multiple="false" :options="colors" :hide-selected="false" name="no_aplica" label="No aplica" placeholder="Seleccione el color">
      </vue-advanced-select>           
    </b-form-row>

    <b-form-row>
     <vue-advanced-select class="col-md-4" v-model="form.informativo" :error="form.errorsFor('informativo')" :multiple="false" :options="colors" :hide-selected="false" name="informativo" label="Informativo" placeholder="Seleccione el color">
          </vue-advanced-select>      
     <vue-advanced-select class="col-md-4" v-model="form.no_vigente" :error="form.errorsFor('no_vigente')" :multiple="false" :options="colors" :hide-selected="false" name="no_vigente" label="No Vigente" placeholder="Seleccione el color">
          </vue-advanced-select>    
     <vue-advanced-select class="col-md-4" v-model="form.en_transicion" :error="form.errorsFor('en_transicion')" :multiple="false" :options="colors" :hide-selected="false" name="en_transicion" label="En Transición" placeholder="Seleccione el color">
          </vue-advanced-select>      
    </b-form-row>
    
    <b-form-row>  
     <vue-advanced-select class="col-md-4" v-model="form.pendiente_reglamentacion" :error="form.errorsFor('pendiente_reglamentacion')" :multiple="false" :options="colors" :hide-selected="false" name="pendiente_reglamentacion" label="Pendiente reglamentación" placeholder="Seleccione el color">
          </vue-advanced-select>      
    </b-form-row>

    <div class="row float-right pt-10 pr-10">
      <template>
        <b-btn type="submit" :disabled="loading" variant="primary">Guardar</b-btn>
      </template>
    </div>
  </b-form>
</template>

<script>
import VueInput from "@/components/Inputs/VueInput.vue";
import VueRadio from "@/components/Inputs/VueRadio.vue";
import VueTextarea from "@/components/Inputs/VueTextarea.vue";
import VueAdvancedSelect from "@/components/Inputs/VueAdvancedSelect.vue";
import Form from "@/utils/Form.js";

export default {
  components: {
    VueInput,
    VueRadio,
    VueTextarea,
    VueAdvancedSelect
  },
  props: {
    url: { type: String },
    method: { type: String },
    configuration: {
      default() {
        return {          
          sin_calificar: '',
          cumple: '',
          no_cumple: '',
          en_estudio: '',
          parcial: '',
          no_aplica: '',
          informativo: '',
          no_vigente: '',
          en_transicion: '',
          pendiente_reglamentacion: ''
        };
      }
    }
  },
  watch: {
    configuration() {
      this.loading = false;
      this.form = Form.makeFrom(this.configuration, this.method);
    }
  },
  data() {
    return {
      loading: this.isEdit,
      form: Form.makeFrom(this.configuration, this.method),
      colors: [
        {name: 'Amarillo', value: 'FFD950'},
        {name: 'Celeste', value: '7fafec'},
        {name: 'Verde', value: '02BC77'},
        {name: 'Rojo', value: 'f0635f'},
        {name: 'Naranja', value: 'ff9565'},
        {name: 'Mostaza', value: 'ca8622'},
        {name: 'Marron', value: 'd48265'},
        {name: 'Morado', value: '6A5ACD'},
        {name: 'Azul', value: '4169E1'},
        {name: 'Sin Color', value: 'ffffff'},
      ]
    };
  },
  methods: {
    submit(e) {
      this.loading = true;
      console.log(this.form)
      this.form
        .submit(e.target.action)
        .then(response => {
          this.loading = false;
          this.$router.push({ name: "legalaspects-legalmatrix" });
        })
        .catch(error => {
          this.loading = false;
        });
    }
  }
};
</script>
