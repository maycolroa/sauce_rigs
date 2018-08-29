<template>
  <sidenav :orientation="orientation" :class="curClasses">

    <!-- Brand logo -->
    <div class="app-brand logo" v-if="orientation !== 'horizontal'">
      <span class="app-brand-logo logo bg-primary">
        <div class="ui-w-30 rounded-circle align-middle text-circle">M</div>
      </span>
      <a href="/" class="app-brand-text logo sidenav-text font-weight-normal ml-2">Medicina Laboral Preventiva</a>
      <a href="javascript:void(0)" class="layout-sidenav-toggle sidenav-link text-large ml-auto" @click="toggleSidenav()">
        <i class="ion ion-md-menu align-middle"></i>
      </a>
    </div>
    <div class="sidenav-divider mt-0" v-if="orientation !== 'horizontal'"></div>

    <!-- Inner -->
    <div class="sidenav-inner" :class="{ 'py-1': orientation !== 'horizontal' }">
      <sidenav-router-link icon="fas fa-angle-right" to="/" :exact="true">Descripción Sociodemografica</sidenav-router-link>
      <sidenav-router-link icon="fas fa-angle-right" to="/" :exact="true">Sistemas de Vigilancia Epidemiológica</sidenav-router-link>
      <sidenav-router-link icon="fas fa-angle-right" to="/" :exact="true">Conceptos Medicos</sidenav-router-link>
      <sidenav-router-link icon="fas fa-angle-right" to="/" :exact="true">Restricciones y Recomendaciones</sidenav-router-link>
      <sidenav-menu icon="fas fa-angle-right" :open="true">
        <template slot="link-text">Monitoreo Biologico</template>
        <sidenav-router-link :to="{ name: 'audiometry'}" :exact="true">Audiometrias</sidenav-router-link>
        <sidenav-router-link to="/" :exact="true">Espirometrias</sidenav-router-link>
        <sidenav-router-link to="/" :exact="true">Otros Examenes</sidenav-router-link>
      </sidenav-menu>
      <sidenav-router-link icon="fas fa-angle-right" to="/" :exact="true">Ausentismo e Indicadores</sidenav-router-link>
      <sidenav-router-link icon="fas fa-angle-right" to="/" :exact="true">Profesiogramas</sidenav-router-link>
      <sidenav-router-link icon="fas fa-angle-right" to="/" :exact="true">Procedimientos y Programas</sidenav-router-link>
      <sidenav-router-link icon="fas fa-angle-right" to="/" :exact="true">SVE Psicosocial</sidenav-router-link>
      <sidenav-router-link icon="fas fa-angle-right" to="/" :exact="true">Analisís de puestos de Trabajo</sidenav-router-link>
    </div>
  </sidenav>
</template>

<script>
import { Sidenav, SidenavRouterLink, SidenavMenu, SidenavHeader, SidenavBlock, SidenavDivider } from '@/vendor/libs/sidenav'

export default {
  components: {
    Sidenav,
    SidenavRouterLink,
    SidenavMenu,
    SidenavHeader,
    SidenavBlock,
    SidenavDivider
  },

  props: {
    orientation: {
      type: String,
      default: 'vertical'
    }
  },

  computed: {
    curClasses () {
      let bg = this.layoutSidenavBg

      if (this.orientation === 'horizontal' && (bg.indexOf(' sidenav-dark') !== -1 || bg.indexOf(' sidenav-light') !== -1)) {
        bg = bg
          .replace(' sidenav-dark', '')
          .replace(' sidenav-light', '')
          .replace('-darker', '')
          .replace('-dark', '')
      }

      return `bg-${bg} ` + (
        this.orientation !== 'horizontal'
          ? 'layout-sidenav'
          : 'layout-sidenav-horizontal container-p-x flex-grow-0'
      )
    }
  },

  methods: {
    isMenuActive (url) {
      return this.$route.path.indexOf(url) === 0
    },

    isMenuOpen (url) {
      return this.$route.path.indexOf(url) === 0 && this.orientation !== 'horizontal'
    },

    toggleSidenav () {
      this.layoutHelpers.toggleCollapsed()
    }
  }
}
</script>
