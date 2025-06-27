<template>

  <b-form :action="url" @submit.prevent="submit" autocomplete="off">
    <b-form-row>
      <vue-input :disabled="isEdit || viewOnly" class="col-md-12" v-model="form.name" label="Nombre" type="text" name="name" :error="form.errorsFor('name')" placeholder="Nombre"></vue-input>
    </b-form-row>

    <b-card no-body class="mb-2 border-secondary" style="width: 100%;">
      <b-card-header class="bg-secondary">
        <b-row>
          <b-col cols="11" class="d-flex justify-content-between"> Columnas </b-col>
          <b-col cols="1">
            <div class="float-right">
              <b-button-group>
                <b-btn href="javascript:void(0)" v-b-toggle="'accordion-field'" variant="link">
                  <span class="collapse-icon"></span>
                </b-btn>
              </b-button-group>
            </div>
          </b-col>
        </b-row>
      </b-card-header>

      <b-collapse :id="`accordion-field`" visible :accordion="`accordion-master`">
        <b-card-body>
          <template v-for="(field, index) in form.columnas">
            <div :key="index.key">
              <b-form-row>
                <div class="col-md-12">
                  <div class="float-right">
                    <b-btn variant="outline-primary icon-btn borderless" size="sm" v-if="!viewOnly" v-b-tooltip.top title="Eliminar" @click.prevent="removeField(index)"><span class="ion ion-md-close-circle"></span></b-btn>
                  </div>
                </div>
                <vue-input class="col-md-12" v-model="field.value" :label="`Columna ${index + 1}`" name="field" type="text" placeholder="Nombre" :disabled="viewOnly"></vue-input>
              </b-form-row>
            </div>
          </template>

          <b-form-row style="padding-bottom: 20px;">
            <div class="col-md-12">
              <center><b-btn variant="primary" v-if="!viewOnly" @click.prevent="addField()"><span class="ion ion-md-add-circle"></span>&nbsp;&nbsp;Agregar Columna</b-btn></center>
            </div>
          </b-form-row>
        </b-card-body>
      </b-collapse>
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
import Form from "@/utils/Form.js";

export default {
  components: {
    VueInput
  },
  props: {
    url: { type: String },
    method: { type: String },
    cancelUrl: { type: [String, Object], required: true },
    isEdit: { type: Boolean, default: false },
    viewOnly: { type: Boolean, default: false },
    table: {
      default() {
        return {
          name: '',
          columnas: [],
          delete: []
        };
      }
    }
  },
  watch: {
    table() {
      this.loading = false;
      this.form = Form.makeFrom(this.table, this.method);
    },
    'form.name'() {
      this.form.name = this.formatValue(this.form.name);
    },
    'form.columnas': {
      handler(val) {
        this.form.columnas.forEach(columna => {
          columna.value = this.formatValue(columna.value);
        });
      },
      deep: true
    }
  },
  data() {
    return {
      loading: this.isEdit,
      form: Form.makeFrom(this.table, this.method),
    };
  },
  methods: {
    submit(e) {

      this.form.columnas ? JSON.stringify(this.form.columnas) : []
      
      this.loading = true;
      this.form
        .submit(e.target.action)
        .then(response => {
          this.loading = false;
          this.$router.push({ name: "absenteeism-tables" });
        })
        .catch(error => {
          this.loading = false;
        });
    },
    formatValue(value) {
      return value.replace(/((?![a-zA-Z0-9_]).)*/g, '').toLowerCase();
    },
    addField() {
      this.form.columnas.push({
        key: new Date().getTime() + Math.round(Math.random() * 10000),
        value: '',
        old: ''
      })
	  },
    removeField(index)
    {
      if (this.form.columnas[index].old != undefined)
        this.form.delete.push(this.form.columnas[index].old)

      this.form.columnas.splice(index, 1)
    }
  }
};
</script>
