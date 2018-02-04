require('../css/app.scss');
// loads the jquery package from node_modules
var $ = require('jquery');
require('bootstrap-sass');


const Tooltip = require('./tooltip.js');
const Form = require('./form.js');
const Bulk = require('./bulk.js');


$(document).ready(function() {
    Tooltip.initialize();
    Bulk.initialize();
    Form.initialize();
});
