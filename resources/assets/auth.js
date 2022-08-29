require('./entry-point');

import Vue from 'vue'
import BootstrapVue from 'bootstrap-vue'

import globals from './globals'
import Popper from 'popper.js'

import Notifications from 'vue-notification'
import VueSignaturePad from 'vue-signature-pad';
/*import FlowyPlugin from "@hipsjs/flowy-vue";
import "@hipsjs/flowy-vue/dist/lib/flowy-vue.css";*/

// Required to enable animations on dropdowns/tooltips/popovers
Popper.Defaults.modifiers.computeStyle.gpuAcceleration = false

Vue.config.productionTip = false

Vue.use(BootstrapVue)
Vue.use(Notifications)
Vue.use(VueSignaturePad);
//Vue.use(FlowyPlugin);

const DiagramaFlujo = {
  data () {
    return {
      text: 'This is component A'
    }
  },
  props: ['remove', 'node', 'title', 'description'],
  template: `
    <b-card no-body bg-variant="transparent" border-variant="dark" class="mb-3 box-shadow-none" >
    <b-card-body style="
    padding: 2px !important;
">
      <b-row>
        <b-col v-html="description"/>
      </b-row>
    </b-card-body>
    </b-card>
  `
}

Vue.component('diagrama-flujo', DiagramaFlujo)

// Global RTL flag
Vue.mixin({
  data: globals
});

Vue.component('header-module', require('./views/headerModule.vue'));

import Login from './components/Administrative/Auth/LoginComponent.vue';
import Footerlogin from './components/LayoutFooter.vue';
import MailResetPassword from './components/Administrative/Auth/MailResetPasswordComponent.vue';
import GeneratePassword from './components/Administrative/Auth/GeneratePasswordComponent.vue';
import PasswordReset from './components/Administrative/Auth/PasswordResetComponent.vue';
import TrainingEmployee from './components/LegalAspects/Contracts/Trainings/FormTrainingEmployeeComponent.vue';
import DeliveryEmployee from './components/IndustrialSecure/Epp/FormDeliveryFirmEmployeeComponent.vue';
import CausesExport from './components/IndustrialSecure/AccidentsWork/FormCausesExportComponent.vue';

/* eslint-disable no-new */
new Vue({
  el: '#app',
  components: { Footerlogin,Login,MailResetPassword,GeneratePassword,PasswordReset,TrainingEmployee, DeliveryEmployee, CausesExport}
})
