<template>
      <div>
        <b-card bg-variant="transparent" border-variant="dark" title="Información General" class="mb-3 box-shadow-none">
            <b-row>
                <b-col>
                    <div><b>Nit:</b> {{contractor.nit}}</div>
                    <div><b>Dirección:</b> {{contractor.address}}</div>
                    <div><b>Teléfono:</b> {{contractor.phone}}</div>
                    <div><b>N° de personas que laboran en la empresa:</b> {{contractor.number_workers}}</div>
                    <div><b>Nombre del coordinador de gestión ambiental:</b> {{contractor.environmental_management_name}}</div>
                </b-col>
                <b-col>
                    <div><b>Nombre del representante legal:</b> {{contractor.legal_representative_name}}</div>
                    <div><b>Nombre del coordinador de SST:</b> {{contractor.SG_SST_name}}</div>
                    <div><b>Nombre del Coordinador de gestión humana:</b> {{contractor.human_management_coordinator}}</div>
                    <div><b>Actividad económica general de la empresa:</b> {{contractor.economic_activity_of_company}}</div>
                    <div><b>Clase de riesgo:</b> {{contractor.risk_class}}</div>
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
            classification: '',
            type: '',
            business_name: '',
            phone: '',
            address: '',
            legal_representative_name: '',
            environmental_management_name: '',
            economic_activity_of_company: '',
            arl: '',
            SG_SST_name: '',
            risk_class: '',
            number_workers: '',
            high_risk_work: '',
            social_reason: ''
        };
      }
    }
  }
}
</script>
