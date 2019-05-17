<template>
    <div>
        Total: {{ total_standard }} | #Cumple: {{ total_c }} ({{ total_p_c }}%) | #No Cumple: {{ total_nc }} ({{ total_p_nc }}%) | #No Calificados: {{ total_sc }}
    </div>
</template>

<script>

export default {
    props: {
        items: {
			type: [Array, Object],
			default: function() {
				return []
			}
		},
    },
    watch: {
        items: {
            handler(val){
                this.calculateResumen()
            },
            deep: true
        }
    },
    data () {
        return {
            total_standard: 0,
            total_c: 0,
            total_nc: 0,
            total_sc: 0,
            total_p_c: 0,
            total_p_nc: 0
        }
    },
    created() {
        this.calculateResumen()
    },
    methods: {
        calculateResumen() {
            Object.assign(this.$data, this.$options.data.apply(this))

            this.items.forEach((item, index) => {

                this.total_standard++;
                
                if (item.qualification == 'C' || item.qualification == 'NA')
                {
                    this.total_c++;
                }
                else if (item.qualification == 'NC')
                {
                    this.total_nc++;
                }
                else
                {
                    this.total_nc++;
                    this.total_sc++;
                }
            });

            if (this.total_standard > 0)
            {
                this.total_p_c = Math.round((this.total_c / this.total_standard) * 100);
                this.total_p_nc = Math.round((this.total_nc / this.total_standard) * 100);
            }
        }
    },
}

</script>