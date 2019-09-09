<template>
    <div>
        <template v-for="(item_row, key_row) in data_row">
            <b-row :key="key_row">
                <b-col v-for="(parameter, key) in item_row" :key="key">
                    <b-card class="mb-4" border-variant="primary">
                        <center>
                            <div style="font-size: 12px;" class="my-2 mx-2 text-center text-dark pt-1">
                                {{ parameter.label }}
                            </div>
                            <footer style="font-size: 12px;">{{ parameter.type == 'percentage' ? `${parameter.value}%` : parameter.value }}</footer>
                        </center>
                    </b-card>
                </b-col>
            </b-row>
        </template>
    </div>

</template>

<script>
export default {
    props: {
        data: {
            type: Object,
            default() {
                return {};
            }
        }
    },
    data() {
        return {
        }
    },
    watch: {
        data() {
        }
    },
    computed: {
        data_row() {
            let aux = []
            let data_row = []

            for (var prop in this.data)
            {
                aux.push(this.data[prop]);

                if (aux.length == 4)
                {
                    data_row.push(aux)
                    aux = []
                }
            }

            if (aux.length > 0)
            {
                data_row.push(aux)
            }

            return data_row;
        }
    }
}
</script>