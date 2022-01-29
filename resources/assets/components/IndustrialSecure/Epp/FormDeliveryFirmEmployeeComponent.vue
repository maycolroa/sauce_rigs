<template>
  <div>
    <header-module
      title="ELEMENTOS DE PROTECCIÓN"
      subtitle="ENTREGA EPP"
    />

    <div class="col-md-10 offset-1" v-if="!error && !result">
      <b-card no-body>
        <b-card-body>
            <b-form :action="action" @submit.prevent="submit" autocomplete="off">
              <div class="my-4 mx-2 text-right" v-if="form.logo">
                  <img class="ui-w-100" :src="`${form.logo}`" alt="">
              </div>
                <b-card bg-variant="transparent" title="ENTREGA DE ELEMENTOS DE PROTECCIÓN PERSONAL" style="text-align: center;" class="mb-3 box-shadow-none">
                  <br><br>

                  <div style="text-align: justify" v-html="form.text_company"></div>

                  <br><br>
                  <table class="table table-bordered table-hover">

                    <thead class="bg-secondary">
                      <tr>
                        <th scope="col" style="text-align: center;" >Descripción del Elemento</th>
                        <th scope="col" style="text-align: center;" >Cantidad</th>
                      </tr>
                    </thead>
                    <tbody>
                      <template v-for="(item, index2) in form.elements_id">
                        <tr :key="index2">
                          <td style="padding: 0px; text-align: center;">
                            {{item.name}}
                          </td>
                          <td style="padding: 0px; text-align: center;">
                            {{item.quantity}}
                          </td>
                        </tr>
                      </template>
                    </tbody>
                  </table>

                  <br><br>

                  <table class="table table-bordered table-hover" >
                    <tbody>
                      <tr>
                        <th scope="col" style="text-align: center;" class="bg-secondary" >Fecha de entrega</th>
                        <th scope="col" style="text-align: center;" >{{form.created_at}}</th>
                      </tr>
                     <tr>
                        <th scope="col" style="text-align: center;" class="bg-secondary">Nombre de quien realizo la entrega</th>
                        <th scope="col" style="text-align: center;" >{{form.user_name}}</th>
                      </tr>
                    </tbody>
                  </table>

                  <br><br>

                  <table class="table table-bordered table-hover" >
                    <tbody>
                      <tr>
                        <th scope="col" colspan="2" style="text-align: center;" class="bg-secondary">Observaciones</th>
                      </tr>
                      <tr>
                        <th scope="col" colspan="2" style="text-align: center;" >{{form.observations}}</th>
                      </tr>
                    </tbody>
                  </table>

                  <div>
                    <center>
                      <p><b>Ingresa aqui tu firma</b></p>
                      <VueSignaturePad
                          id="signature"
                          width="100%"
                          height="250px"
                          ref="signaturePad"
                          v-model="form.firm_employee"
                      />
                      <br>
                      <div>
                          <b-btn variant="default" @click="undo">Borrar</b-btn>
                      </div>
                      <br>
                    </center>
                  </div>
                </b-card>

                <b-modal ref="modalConfirm" :hideFooter="true" id="modals-historial2" class="modal-top" size="lg">
                    <div slot="modal-title">
                        <h4>Confirmación</h4>
                    </div>
                    <center>
                        <p> ¿Esta seguro de que desea continuar con el envio del documento? </p>
                    </center>

                    <div class="row float-right pt-12 pr-12y">                      
                        <b-btn type="submit" :disabled="loading" variant="primary">SI</b-btn>&nbsp;&nbsp;
                        <b-btn variant="primary" @click="$refs.modalConfirm.hide()">NO</b-btn>
                    </div>
                </b-modal>

                 <div class="row float-right pt-10 pr-10" style="padding-top: 20px; padding-right: 25px;">
                  <template>
                    <b-btn @click="$refs.modalConfirm.show()" :disabled="loading" variant="primary">Firmar</b-btn>
                  </template>
                </div>
            </b-form>
        </b-card-body>
      </b-card>
    </div>
    <div class="col-md-10 offset-1" v-if="error">
      <b-card no-body>
        <b-card-body>
          <center><h3>{{ error }}</h3></center>
        </b-card-body>
      </b-card>
    </div>
    <div class="col-md-10 offset-1" v-if="result">
        <b-card no-body>
          <b-card-body>
            <center><h3>{{ result }}</h3></center>
          </b-card-body>
        </b-card>
    </div>
  </div>
</template>

<style src="@/vendor/libs/vue-form-wizard/vue-form-wizard.scss" lang="scss"></style>

<script>
import VueAdvancedSelect from "@/components/Inputs/VueAdvancedSelect.vue";
import VueRadio from "@/components/Inputs/VueRadio.vue";
import PerfectScrollbar from '@/vendor/libs/perfect-scrollbar/PerfectScrollbar';
import Form from "@/utils/Form.js";
import Loading from "@/components/Inputs/Loading.vue";

export default {
  components: {
    VueAdvancedSelect,
    VueRadio,
    PerfectScrollbar,
    Loading
  },
  props: {
    action: { type: String },
    method: { type: String },
    error: { type: String },
    delivery: {
      default() {
        return {
            firm_employee: '', 
            created_at: '',
            user_name: '',
            obsercations: '',
            elements: [],
            text_company: ''
        };
      }
    }
  },
  data() {
    return {
        loading: this.isEdit,
        form: Form.makeFrom(this.delivery, this.method),
        result: ''
    };
  },
  methods: {
    submit(e) {
      this.loading = true;
      this.$refs.modalConfirm.hide()
      const { isEmpty, data } = this.$refs.signaturePad.saveSignature()
      if (data != null) {
        this.form.firm_employee = data
      }
                        
      this.form
        .submit(e.target.action)
        .then(response => {
          this.loading = false;
          this.result = response.data.result;
        })
        .catch(error => {
          this.loading = false;
        });
    },
    undo () {
      this.$refs.signaturePad.undoSignature()
    },
  }
};
</script>
<style>
  #signature {
    border: double 3px transparent;
    border-radius: 5px;
    background-image: linear-gradient(white, white),
      radial-gradient(circle at top left, #9f6274, #9f6274);
    background-origin: border-box;
    background-clip: content-box, border-box;
  }
</style>
