<template>
    <div>
        <div class="table-responsive" style="font-size: 10px;"  v-if="rows.length > 0">
            <table class="table table-bordered table-sm table-striped table-hover" style="width: 100%; margin-bottom: 0px">
                <thead>
                    <tr>
                        <th v-for="(header, index) in headers" :key="`th-${index}`" class="text-center align-middle">
                            {{ header }}
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(row, index) in rows" :key="`row-${index}`">
                        <td v-for="(col, index2) in row" :key="`col-${index2}`" :class="index2 != 0 ? 'text-center align-middle' : 'align-middle'">
                            {{ col }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
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
        rows: {
            type: Array,
            required: true
        }
    },
}
</script>