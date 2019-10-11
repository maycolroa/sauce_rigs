<template>
    <div class="row">
        <div class="col-md-8">
            <span class="title-module">{{ title }}</span><br>
            <span v-if="subtitle" class="title-module subtitle-module">{{ subtitle }}<br></span>
            <img class="filete-module" src="/images/Sauce-ML Filete.png">
        </div>
        <div class="col-md-4 text-right cursor-pointer" v-if="url">
            <router-link :to="{ name: url }" class="text-dark cursor-pointer item-app-navbar">
                Volver atrás <img class="ui-w-50" src="~@/images/Sauce-ML Boton Volver Atras.png">
            </router-link>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        title: { type: String, default: ''},
        subtitle: { type: String, default: '' },
        url: { type: String, default: '' }
    }
}
</script>