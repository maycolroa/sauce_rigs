<template>
<div class="layout-wrapper layout-2">
    <div class="layout-inner">
      <sidenav :data="data" :appSelected="appSelected"/>

      <div class="layout-container">
        <Navbar :data="data" :appSelected="appSelected" @changeApp="changeModules($event)"/>

        <div class="layout-content">
          <div class="router-transitions container-fluid flex-grow-1 container-p-y">
            <router-view />
          </div>

      </div>
    </div>
    
  </div>
  <div class="layout-overlay" @click="closeSidenav"></div>
  <notifications group="app"/>
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
      changeModules(event)
      {
        this.appSelected = event
      }
    }
}
</script>
