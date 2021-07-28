<template>
    <b-form :action="url" @submit.prevent="submit" autocomplete="off">
        <perfect-scrollbar :options="{ wheelPropagation: true }" class="mb-4" style="height: 500px; padding-right: 15px; width: 100%;">
            <table class="table table-bordered table-hover" v-if="theme.items.length > 0">
                <thead class="bg-secondary">
                    <tr>
                        <th scope="col" class="align-middle">#</th>
                        <template v-for="(item, index2) in theme.items">
                    </tr>
                </thead>
                <tbody>
                    <template v-for="(item, index2) in theme.items">
                        <tr :key="index2">
                            <td style="padding: 0px;">
                                <vue-textarea :disabled="true" class="col-md-12" v-model="form.themes[index].items[index2].description" label="" name="description" placeholder="Descripción" rows="1"></vue-textarea>
                            </td>
                            <template v-for="(item, index2) in theme.items">
                        </tr>
                    </template>
                </tbody>
            </table>
        </perfect-scrollbar>

        <div class="row float-right pt-10 pr-10" style="padding-top: 20px;">
            <template>
                <b-btn variant="default" :to="cancelUrl" :disabled="loading">{{"Cancelar"}}</b-btn>&nbsp;&nbsp;
                <b-btn type="submit" :disabled="loading" variant="primary">Finalizar</b-btn>
            </template>
        </div>
    </b-form>
</template>

<script>
import VueInput from "@/components/Inputs/VueInput.vue";
import VueRadio from "@/components/Inputs/VueRadio.vue";
import PerfectScrollbar from '@/vendor/libs/perfect-scrollbar/PerfectScrollbar';
import Form from "@/utils/Form.js";
import VueCheckboxSimple from "@/components/Inputs/VueCheckboxSimple.vue";
import Alerts from '@/utils/Alerts.js';
import Loading from "@/components/Inputs/Loading.vue";

export default {
  components: {
    VueInput,
    VueRadio,
    PerfectScrollbar,
    VueCheckboxSimple,
    Loading
  },
  props: {
    url: { type: String },
    method: { type: String },
    cancelUrl: { type: [String, Object], required: true },
    userDataUrl: { type: String, default: "" },
    isEdit: { type: Boolean, default: false },
    viewOnly: { type: Boolean, default: false },
    information: {
      default() {
        return {
            contracts: [
                
            ],
            activities: [],
            
        };
      }
    }
  },
  mounted() {
        setTimeout(() => {
            //this.$refs.wizardFormEvaluation.activateAll();
        }, 4000)
  },
  watch: {
    information() {
      this.loading = false;
      this.form = Form.makeFrom(this.evaluation, this.method);
    }
  },
  data() {
    return {
        cargando: false,
        loading: this.isEdit,
        form: Form.makeFrom(this.evaluation, this.method),
        
    };
  },
  methods: {
    submit(e) {
      this.loading = true;
      this.form
        .submit(e.target.action)
        .then(response => {
            this.loading = false;
            this.$router.push({ name: "legalaspects-contractor" });
        })
        .catch(error => {
            this.loading = false;
        });
    }
  }
};
</script>
