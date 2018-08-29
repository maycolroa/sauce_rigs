<template>
<div class="layout-wrapper layout-2">
    <div class="layout-inner">
      <sidenav/>

      <div class="layout-container">
        <Navbar/>

        <div class="layout-content">
          <div class="router-transitions container-fluid flex-grow-1 container-p-y">
            <router-view />
          </div>

      </div>
    </div>
    
  </div>
  <div class="layout-overlay" @click="closeSidenav"></div>
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

  mounted () {
    this.layoutHelpers.init()
    this.layoutHelpers.update()
    this.layoutHelpers.setAutoUpdate(true)
  },

  beforeDestroy () {
    this.layoutHelpers.destroy()
  },

  methods: {
    closeSidenav () {
      this.layoutHelpers.setCollapsed(true)
    }
  }
}
</script>
