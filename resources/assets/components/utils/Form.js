import FormErrors from './FormErrors.js';

/**
* this class handles everything that concerns
* about forms. Submit and errors and considered too
*/
export default class Form {

  /**
   * Set up the form class setting the data properties
   * directly to the form properties
   *
   * @param  {Object}  data
   * @param  {Boolean} clearAfterResponse
   * @return {void}
   */
  constructor(data, method = 'post', clearAfterResponse = false) {
    this.updateData(data);
    this.method = method;
    this.clearAfterResponse = clearAfterResponse;
    this.errors = new FormErrors();
  }

  static makeFrom(data, method = 'post', clearAfterResponse = false) {
    return new this(data, method, clearAfterResponse);
  }

  /**
   * Update de form data with the passed data
   *
   * @param  {Object} data
   * @return {void}
   */
  updateData(data) {
    this.originalData = data;
    Object.assign(this, data);
  }

  /**
   * Reset the form data
   *
   * @return {void}
   */
  reset() {
    Object.assign(this, this.originalData);
    this.errors.clear();
  }

  /**
   * Return the form data
   *
   * @return {Object}
   */
  data() {
    let data = new FormData();

    for (let field in this.originalData) {
      if (Array.isArray(this[field])) {
        this[field].forEach(element => {
          if (Object.prototype.toString.call(element) === '[object Object]') {
            element = JSON.stringify(element);
          }
          data.append(`${field}[]`, element);
        });
      } else {
        data.append(field, this[field] == null ? '' : this[field]);
      }
    }

    if (this.method != 'post') {
      data.append('_method', this.method);
    }

    return data;
  }

  errorsFor(field) {
    return this.errors.get(field);
  }

  /**
   * Submit the form and return a promise
   *
   * @param  {string} url
   * @return {Promise}
   */
  submit(url) {
    return new Promise((resolve, reject) => {
      axios.post(url, this.data())
        .then(response => {
          this.formSubmitSucceded(response);
          resolve(response);
        })
        .catch(error => {
          $notify({
            group: 'app',
            title: 'Error',
            text: "dsfa"
          });
          this.formSubmitFailed(error);
          reject(error);
        });
    });
  }

  /**
   * Perform actions after submition succeded
   *
   * @param  {Object} response
   * @return {void}
   */
  formSubmitSucceded(response) {
    if (this.clearAfterResponse) {
      this.reset();
    }
  }

  /**
   * Perform actions after submition failed
   *
   * @param  {Object} errors
   * @return {void}
   */
  formSubmitFailed(errors) {
    if (errors.response.status == 422) {
      this.errors.record(errors.response.data.errors);
    }
  }
}