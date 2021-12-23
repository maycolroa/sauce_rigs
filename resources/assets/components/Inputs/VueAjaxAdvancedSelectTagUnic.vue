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
        allowEmpty: {type: Boolean, default: false}
    },
    components:{
        Multiselect,
    },
    data() {
        return {
            selectValue: '',
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
        updateValue(event, onChange = false) {

            let value = this.selectValue ? [this.selectValue] : [];

            this.$emit("input", value);

            if (onChange)
                this.$emit("change");
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
            this.selectValue = {
                name: newTag,
                value: newTag
            }

            this.updateValue()
        },
        setMultiselectValue() {
            if (this.value && this.options.length == 0 && this.taggable) {

                this.options.push({'name': this.value, 'value': this.value})
                this.selectValue = {
                    name: this.value,
                    value: this.value
                }

                this.updateValue()
            }
        },
    },
    mounted() {
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
</style>