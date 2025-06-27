<template>
  <b-navbar toggleable="lg" :variant="getLayoutNavbarBg()" class="layout-navbar align-items-lg-center container-p-x" style="left: 0px;">

    <b-navbar-brand :to="{ name: routeAppName}" class="app-brand demo d-lg-none py-0 mr-4">
      <img class="ui-w-30 rounded-circle align-middle text-circle" src="~@/images/Sauce-MLMesa de trabajo 73@2x.png" alt="Kitten">
      <span class="app-brand-text logo font-weight-normal ml-2"> {{ appName }} </span>
    </b-navbar-brand>

    <!-- Sidenav toggle-->
    <b-navbar-nav class="layout-sidenav-toggle d-lg-none align-items-lg-center mr-auto" v-if="sidenavToggle">
      <a class="nav-item nav-link px-0 mr-lg-4" href="javascript:void(0)" @click="toggleSidenav">
        <i class="ion ion-md-menu text-large align-middle" />
      </a>
    </b-navbar-nav>

    <!-- Navbar toggle -->
    <b-navbar-toggle target="app-layout-navbar"></b-navbar-toggle>

  <b-collapse is-nav id="app-layout-navbar">
      <!-- Divider -->
      <hr class="d-lg-none w-100 my-2">

      <b-navbar-nav class="align-items-lg-center">
        <!-- Search -->
        <label class="nav-item navbar-text navbar-search-box p-0 active">
          <i class="ion navbar-icon align-middle"></i>
          <span class="navbar-search-input pl-2">
            <input type="text" readonly class="form-control navbar-text mx-2" placeholder="" style="width:200px">
          </span>
        </label>
      </b-navbar-nav>

      <b-navbar-nav class="align-items-lg-center ml-auto">

        <label class="nav-item navbar-text navbar-search-box p-0 active" style="cursor: pointer;" @click="returnContratante" title="Ingresar como contratante"
        v-if=" (auth.hasRole['Contratista'] || auth.hasRole['Arrendatario']) && auth.isContratante">
          <div class="media-body line-height-condenced ml-3">
            <div class="text-dark">
              <span class="d-inline-flex flex-lg-row-reverse align-items-center align-middle">
                <i class="fas fa-random navbar-icon align-middle"></i>
              </span>
            </div>
          </div>
        </label>

        <label class="nav-item navbar-text navbar-search-box p-0 active">
          <div class="media-body line-height-condenced ml-3">
            <div class="text-dark">{{ auth.user_auth.email }}</div>
          </div>
        </label>
        
        <label class="nav-item navbar-text navbar-search-box p-0 active">
          <div class="media-body line-height-condenced ml-3">
            <div class="text-dark">{{ companyName }}</div>
          </div>
        </label>

        <b-nav-item-dropdown no-caret :right="!isRTL" class="demo-navbar-notifications mr-lg-3"
            v-if="Object.keys(company.data).length > 1 /*&& (!auth.hasRole['Contratista'] && !auth.hasRole['Arrendatario'])*/">
          <template slot="button-content">
            <i class="fas fa-sync navbar-icon align-middle"></i>
            <span class="d-lg-none align-middle">&nbsp; </span>
          </template>

          <b-list-group-item class="media d-flex align-items-center" style="min-height: 40px;">
            <div class="media-body line-height-condenced ml-3">
              <div class="text-dark">
                <b-input 
                  placeholder="Buscar..." 
                  type="text"
                  autocomplete="off"
                  v-model="searchCompany"
                  />
                </div>
            </div>
          </b-list-group-item>

          <b-list-group flush style="max-height: 300px; overflow-y: scroll;">
            <template v-for="(item, index) in companiesData">
              <b-list-group-item href="javascript:void(0)" class="media d-flex align-items-center" style="min-height: 40px;"
                 :key="index" v-if="item.id != company.selected && showItem(item.name)" @click="changeCompany(item.id)">
                <div class="ui-icon ui-icon-sm ion bg-primary border-0 text-white"> {{ item.name.substr(0,1).toUpperCase() }} </div>
                <div class="media-body line-height-condenced ml-3">
                  <div class="text-dark">{{ item.name }}</div>
                </div>
              </b-list-group-item>
            </template>
          </b-list-group>
        </b-nav-item-dropdown>

        <!-- Divider -->
        <div class="nav-item d-none d-lg-block text-big font-weight-light line-height-1 opacity-25 mr-3 ml-1">|</div>

        <label class="nav-item navbar-text navbar-search-box p-0 active">
          <div class="media-body line-height-condenced ml-3">
            <div class="text-dark">{{ contractName }}</div>
          </div>
        </label>

        <b-nav-item-dropdown no-caret :right="!isRTL" class="demo-navbar-notifications mr-lg-3"
            v-if="Object.keys(contract.data).length > 1">
          <template slot="button-content">
            <i class="fas fa-sync navbar-icon align-middle"></i>
            <span class="d-lg-none align-middle">&nbsp; </span>
          </template>

          <b-list-group-item class="media d-flex align-items-center" style="min-height: 40px;">
            <div class="media-body line-height-condenced ml-3">
              <div class="text-dark">
                <b-input 
                  placeholder="Buscar..." 
                  type="text"
                  autocomplete="off"
                  v-model="searchContract"
                  />
                </div>
            </div>
          </b-list-group-item>

          <b-list-group flush style="max-height: 300px; overflow-y: scroll;">
            <template v-for="(item, index) in contractData">
              <b-list-group-item href="javascript:void(0)" class="media d-flex align-items-center" style="min-height: 40px;"
                 :key="index" v-if="item.id != contract.selected && showItemContract(item.name)" @click="changeContract(item.id)">
                <div class="ui-icon ui-icon-sm ion bg-primary border-0 text-white"> {{ item.name.substr(0,1).toUpperCase() }} </div>
                <div class="media-body line-height-condenced ml-3">
                  <div class="text-dark">{{ item.name }}</div>
                </div>
              </b-list-group-item>
            </template>
          </b-list-group>
        </b-nav-item-dropdown>

        <b-nav-item-dropdown no-caret :right="!isRTL" class="demo-navbar-notifications mr-lg-3"
            v-if="Object.keys(contractMultilogin.data).length > 1" title="Ingresar como contratista">
          <template slot="button-content">
            <i class="fas fa-random navbar-icon align-middle"></i>
            <span class="d-lg-none align-middle">&nbsp; </span>
          </template>

          <b-list-group-item class="media d-flex align-items-center" style="min-height: 40px;">
            <div class="media-body line-height-condenced ml-3">
              <div class="text-dark">
                <b-input 
                  placeholder="Buscar..." 
                  type="text"
                  autocomplete="off"
                  v-model="searchContractMultilogin"
                  />
                </div>
            </div>
          </b-list-group-item>

          <b-list-group flush style="max-height: 300px; overflow-y: scroll;">
            <template v-for="(item, index) in contractMultiloginData">
              <b-list-group-item href="javascript:void(0)" class="media d-flex align-items-center" style="min-height: 40px;"
                 :key="index" v-if="showItemContractMultilogin(item.name)" @click="changeContractMultilogin(item.id)">
                <div class="ui-icon ui-icon-sm ion bg-primary border-0 text-white"> {{ item.name.substr(0,1).toUpperCase() }} </div>
                <div class="media-body line-height-condenced ml-3">
                  <div class="text-dark">{{ item.name }}</div>
                </div>
              </b-list-group-item>
            </template>
          </b-list-group>
        </b-nav-item-dropdown>

        <!-- Divider -->
        <div class="nav-item d-none d-lg-block text-big font-weight-light line-height-1 opacity-25 mr-3 ml-1">|</div>

        <!--Aplications-->

        <b-nav-item-dropdown no-caret :right="!isRTL" id="navbar-application-sauce" class="navbar-application-sauce mr-lg-3">
          <template slot="button-content">
            <i class="ion ion-md-apps navbar-icon align-middle"></i>
            <span class="d-lg-none align-middle">&nbsp; Aplicaciones</span>
          </template>

          <b-row>
            <template v-for="(item, index) in apps">
              <b-col :key="index" v-if="item.modules.length > 0">
                <router-link :to="{ name: index}" v-on:click.native="toggleApp(item.display_name)" class="text-dark cursor-pointer item-app-navbar">
                <div class="my-2 mx-2 text-center" :ref="`${item.image}`" @mouseover="changeClassImage(item.image, `${item.image}_hover`)">
                  <img class="ui-w-60" :src="`/images/${item.image}.png`" alt="">
                  <div class="text-center font-weight-bold pt-1">
                    {{ item.display_name }}
                  </div>
                </div>
                <div class="my-2 mx-2 text-center imgHidden" :ref="`${item.image}_hover`" @mouseleave="changeClassImage(`${item.image}_hover`, item.image)">
                  <img class="ui-w-60" :src="`/images/${item.image}_hover.png`" alt="">
                  <div class="text-center font-weight-bold pt-1" style="text-decoration: underline rgb(244, 75, 82); text-underline-position: under;">
                    {{ item.display_name }}
                  </div>
                </div>
                </router-link>
              </b-col>
            </template>
          </b-row>
        </b-nav-item-dropdown>

        <!-- Divider -->
        <div class="nav-item d-none d-lg-block text-big font-weight-light line-height-1 opacity-25 mr-3 ml-1">|</div>


      <!-- User -->
        <b-nav-item-dropdown :right="!isRTL" class="">
          <template slot="button-content">
            <span class="d-inline-flex flex-lg-row-reverse align-items-center align-middle">
              <img class="ui-w-30 rounded-circle align-middle text-circle" src="~@/images/Sauce-MLMesa de trabajo 73@2x.png" alt="Kitten">
            </span>
            
          </template>

          <!-- <b-dd-item><i class="ion ion-ios-person text-lightest"></i> &nbsp; Mi Perfil</b-dd-item>
          <b-dd-divider /> -->

          <b-dd-item :to="{ name: 'system-companies-edit-info', params : { id }}"><i class="ion ion-ios-copy text-danger"></i> &nbsp; Completar informacion de la compañia</b-dd-item>
          <b-dd-item :to="{ name: 'changepassword'}"><i class="ion ion-md-key text-danger"></i> &nbsp; Cambiar contraseña</b-dd-item>
          <b-dd-item :to="{ name: 'defaultmodule'}"><i class="ion ion-ios-star-outline text-danger"></i> &nbsp; Módulo favorito</b-dd-item>
          <b-dd-item :to="{ name: 'termsconditions'}"><i class="ion ion-md-list text-danger"></i> &nbsp; Términos y condiciones</b-dd-item>
          <b-dd-item :to="{ name: 'firm'}"><i class="ion ion-md-create text-danger"></i> &nbsp; Agregar Firma</b-dd-item>
          <b-dd-item href="/logout"><i class="ion ion-ios-log-out text-danger"></i> &nbsp; Cerrar sesión</b-dd-item>
        </b-nav-item-dropdown>
      </b-navbar-nav>
    </b-collapse>
  </b-navbar>
