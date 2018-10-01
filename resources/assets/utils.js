
/**
 * Get the companies with active license associated with the user
 */
//window.globalCompaniesUser = {}

const getCompaniesUser = (callback) => {
    axios
        .get('/getCompanies')
        .then(response => {
            callback(null, response.data);
        }).catch(error => {
            callback(error, error.response.data);
        });
};
  
getCompaniesUser((err, data) => {
    if (err) {
        console.log(err.toString())
    } else {
        window.globalCompaniesUser = data
    }
})
  
/****************************************************************************************/

/**
 * Get the applications and associated modules the user
 */
//window.globalAppModulesUser = {}

const getAppModulesUser = (callback) => {
    axios
        .get('/appWithModules')
        .then(response => {
            callback(null, response.data);
        }).catch(error => {
            callback(error, error.response.data);
        });
};
  
getAppModulesUser((err, data) => {
    if (err) {
        console.log(err.toString())
    } else {
        window.globalAppModulesUser = data
    }
})
  
/****************************************************************************************/