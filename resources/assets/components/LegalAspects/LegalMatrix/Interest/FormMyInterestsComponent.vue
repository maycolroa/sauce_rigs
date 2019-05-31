<template>

  <b-form :action="url" @submit.prevent="submit" autocomplete="off">
    <b-form-row>
      <vue-checkbox style="padding-top: 20px;" :disabled="viewOnly" class="col-md-12" v-model="form.values" :checked="form.values" label="Intereses" name="interests" :options="options" :vertical="true"></vue-checkbox>
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
import VueCheckbox from "@/components/Inputs/VueCheckbox.vue";
import Form from "@/utils/Form.js";

export default {
  components: {
    VueCheckbox
  },
  props: {
    url: { type: String },
    method: { type: String },
    cancelUrl: { type: [String, Object], required: true },
    isEdit: { type: Boolean, default: false },
    viewOnly: { type: Boolean, default: false },
    options: {
      type: Array,
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
  watch: {
    'interest.values'() {
      this.loading = false;
      this.form = Form.makeFrom(this.interest, this.method);
    }
  },
  data() {
    return {
      loading: this.isEdit,
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
    }
  }
};
</script>
