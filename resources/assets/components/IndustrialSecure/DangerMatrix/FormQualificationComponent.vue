<template>
    <div>
        <b-row>
            <b-col>
                <div class="float-right" style="padding-bottom: 20px;">
                    <b-btn v-b-popover.hover.focus.left="info" title="Descripción de las calificaciones" variant="primary" class="btn-circle-sm"><span class="fas fa-info"></span></b-btn>
                </div>
            </b-col>
        </b-row>
        <b-form-row
            v-if="data.length > 0">
            <vue-advanced-select 
                v-for="(item, index) in qualifications.types"
                :key="index" 
                v-model="data[index].qualification"
                :disabled="viewOnly" class="col-md-6" :multiple="false" :options="qualifications.qualifications" :hide-selected="false" name="qualification" :label="item" placeholder="Seleccione la calificación"
                :error="form.errorsFor('activities.'+indexActivity+'.dangers.'+indexDanger+'.qualifications.'+index+'.qualification')" >
                    </vue-advanced-select>
        </b-form-row>
    </div>
</template>

<script>
import VueAdvancedSelect from "@/components/Inputs/VueAdvancedSelect.vue";

export default {
    components: {
        VueAdvancedSelect
    },
    props: {
        isEdit: { type: Boolean, default: false },
        viewOnly: { type: Boolean, default: false },
        form: { type: Object, required: true }, 
        indexActivity: { type: Number, required: true },
        indexDanger: { type: Number, required: true },
        qualifications: {
            type: [Array, Object],
            default: function() {
                return [];
            }
        },
        qualificationsData: { type: Object },
    },
    watch: {
        data: {
            handler(val){
                this.$emit('input', this.data)
            },
            deep: true
        }
    },
    data() {
        return {
            data: []
        }
    },
    created() {
        if (this.qualifications.types != undefined && this.qualifications.qualifications != undefined)
        {
            _.forIn(this.qualifications.types, (value, key) => {
                let qualification = ''

                if (this.qualificationsData && this.qualificationsData[value] != undefined)
                    qualification = this.qualificationsData[value].qualification

                this.data.push({
                    "type": value,
                    "qualification": qualification
                })
            });
        }
    },
    mounted() {
        
    }, 
    methods: {
    },
    computed: {
        info() {
            if (this.qualifications.help != undefined)
                return this.qualifications.help
        }
    }
};
</script>