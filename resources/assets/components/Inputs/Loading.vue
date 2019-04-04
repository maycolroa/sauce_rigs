<template>
    <div>
        <div class="row" v-if="display">
            <div class="col">
                <div class="sk-circle sk-primary">
                    <div class="sk-circle1 sk-child"></div>
                    <div class="sk-circle2 sk-child"></div>
                    <div class="sk-circle3 sk-child"></div>
                    <div class="sk-circle4 sk-child"></div>
                    <div class="sk-circle5 sk-child"></div>
                    <div class="sk-circle6 sk-child"></div>
                    <div class="sk-circle7 sk-child"></div>
                    <div class="sk-circle8 sk-child"></div>
                    <div class="sk-circle9 sk-child"></div>
                    <div class="sk-circle10 sk-child"></div>
                    <div class="sk-circle11 sk-child"></div>
                    <div class="sk-circle12 sk-child"></div>
                </div>
            </div>
        </div>
    </div>
</template>

<style src="@/vendor/libs/spinkit/spinkit.scss" lang="scss"></style>

<script>

export default {
    props: {
        display: { type: Boolean, default: false }
    }
};
</script>