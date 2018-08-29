<template>
    <b-form-group>
        <div slot="label" :class="classBlock">
            <div v-if="label">{{label}}</div>
            <a v-if="textBlock" :href="actionBlock" class="d-block small">{{textBlock}}</a>
        </div>
            <datepicker
                :value="value" 
                :placeholder="placeholder" 
                :name="name"
                :input-class="state"
                :disabled="disabled"
                @input="updateValue($event)"
                :bootstrapStyling="true"
                :monday-first="true"
                :full-month-name="fullMonthName"
                :disabled-dates="disabledDates" />
                <b-form-invalid-feedback v-if="error" :force-show="true">
                   {{error}}
                </b-form-invalid-feedback>
    </b-form-group>
</template>
<style src="@/vendor/libs/vuejs-datepicker/vuejs-datepicker.scss" lang="scss"></style>
<script>
import Datepicker from 'vuejs-datepicker'
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
    fullMonthName: {type: Boolean, default: true},
    disabledDates: {type: Object}
  },
  components:{
      Datepicker
  },
  computed:{
      state(){
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
        console.log(value.toDateString());
      this.$emit('input', value.toDateString());
    }
  }
}
</script>