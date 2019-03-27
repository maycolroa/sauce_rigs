<template>
      <div>
        <b-card bg-variant="transparent" border-variant="dark" title="Información General" class="mb-3 box-shadow-none">
            <b-row>
                <b-col>
                    <div><b>Nit:</b> {{contractor.nit}}</div>
                    <div><b>Dirección:</b> {{contractor.address}}</div>
                    <div><b>Teléfono:</b> {{contractor.phone}}</div>
                    <div><b>N° de Personas que laboran en el HPTU:</b> {{contractor.number_workers}}</div>
                </b-col>
                <b-col>
                    <div><b>Nombre del Coordinador de Gestión Ambiental:</b> {{contractor.legal_representative_name}}</div>
                    <div><b>Nombre del Coordinador de SST:</b> {{contractor.SG_SST_name}}</div>
                    <div><b>Actividad economica general de la empresa:</b> {{contractor.social_reason}}</div>
                    <div><b>Clase de riesgo:</b> {{contractor.high_risk_work}}</div>
                </b-col>
            </b-row>
        </b-card>
      </div>
</template>


<script>

export default {
  props:{
    contractor: {
      default() {
        return {
            nit: '',
            type: '',
            business_name: '',
            phone: '',
            address: '',
            legal_representative_name: '',
            SG_SST_name: '',
            number_workers: '',
            high_risk_work: '',
            social_reason: ''
        };
      }
    }
  }
}
</script>
