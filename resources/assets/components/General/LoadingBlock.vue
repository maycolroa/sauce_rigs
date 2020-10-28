<template>
    <b-card>
        <BlockUI :message="message" :html="html"/>
    </b-card>
</template>

<style src="@/vendor/libs/spinkit/spinkit.scss" lang="scss"></style>

<script>
export default {
    props: {
        text: { 
            type: String,
            default: 'Cargando...'
        },
        message: {
            type: String,
            default: ''
        }
    },
    computed: {
        html() {
            return `<div class="sk-folding-cube sk-primary"><div class="sk-cube1 sk-cube"></div><div class="sk-cube2 sk-cube"></div><div class="sk-cube4 sk-cube"></div><div class="sk-cube3 sk-cube"></div></div><h5 class="text-center mb-0">${this.text}</h5>`
        } 
    }
}
</script>