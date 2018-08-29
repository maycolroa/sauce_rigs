<template>
  <b-navbar toggleable="lg" :variant="getLayoutNavbarBg()" class="layout-navbar align-items-lg-center container-p-x">

    <b-navbar-brand to="/" class="app-brand demo d-lg-none py-0 mr-4">
      <span class="app-brand-logo logo bg-primary">
        <div class="ui-w-30 rounded-circle align-middle text-circle">S</div>
      </span>
      <span class="app-brand-text logo font-weight-normal ml-2">Medicina Laboral Preventiva</span>
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
          <i class="ion ion-ios-search navbar-icon align-middle"></i>
          <span class="navbar-search-input pl-2">
            <input type="text" class="form-control navbar-text mx-2" placeholder="Buscar..." style="width:200px">
          </span>
        </label>
      </b-navbar-nav>

      
        <!--Aplications-->
        <b-navbar-nav class="align-items-lg-center ml-auto">

        <b-nav-item-dropdown no-caret :right="!isRTL" class="navbar-application-sauce mr-lg-3">
          <template slot="button-content">
            <i class="ion ion-md-apps navbar-icon align-middle"></i>
            <span class="d-lg-none align-middle">&nbsp; Aplicaciones</span>
          </template>

          <b-row>
            <b-col class="my-2 mx-2">
              <a class="text-dark cursor-pointer">
              <img class="img-fluid" src="https://mail.google.com/mail/u/0/?ui=2&ik=ed8c0f24ac&view=fimg&th=1652485af9799abe&attid=0.7&disp=emb&realattid=ii_jko60h0b0_1652485854d8409d&attbid=ANGjdJ8_DDZubCBie19grd9iqOiH2JuLd2cHJzAqe6zbqwvQ9xNJgCtyRhN6HrzcwMDSNEOr3gELZlpW0aqyb01PvIDjZnqTIjnS4E2p8V5yr2W0EvJwfZ-AHCdlK1M&sz=w512-h512&ats=1533916068723&rm=1652485af9799abe&zw&atsh=1">
              <div class="text-center font-weight-bold pt-1">
                Administrativo
              </div>
              </a>
            </b-col>
            <b-col class="my-2 mx-2">
              <a class="text-dark cursor-pointer">
              <img class="img-fluid" src="https://mail.google.com/mail/u/0/?ui=2&ik=ed8c0f24ac&view=fimg&th=165247d87d90853b&attid=0.6&disp=emb&realattid=ii_jko5ovy88_165247d452ec7f40&attbid=ANGjdJ8z913YxV4EGRO3kAX77SINgNY2mJs6DClLdr0CB44CpijkpbJ5WSwbVVCFE8ETs70_gQGF59On-0djqA2_NLE2KwmSDfglC_wzKjiZdg1chKB6nuwA3qAMvHY&sz=w512-h512&ats=1533915719542&rm=165247d87d90853b&zw&atsh=1">
              <div class="text-center font-weight-bold pt-1">
                Seguridad industrial
              </div>
              </a>
            </b-col>
            <b-col class="my-2 mx-2">
              <a class="text-dark cursor-pointer">
              <img class="img-fluid" src="https://mail.google.com/mail/u/0/?ui=2&ik=ed8c0f24ac&view=fimg&th=165247d87d90853b&attid=0.2&disp=emb&realattid=ii_jko58zbz1_1652471f3b7ce8f5&attbid=ANGjdJ_wMnTvyX48K1CQttLDqZrKO8pNQlih-nQQPrGFuVcFYxxTaLMYDwSKPVvPkrgXyoajve6VZiKjPxTNfTa3PyCxFeHX9L5l9ITwVCTTctqjFXCAknxIKPsPMek&sz=w512-h512&ats=1533915719542&rm=165247d87d90853b&zw&atsh=1">
              <div class="text-center font-weight-bold pt-1">
                Higiene industrial
              </div>
              </a>
            </b-col>
          </b-row>
          <b-row>
            <b-col class="my-2 mx-2">
              <a class="text-dark cursor-pointer">
              <img class="img-fluid" src="https://mail.google.com/mail/u/0/?ui=2&ik=ed8c0f24ac&view=fimg&th=1652466ce68ab8af&attid=0.1&disp=emb&realattid=ii_jko4t4dm0_1652466a947a5bc8&attbid=ANGjdJ8PnvxYmG8p0FZIkxp9zfhUkkqKyrDFMYt8_mcgzRJd6Vs5nSh1P_XaLHp_LaC4AKbifrpaTksVmjllqr4Y-zlQy3tiZJNpNlScoNIxSjcaL4V_JYXcyIWgArY&sz=w512-h512&ats=1533914048386&rm=1652466ce68ab8af&zw&atsh=1">
              <div class="text-center font-weight-bold pt-1">
                Medicina laboral  y preventiva
              </div>
              </a>
            </b-col>
            <b-col class="my-2 mx-2">
              <a class="text-dark cursor-pointer">
              <img class="img-fluid" src="https://mail.google.com/mail/u/0/?ui=2&ik=ed8c0f24ac&view=fimg&th=165247d87d90853b&attid=0.1&disp=emb&realattid=ii_jko5accn2_1652472ebdf00394&attbid=ANGjdJ_CwXUC9bs8NpCXsUVlVegoZzDVTfXR07WUfmyMIE1RmYXFWIbNeIOKBZyYmod3AFeroxIzWz8s3xkoMB10e_3yWR7oe2lwcmgwk3uvwC7j-46av15azbusp8Q&sz=w512-h512&ats=1533915719542&rm=165247d87d90853b&zw&atsh=1">
              <div class="text-center font-weight-bold pt-1">
                Formacion y capacitación
              </div>
              </a>
            </b-col>
            <b-col class="my-2 mx-2">
              <a class="text-dark cursor-pointer">
              <img class="img-fluid" src="https://mail.google.com/mail/u/0/?ui=2&ik=ed8c0f24ac&view=fimg&th=165247d87d90853b&attid=0.5&disp=emb&realattid=ii_jko5brko4_1652473ef4550f8f&attbid=ANGjdJ82ArJ0KapLViVSL-usQxDS7E7vEKEZqCCPiGySIq4H5EXLj9Iid-ykNIEK9X1F4ZXPyfqtaSKdRWhVJjMw14PK4hoKTONZtCN9B_n4n3kUom8D0vEh9HU5O-g&sz=w512-h512&ats=1533915719542&rm=165247d87d90853b&zw&atsh=1">
              <div class="text-center font-weight-bold pt-1">
                Medicion y seguimiento
              </div>
              </a>
            </b-col>
          </b-row>

          <b-row>
            <b-col class="my-2 mx-2">
              <a class="text-dark cursor-pointer">
              <img class="img-fluid" src="https://mail.google.com/mail/u/0/?ui=2&ik=ed8c0f24ac&view=fimg&th=165247d87d90853b&attid=0.3&disp=emb&realattid=ii_jko5h5sl5_1652477c5840998a&attbid=ANGjdJ_Ny0vbbcijX8GS91xJrZAiakZ7JM0U0AW16U90p8RFX7DXWZe95iryzd7Qurd973Ib4t29P557t6-3zqqoC-o5FrzemAk40f0naA15ep6KVw5xihffV1GvvNU&sz=w512-h512&ats=1533915719542&rm=165247d87d90853b&zw&atsh=1">
              <div class="text-center font-weight-bold pt-1">
                Aspectos Legales
              </div>
              </a>
            </b-col>
            <b-col class="my-2 mx-2"></b-col>
            <b-col class="my-2 mx-2"></b-col>
          </b-row>

        </b-nav-item-dropdown>

        <!-- Divider -->
        <div class="nav-item d-none d-lg-block text-big font-weight-light line-height-1 opacity-25 mr-3 ml-1">|</div>


      <!-- User -->
        <b-nav-item-dropdown :right="!isRTL" class="">
          <template slot="button-content">
            <span class="d-inline-flex flex-lg-row-reverse align-items-center align-middle">
              <span class="app-brand-logo logo bg-primary">
                <div class="ui-w-30 rounded-circle align-middle text-circle">S</div>
              </span>
            </span>
            
          </template>

          <b-dd-item><i class="ion ion-ios-person text-lightest"></i> &nbsp; Mi Perfil</b-dd-item>
          <b-dd-divider />
          <b-dd-item href="/logout"><i class="ion ion-ios-log-out text-danger"></i> &nbsp; Cerrar sesión</b-dd-item>
        </b-nav-item-dropdown>
      </b-navbar-nav>
    </b-collapse>
  </b-navbar>
</template>

<script>
export default {
  props: {
    sidenavToggle: {
      type: Boolean,
      default: true
    }
  },
  components: {
  
  },

  methods: {
    toggleSidenav () {
      this.layoutHelpers.toggleCollapsed()
    },

    getLayoutNavbarBg () {
      return this.layoutNavbarBg
    },
    logout(){
       axios.post('/logout')
        .then(response => {
          location.href = "/login";
        })
        .catch(error => {
          location.href = "/login";
        });
    }
  }
}
</script>
