<template>
    <div>
        <b-row align-h="end">
            <b-col cols="3" v-for="(item, index) in data" :key="index">
            <b-card class="mb-4">
                <center>
                <div style="font-size: 12px;" class="my-2 mx-2 text-center text-dark pt-1">
                    {{ item.label }}
                </div>
                <footer style="font-size: 12px;">{{ item.value }}</footer>
                </center>
            </b-card>
            </b-col>
        </b-row>
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
    }
}
</script>