</template>

<style>
.imgHidden {
    display: none;
}
</style>

<script>
import Alerts from '@/utils/Alerts.js';
import VueAdvancedSelect from "@/components/Inputs/VueAdvancedSelect.vue";
import PerfectScrollbar from '@/vendor/libs/perfect-scrollbar/PerfectScrollbar'

export default {
  props: {
    sidenavToggle: {
      type: Boolean,
      default: true
    },
    data: {
      type: Object,
      required: true,
      default: {}
    }
  },
  components: {
    VueAdvancedSelect,
    PerfectScrollbar
  },
  data(){
      return {
        company: {
          selected: null,
          data: []
        },
        searchCompany: '',
        contract: {
          selected: null,
          data: []
        },
        contractMultilogin: {
          selected: null,
          data: []
        },
        searchContract: '',
        searchContractMultilogin: ''
      }
    },
  methods: {
    changeClassImage(image, imageHover) {
      this.$refs[image][0].classList.add("imgHidden");
      this.$refs[imageHover][0].classList.remove("imgHidden");
    },
    toggleSidenav() {
      this.layoutHelpers.toggleCollapsed();
    },

    getLayoutNavbarBg() {
      return this.layoutNavbarBg;
    },
    logout() {
      axios
        .post("/logout")
        .then(response => {
          location.href = "/login";
        })
        .catch(error => {
          location.href = "/login";
        });
    },
    companies () {
      axios
        .get('/getCompanies')
        .then(response => {
            this.company.selected = response.data.selected
            this.company.data = response.data.data
        })
        .catch(error => {
            Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
        });
    },
    getContract () {
      axios
        .get('/getContract')
        .then(response => {
            this.contract.selected = response.data.selected
            this.contract.data = response.data.data
        })
        .catch(error => {
            Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
        });
    },
    getContractsMultilogin () {
      axios
        .get('/getContractsMultilogin')
        .then(response => {
            this.contractMultilogin.selected = response.data.selected
            this.contractMultilogin.data = response.data.data
        })
        .catch(error => {
            Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
        });
    },
    changeCompany(company) {
      axios
        .post('/changeCompany', {
            company_id: company,
            currentPath: this.$route.path,
            currentName: this.$route.name
        })
        .then(response => {
            window.location = response.data
        })
        .catch(error => {
            Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
        });
    }, 
    changeContract(contract) {
      axios
        .post('/changeContract', {
            contract_id: contract,
            currentPath: this.$route.path,
            currentName: this.$route.name
        })
        .then(response => {
            window.location = response.data
        })
        .catch(error => {
            Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
        });
    }, 
    changeContractMultilogin(contract) {
      axios
        .post('/changeContractMultilogin', {
            contract_id: contract
        })
        .then(response => {
            window.location = "/"
        })
        .catch(error => {
            Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
        });
    }, 
    returnContratante() {
      axios
        .post('/returnContratante')
        .then(response => {
            window.location = "/"
        })
        .catch(error => {
            Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
        });
    },
    toggleApp(description) {
      document.getElementById('navbar-application-sauce__BV_button_').click()
      this.userActivity(description)
    },
    showItem(label) {
      if (this.searchCompany)
      {
          return label.toLowerCase().includes(this.searchCompany.toLowerCase())
      }
      else
          return true
    },
    showItemContract(label) {
      if (this.searchContract)
      {
          return label.toLowerCase().includes(this.searchContract.toLowerCase())
      }
      else
          return true
    },
    showItemContractMultilogin(label) {
      if (this.searchContractMultilogin)
      {
          return label.toLowerCase().includes(this.searchContractMultilogin.toLowerCase())
      }
      else
          return true
    }
  },
  created () {
    this.companies()
    this.getContract()

    if (auth.can['contracts_multilogin'])
      this.getContractsMultilogin()
  },
  computed: {
      apps: function () {
        return this.data;
      },
      appName: function () {
        return this.data[this.routeAppName] != undefined ? this.data[this.routeAppName].display_name : ''
      },
      companyName: function () {
        return this.company.data[this.company.selected] != undefined ? this.company.data[this.company.selected].name : ''
      },
      contractName: function () {
        return this.contract.data[this.contract.selected] != undefined ? this.contract.data[this.contract.selected].name : ''
      },
      id: function () {
        return this.company.data[this.company.selected] != undefined ? this.company.data[this.company.selected].id : ''
      },
      companiesData() {
        let data = [];

        if (this.company.selected) 
        {
          _.forIn(this.company.data, (value, key) => {
              data.push(value);
          })

          data.sort((a, b) => (a.name > b.name) ? 1 : -1)
        }

        return data;
      },
      contractData() {
        let data = [];

        if (this.contract.selected) 
        {
          _.forIn(this.contract.data, (value, key) => {
              data.push(value);
          })

          data.sort((a, b) => (a.name > b.name) ? 1 : -1)
        }

        return data;
      },
      contractMultiloginData() {
        let data = [];

        _.forIn(this.contractMultilogin.data, (value, key) => {
            data.push(value);
        })

        data.sort((a, b) => (a.name > b.name) ? 1 : -1)

        return data;
      }
  }
};
</script>
