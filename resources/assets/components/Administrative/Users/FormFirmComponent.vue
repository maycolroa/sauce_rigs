<template>
    <b-form :action="url" @submit.prevent="submit" autocomplete="off">
        <div>
          <div class="row">
            <div class="col-md-12">
                <center>
                    <div class="my-4 mx-2 text-center" v-if="form.old_firm">
                        <img class="ui-w-300" :src="`${form.firm_path}`" alt="">
                    </div>
                </center>
            </div>
          </div>
          <b-form-row>
            <vue-radio :checked="form.type" class="col-md-6 offset-md-3" v-model="form.type" :options="cargaDibujo" name="type" :error="form.errorsFor('type')" label="¿Como desea ingresar su firma?"></vue-radio>
          </b-form-row>
            <b-card>
                <div v-if="form.type == 'Dibujar'">
                    <center>
                        <p><b>Ingresa aqui tu firma</b></p>
                        <VueSignaturePad
                            id="signature"
                            width="100%"
                            height="250px"
                            ref="signaturePad"
                            v-model="form.firm_image"
                        />
                        <br>
                        <div>
                            <!--<b-btn variant="primary" @click="saveFirm">Guardar</b-btn>-->
                            <b-btn variant="default" @click="undo">Borrar</b-btn>
                        </div>
                        <br>
                    </center>
                </div>
                <div v-if="form.type == 'Cargar'">
                    <center>
                         <vue-file-simple class="col-md-12" v-model="form.firm_image" label="Firma" name="firm_image" placeholder="Seleccione un archivo" :error="form.errorsFor(`firm_image`)" :maxFileSize="20"/>
                    </center>
                </div>
            </b-card>

            <div class="row float-right pt-10 pr-10">
              <template>
                <b-btn variant="default" :to="cancelUrl" :disabled="loading">Cancelar</b-btn>&nbsp;&nbsp;
                <b-btn type="submit" :disabled="loading" variant="primary">Finalizar</b-btn>
              </template>
            </div>
        </div>
  </b-form>
</template>

<script>
import Form from "@/utils/Form.js";
import Alerts from '@/utils/Alerts.js';
import VueRadio from "@/components/Inputs/VueRadio.vue";
import VueFileSimple from "@/components/Inputs/VueFileSimple.vue";
import Loading from "@/components/Inputs/Loading.vue";

export default {
  components: {
    VueRadio,
    VueFileSimple,
    Loading
  },
  props: {
    url: { type: String },
    method: { type: String },
    cancelUrl: { type: [String, Object], required: true },
    isEdit: { type: Boolean, default: false },
    viewOnly: { type: Boolean, default: false },
    firm: {
        default() {
            return {
                firm_image: '',
                type: ''
            };
        }
    }
  },
  watch: {
    firm () {
      this.loading = false;
      this.form = Form.makeFrom(this.firm, this.method);
    }
  },
  data() {
    return {
      loading: this.isEdit,
      form: Form.makeFrom(this.firm, this.method),
      cargaDibujo: [
          {text: 'Cargar', value: 'Cargar'},
          {text: 'Dibujar', value: 'Dibujar'}
        ],
    };
  },
  methods: {
    submit(e) {
      this.loading = true;
      const { isEmpty, data } = this.$refs.signaturePad.saveSignature()
      if (data != null) {
        this.form.firm_image = data
      } else {
          if (this.form.type == 'Dibujar')
            Alerts.error('Error', 'Por favor ingrese su firma');
      }
      this.form
        .submit(e.target.action)
        .then(response => {
          this.loading = false;
          this.$router.push({ path: '/' });
        })
        .catch(error => {
          this.loading = false;
        });
    },
    undo () {
      this.$refs.signaturePad.undoSignature()
    }
  }
}
</script>

<style>
  #signature {
    border: double 3px transparent;
    border-radius: 5px;
    background-image: linear-gradient(white, white),
      radial-gradient(circle at top left, #4bc5e8, #9f6274);
    background-origin: border-box;
    background-clip: content-box, border-box;
  }
</style>