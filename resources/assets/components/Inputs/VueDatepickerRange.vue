<template>
    <b-form-group>
        <b-row>
          <b-col>
            <div slot="label" :class="classBlock">
                <div v-if="label">{{label}}</div>
                <a v-if="textBlock" :href="actionBlock" class="d-block small">{{textBlock}}</a>
            </div>
          </b-col>
        </b-row>
        <b-row>
          <b-col>
            <datepicker
                :disabled="disabled"             
                placeholder="Fecha inicio" 
                :name="`${name}_1`"
                :input-class="inputclass"
                :language="es"
                v-model="date_ini"
                @input="updateValue($event)"
                :bootstrapStyling="true"
                :monday-first="true"
                :full-month-name="fullMonthName"
                :disabled-dates="disabledDatesIni"
                :highlighted="highlightedDates" />
          </b-col>
          <b-col>
            <datepicker
                :disabled="disabled"             
                placeholder="Fecha fin" 
                :name="`${name}_2`"
                :input-class="inputclass"
                :language="es"
                v-model="date_end"
                @input="updateValue($event)"
                :bootstrapStyling="true"
                :monday-first="true"
                :full-month-name="fullMonthName"
                :disabled-dates="disabledDatesEnd"
                :highlighted="highlightedDates" />
            </b-col>
        </b-row>
        <b-row>
            <b-col>
              <b-form-invalid-feedback v-if="error" :force-show="true">
                   {{error}}
                </b-form-invalid-feedback>
            </b-col>
          </b-row>
    </b-form-group>
</template>
<style src="@/vendor/libs/vuejs-datepicker/vuejs-datepicker.scss" lang="scss"></style>
<script>
import Datepicker from 'vuejs-datepicker'
import {es} from 'vuejs-datepicker/dist/locale'
import moment from 'moment'

export default {
  props: {
    error: {type: String, default: null},
    label: {type: String},
    value: {type: String, default:''},
    name: { type: String, required: true },
    disabled: { type: Boolean, default: false },
    textBlock: {type: String},
    actionBlock: {type: String},
    fullMonthName: {type: Boolean, default: true},
  },
  data () {
    return {
      es: es,
      date_ini: '',
      date_end: '',
    }
  },
  components:{
      Datepicker
  },
  computed:{
      inputclass(){
        return this.error ? (this.disabled ? 'is-invalid disabled-class': 'is-invalid') :  (this.disabled ? 'disabled-class' : null);
          if(!this.error){
              return null;
          }
          else {
              return 'is-invalid';
          }
      },
      classBlock(){
          return this.textBlock ? 'd-flex justify-content-between align-items-end' : '';
      },
      highlightedDates() {
        if (this.date_ini && this.date_end)
        {
          let toDate = new Date(this.date_end.getFullYear(), this.date_end.getMonth(), this.date_end.getDate())
          toDate = toDate.setDate(toDate.getDate()+1)
          return {
            to: toDate,
            from: new Date(this.date_ini.getFullYear(), this.date_ini.getMonth(), this.date_ini.getDate())
          }
        }
        else
        {
          return {
          }
        }
      },
      disabledDatesIni() {
        if (this.date_end)
        {
          return {
            from: this.date_end
          }
        }
        else
        {
          return {
          }
        }
      },
      disabledDatesEnd() {
        if (this.date_ini)
        {
          return {
            to: this.date_ini
          }
        }
        else
        {
          return {
          }
        }
      }
  },
  methods: {
    updateValue(value) {
      if (this.date_ini && this.date_end)
        this.$emit('input', this.date_ini.toDateString() +'/'+ this.date_end.toDateString());
    }
  }
}
</script>
<style>
.disabled-class {
  background-color: #f1f1f2 !important;
}
</style>
