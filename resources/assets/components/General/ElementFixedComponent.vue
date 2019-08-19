<template>
    <div class="fixedFooter" ref="myFooter">
        <slot></slot>
    </div>
</template>

<style>

.fixedFooter {
    padding: 10px;
    background: #5a5e65;
    color: #fff;
    border: 2px solid #aaa5a6;
    border-radius: 8px;
    margin-bottom: 5px;
}

.sticky {
    position: fixed;
    bottom: 0;
    right: 0;
    z-index: 1000;
    padding: 10px;
    border: 2px solid #aaa5a6;
    border-radius: 8px 0px 0px 0px;
}

</style>

<script>

export default {
    methods: {
        handleScroll: function (event) {
            
            let header = this.$refs.myFooter
            let sticky = this.coordenadas.y

            if (window.pageYOffset > sticky) {
                header.classList.add("sticky");
            } else {
                header.classList.remove("sticky");
            }
        }
    },
    data() {
		return {
			coordenadas: ''
		};
	},
    created: function () {
        window.addEventListener('scroll', this.handleScroll);
    },
    mounted: function () {
        this.coordenadas = this.$refs.myFooter.getBoundingClientRect()
    },
    destroyed: function () {
        window.removeEventListener('scroll', this.handleScroll);
    }
}

</script>