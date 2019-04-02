<template>
    <div class="col-md-12">
        <blockquote class="blockquote text-center">
            <p class="mb-0">Tipos de calificación habilitadas para la evaluación</p>
        </blockquote>
        <b-form-feedback class="d-block" v-if="form.errorsFor(`types_rating`)" style="padding-bottom: 10px;">
            {{ form.errorsFor(`types_rating`) }}
        </b-form-feedback>
        <b-row>
            <b-col v-for="(type, index) in data" :key="index">
                <vue-checkbox-simple :disabled="viewOnly" class="col-md-12" :label="type.name" v-model="data[index].apply" :checked="data[index].apply" name="evaluation_type_rating" checked-value="SI" unchecked-value="NO" @input="updateValue"></vue-checkbox-simple>
            </b-col>
        </b-row>
    </div>
</template>

<script>

import VueCheckboxSimple from "@/components/Inputs/VueCheckboxSimple.vue";

export default {
    name: 'evaluation-types-rating',
    components:{
        VueCheckboxSimple
    },
    props: {
        value: {type: [Array]},
        form: { type: Object, required: true },
        isEdit: { type: Boolean, default: false },
        viewOnly: { type: Boolean, default: false },
        typesRating: {
            type: Array,
            default: function() {
                return [];
            }
        },
    },
    data () { 
        return {
            data: []
        }
    },
    watch: {
        typesRating() {
            _.forIn(this.typesRating, (value, key) => {
                this.data.push({
                    id: value.id,
                    type_rating_id: value.id,
                    name: value.name,
                    apply: 'NO'
                })
            });

            this.updateValue()
        }
    },
    mounted() {
        setTimeout(() => {
            this.data.map((f) => {
                f.apply = _.find(this.value, { type_rating_id: f.type_rating_id }) ? 'SI' : 'NO'
            });
            
        }, 3000)
    },
    methods: {
        updateValue() {
            let value =  this.data.filter((f) => {
                return f.apply == 'SI';
            });

            this.$emit('input', value);
        }
    }
}
</script>