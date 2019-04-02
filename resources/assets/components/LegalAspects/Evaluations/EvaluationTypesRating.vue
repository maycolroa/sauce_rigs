<template>
    <div class="col-md-12">
        <blockquote class="blockquote text-center">
            <p class="mb-0">Tipos de calificacón habilitadas para la evaluación</p>
        </blockquote>

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
        value: {type: [Array], default:[]},
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
    mounted() {
        setTimeout(() => {
            _.forIn(this.typesRating, (value, key) => {
                this.data.push({
                    id: value.id,
                    name: value.name,
                    text: value.name,
                    apply: 'NO'
                })
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