<template>
    <b-form :action="url" @submit.prevent="submit" autocomplete="off">
      <template v-if="form.contracts.length == 0">
        <div slot="modal-title">
          <h4>INFORMACIÓN</h4>
            <p> No existen contratistas sin actividades asignadas</p>
        </div>
      </template>
      <template v-else>
        <perfect-scrollbar :options="{ wheelPropagation: true }" class="mb-4" style="height: 50%; padding-right: 15px; width: 100%;">
            <table class="table table-bordered table-hover">
                <thead class="bg-secondary">
                    <tr>
                        <th scope="col" class="align-middle">Contratista</th>
                        <template v-for="(activity, index) in form.activities">
                          <th scope="col" class="align-middle" :key="activity.key">{{index + 1}}. {{ activity.name }}</th>
                        </template>
                    </tr>
                </thead>
                <tbody>
                  <template v-for="contract in form.contracts">
                    <tr :key="contract.id">
                        <td style="padding: 0px; text-aling: center;">{{contract.name}}</td>
                        <template v-for="activity in form.activities">
                          <td scope="col" class="align-middle" :key="activity.key">
                            <center class="radio-masive">
                              <vue-checkbox-simple class="col-md-3" label="" name="activities" :checked-value="activity.id" unchecked-value="" @input="updateValue(contract, activity.id, $event)"/>
                            </center>
                          </td>
                        </template>
                    </tr>
                  </template>
                </tbody>
            </table>
        </perfect-scrollbar>
      </template>

        <div class="row float-right pt-10 pr-10" style="padding-top: 20px;">
            <template>
                <b-btn variant="default" :to="cancelUrl" :disabled="loading">{{"Cancelar"}}</b-btn>&nbsp;&nbsp;
                <b-btn type="submit" v-show="form.contracts.length > 0" :disabled="loading" variant="primary">Finalizar</b-btn>
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
    data: {
      type: [Array, Object],
      default() {
        return {
          contracts: [],
				  activities: []
        };
      }
    }
  },
  watch: {
    data() {
      this.loading = false;
      this.form = Form.makeFrom(this.data, this.method);
    }
  },
  data() {
    return {
        cargando: false,
        loading: this.isEdit,
        form: Form.makeFrom(this.data, this.method)        
    };
  },
  methods: {
    updateValue(contract, id, event)
    {
      if (event)
        contract.selection.push(id);
      else
      {
        contract.selection = contract.selection.filter((f) => {
          return f != id
        });
      }
    },
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

<style lang="scss">
  .radio-masive {

    .custom-checkbox .custom-control-label::before {
      background-color: #e8e8e8
    }
  }
</style>
