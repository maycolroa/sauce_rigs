<template>

  <b-form :action="url" @submit.prevent="submit" autocomplete="off">
    <b-form-row>
       <vue-ajax-advanced-select :disabled="viewOnly" class="col-md-12" v-model="filterInterests" name="types" label="Filtrar Intereses" placeholder="Seleccione los intereses"  :url="urlDataInterests" :multiple="true" :allowEmpty="true" >
        </vue-ajax-advanced-select>
    </b-form-row>
    <b-form-row v-if="auth.company_id == 130">       
        <b-btn @click="checkAll()" :disabled="loading" variant="primary">Marcar todos los intereses</b-btn>&nbsp;&nbsp;
        <b-btn @click="unCheckAll()" :disabled="loading" variant="primary">Desmarcar todos los intereses</b-btn>
    </b-form-row>
    <b-form-row>
      <vue-checkbox style="padding-top: 20px;" :disabled="viewOnly" class="col-md-12" v-model="form.values" :checked="form.values" label="Intereses" name="interests" :options="optionsFilters" :vertical="true" :descriptions="descriptions"></vue-checkbox>
    </b-form-row>

    <div class="row float-right pt-10 pr-10">
      <template>
        <b-btn variant="default" :to="cancelUrl" :disabled="loading">Atras</b-btn>&nbsp;&nbsp;
        <b-btn type="submit" :disabled="loading" variant="primary">Guardar</b-btn>
      </template>
    </div>
  </b-form>
</template>

<script>
import VueAjaxAdvancedSelect from "@/components/Inputs/VueAjaxAdvancedSelect.vue";
import VueCheckbox from "@/components/Inputs/VueCheckboxIcons.vue";
import Form from "@/utils/Form.js";

export default {
  components: {
    VueCheckbox,
    VueAjaxAdvancedSelect
  },
  props: {
    url: { type: String },
    method: { type: String },
    cancelUrl: { type: [String, Object], required: true },
    isEdit: { type: Boolean, default: false },
    viewOnly: { type: Boolean, default: false },
    urlDataInterests: { type: String, default: "" },
    options: {
      type: Array,
      default: function() {
        return [];
      }
    },
    descriptions: {
      type: [Array, Object],
      default: function() {
        return [];
      }
    },
    interest: {
      default() {
        return {
            values: []
        };
      }
    }
  },
  computed: {
        filters() {

          let result = []

          if (this.filterInterests.length > 0)
          {
            _.forIn(this.filterInterests, (value, key) => {
                result.push(value.value)
            });
          }

          return result;
        }
  },
  watch: {
    'interest.values'() {
      this.loading = false;
      this.form = Form.makeFrom(this.interest, this.method);
    },
    filters() {

      if (this.filters.length > 0)
      {
        this.optionsFilters = this.options.filter((f) => {
          return this.filters.includes(f.value)
        });
      }
      else
        this.optionsFilters = this.options
    }
  },
  data() {
    return {
      loading: this.isEdit,
      filterInterests: [],
      optionsFilters: this.options,
      form: Form.makeFrom(this.interest, this.method),
    };
  },
  methods: {
    submit(e) {
      this.loading = true;
      this.form
        .submit(e.target.action)
        .then(response => {
          this.loading = false;
          this.$router.push({ name: "legalaspects-legalmatrix" });
        })
        .catch(error => {
          this.loading = false;
        });
    },
    checkAll()
    {
      this.form.values.splice(0)

      this.optionsFilters.forEach(option => {
          this.form.values.push(option.value)
      })
    },
    unCheckAll()
    {
      this.form.values.splice(0)
    }
  }
};
</script>
