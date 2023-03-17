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
              <template>
                <form-check-hptu
                  url="/biologicalmonitoring/reinstatements/check"
                  method="POST"
                  :check="data"
                  :view-only="true"
                  :disease-origins="diseaseOrigins"
                  :lateralities="lateralities"
                  :si-no="siNo"
                  :origin-advisors="originAdvisors"
                  :refund-classification="refundClassification"
                  :medical-conclusions="medicalConclusions"
                  :labor-conclusions="laborConclusions"
                  :origin-emitters="originEmitters"
                  :type-qualification-controversy="typeQualificationControversy"
                  tracing-others-url="/biologicalmonitoring/reinstatements/check/tracingOthers"
                  :clasification-origin="clasificationOrigin"
                  :cancel-url="{ name: 'reinstatements-checks'}"/>
              </template>
            </div>
        </b-card-body>
      </b-card>
    </div>
  </div>
</template>
 
<script>
import FormCheckHptu from '@/components/PreventiveOccupationalMedicine/Reinstatements/Checks/FormCheckHptuVisorComponent.vue'; 
import Loading from "@/components/Inputs/Loading.vue";
import Alerts from '@/utils/Alerts.js';
import GlobalMethods from '@/utils/GlobalMethods.js';

export default {
  name: 'reinstatements-checks-edit',
  metaInfo: {
    title: 'Reportes - Ver'
  },
  components:{
    FormCheckHptu,
    Loading
  },
  data () {
    return {
      data: [],
      ready: false,
      form: '',
      diseaseOrigins: [],
      lateralities: [],
      qualificationsDME: [],
      siNo: [],
      originAdvisors: [],
      refundClassification: [],
      medicalConclusions: [],
      laborConclusions: [],
      originEmitters: [],
      sveAssociated: [],
      medicalCertificateUeac: [],
      relocatedTypes: [],
      epsFavorabilityConcept: [],
      caseClassification: [],
      typeQualificationControversy: [],
      clasificationOrigin: [],
      recommendationsValidity: []
    }
  },
  created(){
    axios.post(`/configurableForm/formModel`, {key: 'form_check'})
		.then(response => {
      this.form = response.data;

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
    this.fetchOptions('refundClassification', 'reinc_select_refund_classification')
    this.fetchOptions('medicalConclusions', 'reinc_select_medical_conclusions')
    this.fetchOptions('laborConclusions', 'reinc_select_labor_conclusions')
    this.fetchOptions('originEmitters', 'reinc_select_emitter_origin')
    this.fetchSelect('siNo', '/radios/siNo')
    this.fetchOptions('clasificationOrigin', 'reinc_select_qualifications_origin_controversy')
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