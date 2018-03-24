import React from 'react';
import {render} from 'react-dom';

var $ = require('jquery');
require('bootstrap');
require("../img/logo.png");

Window.prototype.showModal = function testFunction(id){
    $('#'+id).modal({backdrop: 'static', keyboard: false});
};

