<template>
  <div>
    <header-module
      title="REINCORPORACIONES"
      subtitle="VER REPORTE"
      url="reinstatements-checks"
    />

    <div class="col-md">
      <b-card no-body>
        <b-card-body>
          <loading :display="!ready"/>
            <div v-if="ready">
              <template v-if="form == 'default'">
                <form-check
                  url="/biologicalmonitoring/reinstatements/check"
                  method="POST"
                  :check="data"
                  :view-only="true"
                  :disease-origins="diseaseOrigins"
                  :lateralities="lateralities"
                  :si-no="siNo"
                  :origin-advisors="originAdvisors"
                  :medical-conclusions="medicalConclusions"
                  :labor-conclusions="laborConclusions"
                  :origin-emitters="originEmitters"
                  tracing-others-url="/biologicalmonitoring/reinstatements/check/tracingOthers"
                  :cancel-url="{ name: 'reinstatements-checks'}"/>
              </template>
              <template v-if="form == 'hptu'">
                <form-check-hptu
                  url="/biologicalmonitoring/reinstatements/check"
                  method="POST"
                  :check="data"
                  :view-only="true"
                  :disease-origins="diseaseOrigins"
                  :lateralities="lateralities"
                  :si-no="siNo"
                  :origin-advisors="originAdvisors"
                  :medical-conclusions="medicalConclusions"
                  :labor-conclusions="laborConclusions"
                  :origin-emitters="originEmitters"
                  :type-qualification-controversy="typeQualificationControversy"
                  tracing-others-url="/biologicalmonitoring/reinstatements/check/tracingOthers"
                  :cancel-url="{ name: 'reinstatements-checks'}"/>
              </template>
              <template v-if="form == 'vivaAir'">
                <form-check-viva-air
                  url="/biologicalmonitoring/reinstatements/check"
                  method="POST"
                  :check="data"
                  :view-only="true"
                  :disease-origins="diseaseOrigins"
                  :lateralities="lateralities"
                  :si-no="siNo"
                  :origin-advisors="originAdvisors"
                  :medical-conclusions="medicalConclusions"
                  :labor-conclusions="laborConclusions"
                  :origin-emitters="originEmitters"
                  :sve-associated="sveAssociated"
                  :medical-certificate-ueac="medicalCertificateUeac"
                  :relocated-types="relocatedTypes"
                  tracing-others-url="/biologicalmonitoring/reinstatements/check/tracingOthers"
                  :cancel-url="{ name: 'reinstatements-checks'}"/>
              </template>
              <template v-if="form == 'misionEmpresarial'">
                <form-check-empresarial
                  url="/biologicalmonitoring/reinstatements/check"
                  method="POST"
                  :check="data"
                  :view-only="true"
                  :disease-origins="diseaseOrigins"
                  :lateralities="lateralities"
                  :si-no="siNo"
                  :origin-advisors="originAdvisors"
                  :medical-conclusions="medicalConclusions"
                  :labor-conclusions="laborConclusions"
                  :origin-emitters="originEmitters"
                  :sve-associated="sveAssociated"
                  :medical-certificate-ueac="medicalCertificateUeac"
                  :relocated-types="relocatedTypes"
                  :eps-favorability-concept="epsFavorabilityConcept"
                  :case-classification="caseClassification"
                  tracing-others-url="/biologicalmonitoring/reinstatements/check/tracingOthers"
                  :cancel-url="{ name: 'reinstatements-checks'}"/>
              </template>
            </div>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>
 
<script>
import FormCheck from '@/components/PreventiveOccupationalMedicine/Reinstatements/Checks/FormCheckComponent.vue';
import FormCheckHptu from '@/components/PreventiveOccupationalMedicine/Reinstatements/Checks/FormCheckHptuComponent.vue';
import FormCheckVivaAir from '@/components/PreventiveOccupationalMedicine/Reinstatements/Checks/FormCheckVivaAirComponent.vue';
import FormCheckEmpresarial from '@/components/PreventiveOccupationalMedicine/Reinstatements/Checks/FormCheckEmpresarialComponent.vue';
import Loading from "@/components/Inputs/Loading.vue";
import Alerts from '@/utils/Alerts.js';
import GlobalMethods from '@/utils/GlobalMethods.js';

export default {
  name: 'reinstatements-checks-edit',
  metaInfo: {
    title: 'Reportes - Ver'
  },
  components:{
    FormCheck,
    FormCheckHptu,
    FormCheckVivaAir,
    FormCheckEmpresarial,
    Loading
  },
  data () {
    return {
      data: [],
      ready: false,
      form: '',
      diseaseOrigins: [],
      lateralities: [],
      siNo: [],
      originAdvisors: [],
      medicalConclusions: [],
      laborConclusions: [],
      originEmitters: [],
      sveAssociated: [],
      medicalCertificateUeac: [],
      relocatedTypes: [],
      epsFavorabilityConcept: [],
      caseClassification: [],
      typeQualificationControversy: []
    }
  },
  created(){
    axios.post(`/configurableForm/formModel`, {key: 'form_check'})
		.then(response => {
      this.form = response.data;
      
      if (this.form == 'vivaAir')
      {
        this.fetchOptions('sveAssociated', 'reinc_select_sve_associated')
        this.fetchOptions('medicalCertificateUeac', 'reinc_select_medical_certificate_ueac')
        this.fetchOptions('relocatedTypes', 'reinc_select_relocated_types')
      }

      if (this.form == 'misionEmpresarial')
      {
        this.fetchOptions('epsFavorabilityConcept', 'reinc_select_eps_favorability_concept')
        this.fetchOptions('caseClassification', 'reinc_select_case_classification')
      }

      if (this.form == 'hptu')
      {
        this.fetchOptions('typeQualificationControversy', 'reinc_select_type_qualification_controversy')
      }
      
      axios.get(`/biologicalmonitoring/reinstatements/check/${this.$route.params.id}`)
      .then(response2 => {
          this.data = response2.data.data;
          this.ready = true
      })
      .catch(error => {
          Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
          this.$router.go(-1);
      });
		})
		.catch(error => {
			Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
			this.$router.go(-1);
		});

    this.fetchOptions('diseaseOrigins', 'reinc_select_disease_origin')
    this.fetchOptions('lateralities', 'reinc_select_lateralities')
    this.fetchOptions('originAdvisors', 'reinc_select_origin_advisors')
    this.fetchOptions('medicalConclusions', 'reinc_select_medical_conclusions')
    this.fetchOptions('laborConclusions', 'reinc_select_labor_conclusions')
    this.fetchOptions('originEmitters', 'reinc_select_emitter_origin')
    this.fetchSelect('siNo', '/radios/siNo')
  },
  methods: {
		fetchSelect(key, url)
		{
			GlobalMethods.getDataMultiselect(url)
			.then(response => {
				this[key] = response;
			})
			.catch(error => {
				Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
				this.$router.go(-1);
			});
		},
    fetchOptions(key, search)
    {
      axios.post(`/configurableForm/selectOptions`, {key: search})
        .then(response => {
          this[key] = response.data;
        })
        .catch(error => {
          Alerts.error('Error', 'Se ha generado un error en el proceso, por favor contacte con el administrador');
          this.$router.go(-1);
        });
    }
	}
}
</script>