<template>
    <b-form-group>
        <div slot="label" :class="classBlock">
            <div v-if="label">{{label}}</div>
            <a v-if="textBlock" :href="actionBlock" class="d-block small">{{textBlock}}</a>
        </div>
            <flat-pickr
                :disabled="disabled" 
                :config="rangeConfig"          
                :value="value" 
                :placeholder="placeholder" 
                :name="name"
                :alt-input-class="inputclass"
                @input="updateValue($event)" />
                <b-form-invalid-feedback v-if="error" :force-show="true">
                   {{error}}
                </b-form-invalid-feedback>
    </b-form-group>
</template>
<style src="@/vendor/libs/vue-flatpickr-component/vue-flatpickr-component.scss" lang="scss"></style>
<script>
import FlatPickr from 'vue-flatpickr-component'
import {es} from 'vuejs-datepicker/dist/locale'

function isRTL () {
  return document.documentElement.getAttribute('dir') === 'rtl' ||
         document.body.getAttribute('dir') === 'rtl'
}

export default {
  props: {
    error: {type: String, default: null},
    label: {type: String},
    value: {type: String, default:''},
    placeholder: {type:String},
    name: { type: String, required: true },
    disabled: { type: Boolean, default: false },
    textBlock: {type: String},
    actionBlock: {type: String},
  },
  data () {
    return {
      es: es,
      rangeConfig: {
        mode: 'range',
        altInput: true,
        animate: !isRTL()
      },
    }
  },
  components:{
      FlatPickr
  },
  computed:{
      inputclass(){
        return this.error ? (this.disabled ? 'is-invalid disabled-class': 'is-invalid') :  (this.disabled ? 'disabled-class' : null);
          if(!this.error){
              return null;
          }
          else{
              return 'is-invalid';
          }
      },
      classBlock(){
          return this.textBlock ? 'd-flex justify-content-between align-items-end' : '';
      },
  },
  methods: {
    updateValue(value) {
      this.$emit('input', value.toDateString());
    }
  }
}
</script>
<style>
.disabled-class {
  background-color: #f1f1f2 !important;
}
</style>
