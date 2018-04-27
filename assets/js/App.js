import React from 'react';
import {render} from 'react-dom';

var $ = require('jquery');
require('bootstrap');
require('jquery-bootstrap-scrolling-tabs');
require("../img/logo.png");
require("../img/home page/oilChange.jpg");
require("../img/home page/engineRepair.jpg");
require("../img/home page/carCheck.jpg");
require("../img/home page/tireChange.jpg");
require("../img/home page/engine.jpg");
require("../img/home page/check.jpg");
require("../img/home page/company.jpg");

Window.prototype.showModal = function testFunction(id){
    $('#'+id).modal({backdrop: 'static', keyboard: false}).modal('toggle');
};

window.setTimeout(function () {
    $(".alert-custom").fadeTo(500, 0).slideUp(500, function () {
        $(this).remove();
    });
}, 5000);

$('.nav-tabs').scrollingTabs({
    bootstrapVersion: 4,
    cssClassLeftArrow: 'fas fa-caret-left',
    cssClassRightArrow: 'fas fa-caret-right',
    scrollToTabEdge: true,
    disableScrollArrowsOnFullyScrolled: true,
    enableSwiping: true
});

