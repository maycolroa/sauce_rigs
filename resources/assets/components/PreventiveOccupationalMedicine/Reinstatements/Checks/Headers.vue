<template>
    <div style= "margin-bottom: 20px;">
        <h4><b>Resumen</b></h4>
        <table class="table table-bordered table-sm table-striped table-hover" style="width: 100%; font-size: 10px;">
            <thead>
                <tr>
                    <th v-for="(header, index) in headers" :key="`th-${index}`" class="text-center align-middle">
                        {{ header }}
                    </th>
                </tr>
            </thead>
             <tbody>
                <tr v-for="(parameter, key) in data" :key="key">
                    <td style='text-center align-middle'>
                        {{ parameter.label }}
                    </td>
                    <td style= 'text-center align-middle'>
                        {{ parameter.type == 'percentage' ? `${parameter.value}%` : parameter.value }}
                    </td>
                </tr>
            </tbody>
        </table>
       <!-- <template v-for="(item_row, key_row) in data_row">
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
        </template>-->
    </div>

</template>

<script>
export default {
    props: {
        headers: {
            type: Array,
            default () {
                return [
                    'Descripción',
                    'Total'
                ];
            }
        },
        data: {
            type: Object,
            default() {
                return {};
            }
        }
    },
    /*data() {
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
    }*/
}
</script>