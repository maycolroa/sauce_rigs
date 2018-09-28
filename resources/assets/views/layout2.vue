<template>
<div>
  <sidenav :data="data" :appSelected="appSelected"/>
  <router-view />
</div>
</template>
<script>
import Navbar from '@/components/NavbarComponent.vue';
import Sidenav from '@/components/SidenavComponent.vue';
export default {
    components:{
        Navbar,
        Sidenav,
    },
    data(){
      return {
        data: {},
        appSelected: 'PreventiveOccupationalMedicine' //Valor temporal
      }
    },
    mounted () {
      this.layoutHelpers.init()
      this.layoutHelpers.update()
      this.layoutHelpers.setAutoUpdate(true)
      this.permits()
    },

    beforeDestroy () {
      this.layoutHelpers.destroy()
    },

    methods: {
      closeSidenav () {
        this.layoutHelpers.setCollapsed(true)
      },
      permits () {
          axios
            .get('/appWithModules')
            .then(response => {
                this.data = response.data;
            })
            .catch(error => {
                Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
            });
      },
    }
}
</script>
