<template>
    <b-form-group>
        <div slot="label" :class="classBlock">
            <div v-if="label">{{label}}</div>
            <a v-if="textBlock" :href="actionBlock" class="d-block small">{{textBlock}}</a>
        </div>
        <multiselect v-model="selectValue"
                         :options="options"
                         label="name"
                         track-by="value"
                         :multiple="multiple"
                         :placeholder="placeholder"
                         :disabled="disabled"
                         deselect-label="Puede quitar este valor"
                         :hide-selected="multiple"
                         :show-labels="false"
                         @input="updateValue"
                         :allow-empty="false"
                         @search-change="asyncFind"
                         :internal-search="false"
                         :options-limit="300"
                         :searchable="true"
                         :loading="isLoading"
                         :close-on-select="!multiple"
                        :limit="5"
                        :limit-text="limitText"
                        :class="state"
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
        actionBlock: {type: String},
        url: { type: String, required: true },
        selectedObject: { type: Object },
    },
    components:{
        Multiselect,
    },
    data() {
        return {
            selectValue: [],
            allowEvent: true,
            options: [],
            isLoading: false
        }
    },
    methods: {
        limitText (count) {
            return `y ${count} mas`
        },
         updateValue() {
            if (this.allowEvent) {
                this.$emit('input', this.selectValue.value);
            }
        },
        asyncFind(keyword) {
            this.isLoading = true;
            axios.post(this.url, {keyword})
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
        }
    },
    watch: {
      selectedObject(){
        this.selectValue = this.selectedObject;
        this.options.push(this.selectedObject);
      }
    },
    mounted() {
         if (this.selectedObject) {
            this.options.push(this.selectedObject);
        }
        if (this.value) {
            this.selectValue = _.find(this.options, {value: this.value});
        }
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
</style>