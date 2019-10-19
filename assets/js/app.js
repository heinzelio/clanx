// loads the jquery package from node_modules
// var $ = require('jquery');
require('bootstrap');

require('bootstrap/dist/css/bootstrap.css');
require('font-awesome/css/font-awesome.css');

require('../css/app.css');

const Tooltip = require('./tooltip.js');
const Form = require('./form.js');
const Bulk = require('./bulk.js');


$(document).ready(function() {
    Tooltip.initialize();
    Bulk.initialize();
    Form.initialize();
});
