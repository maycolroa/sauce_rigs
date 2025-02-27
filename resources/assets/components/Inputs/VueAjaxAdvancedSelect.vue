<template>
    <b-form-group>
        <div slot="label" :class="classBlock">            
            <b-row align-v="end">
              <b-col>
                <div v-if="label">{{label}}</div>
                <a v-if="textBlock" :href="actionBlock" class="d-block small">{{textBlock}}</a>
              </b-col>
              <b-col v-if="btnLabelPopover && Object.keys(btnLabelPopover).length > 0">
                <div class="float-right" style="padding-right: 10px;">
                    <b-btn v-b-popover.hover.focus.left="btnLabelPopover.content" :title="btnLabelPopover.title" variant="primary" class="btn-circle-micro"><span :class="btnLabelPopover.icon"></span></b-btn>
                </div>
              </b-col>
            </b-row>
        </div>
        <multiselect v-model="selectValue"
                         :options="options"
                         label="name"
                         track-by="value"
                         :multiple="multiple"
                         :placeholder="placeholder"
                         :disabled="disabled"
                         deselect-label="Puede quitar este valor"
                         :show-labels="false"
                         @input="updateValue"
                         :allow-empty="allowEmpty"
                         @open="asyncFind"
                         @search-change="asyncFind"
                         :internal-search="false"
                         :options-limit="300"
                         :searchable="true"
                         :loading="isLoading"
                         :close-on-select="!multiple"
                        :limit="limit"
                        :limit-text="limitText"
                        :class="state"
                        tag-placeholder="Añadir esto como nueva etiqueta"
                        :taggable="taggable"
                        @tag="addTag"
            >
                <span slot="noResult">No se encontraron elementos</span>
            </multiselect>
            <b-form-invalid-feedback v-if="error" :force-show="true">
                {{error}}
            </b-form-invalid-feedback>
    </b-form-group>
</template>
<style src="@/vendor/libs/vue-multiselect/vue-multiselect.scss" lang="scss"></style>
<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>

<script>
import Multiselect from 'vue-multiselect'
export default {
    props: {
        error: { type: String },
        name: { type: String, required: true },
        value: [String, Number, Object, Array],
        label: { type: String, default: '' },
        disabled: { type: Boolean, default: false },
        placeholder: { type: String, default: '' },
        multiple: { type: Boolean, default: false },
        textBlock: {type: String},
        limit: { type: Number, default:1000 },
        actionBlock: {type: String},
        url: { type: String, required: true },
        selectedObject: { type: [Object, Array] },
        parameters: { type: Object},
        emptyAll: {type: Boolean, default: false},
        taggable: {type: Boolean, default: false},
        allowEmpty: {type: Boolean, default: false},
        btnLabelPopover: { type: Object },
    },
    components:{
        Multiselect,
    },
    data() {
        return {
            selectValue: [],
            allowEvent: true,
            options: [],
            isLoading: false,
            postData: ''
        }
    },
    methods: {
        limitText (count) {
            return `y ${count} mas`
        },
         updateValue() {
            if (this.allowEvent) {
                let value = this.multiple ? this.selectValue : (this.selectValue ? this.selectValue.value : '');

                this.$emit('input', value);

                if (!this.multiple)
                {
                    this.$emit("selectedName", this.selectValue ? this.selectValue.name : '');
                }
            }
        },
        asyncFind(keyword) {
            this.isLoading = true;

            if (this.parameters)
            {
                this.postData = Object.assign({}, {keyword}, this.parameters);
            }
            else
            {
                this.postData = {keyword}
            }
            axios.post(this.url, this.postData)
                .then(resp => {
                    this.isLoading = false;
                    if (resp.data.options) {
                        this.options = resp.data.options;
                    }
                })
                .catch(error => {
                    console.log('error');
                    this.isLoading = false;
                });
        },
        addTag (newTag) {
            this.selectValue.push({
                name: newTag,
                value: newTag
            })

            this.updateValue()
        },
        setMultiselectValue() {
            if (this.value && this.options.length > 0) {
                if (this.multiple) {
                    if (typeof this.value == "object") {
                        this.selectValue = this.value;
                    } else {
                        this.selectValue = this.value.split(",").map(v => {
                        
                        return {'name': v, 'value': v}
                        });
                    }
                } else {
                    this.selectValue = this.value
                        ? _.find(this.options, { value: this.value })
                        : "";
                }

                this.updateValue()
            }
            else if (this.value && this.options.length == 0 && this.taggable) {
                if (this.multiple) {
                    if (typeof this.value == "object") {
                        this.selectValue = this.value;
                    } else {
                        this.selectValue = this.value.split(",").map(v => {
                        
                        return {'name': v, 'value': v}
                        });
                    }
                }

                this.updateValue()
            }
        },
    },
    watch: {
      selectedObject(){
        this.selectValue = this.selectedObject;

        if (typeof this.selectedObject == "object")
            this.options.push(this.selectedObject);
        else 
            _.forIn(this.selectedObject, (value, key) => {
                this.options.push(value);
            })
      },
      emptyAll(){
        if (this.emptyAll)
        {
            this.selectValue = []
            this.options.splice(0)
            this.$emit('updateEmpty')
        }
      }
    },
    mounted() {
        if (this.selectedObject) {
            if (typeof this.selectedObject == "object")
                this.options.push(this.selectedObject);
            else 
                _.forIn(this.selectedObject, (value, key) => {
                    this.options.push(value);
                })
        }

        setTimeout(() => {
            this.setMultiselectValue();
        }, 3000)
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
}
</script>
<style>
  .multiselect--disabled{
    opacity: 1 !important;
  }

  .multiselect .multiselect__tag {
    overflow: visible !important;
    white-space: break-spaces !important;
    height: 100% !important;  
  }
</style>