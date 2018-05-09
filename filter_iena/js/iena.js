;(function ($) {
    $('iframe').load(function () {
        $('iframe').contents().find("body")
            .append($("<style type='text/css'>  \n\
#page-header{display:none;}\n\
#page-footer{display:none;}  \n\
.hidden-print{display:none;}\n\
#page{margin-top: 15px;padding-left: 10px;padding-right: 15px;} \n\
#card-block{height: 837px;width: 990px;}  \n\
 </style>"));
        $('iframe').contents().find("header")
            .append($("<style type='text/css'>  \n\
.pos-f-t{display:none;} \n\
.navbar{display:none;} \n\
.navbar-full{display:none;} \n\
.navbar-light{display:none;} \n\
.bg-faded{display:none;} \n\
.navbar-static-top{display:none;} \n\
.moodle-has-zindex{display:none;}\n\
 </style>"));
    });
})(jQuery);