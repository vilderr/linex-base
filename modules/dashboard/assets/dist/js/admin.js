"use strict";

var Admin = function () {
};

Admin.prototype = {
    constructor: Admin
};

Admin.options = {
    navbarMenuSlimscroll: true,
    navbarMenuSlimscrollWidth: "3px",
    navbarMenuHeight: "200px",
    sidebar: true,
    sidebarToggleSelector: "[data-toggle='offcanvas']",
    enableControlTreeView: true,
    screenSizes: {
        xs: 480,
        sm: 768,
        md: 992,
        lg: 1200
    },
    animationSpeed: 500
};

Admin.layout = {
    activate: function () {
        var _this = this;

        _this.fix();
        $('body, html, .wrapper').css('height', 'auto');
        $(window, ".wrapper").resize(function () {
            _this.fix();
        });
    },
    fix: function () {
        var footer_height = $('.main-footer').outerHeight() || 0;
        var neg = $('.main-header').outerHeight() + footer_height;
        var window_height = $(window).height();
        var sidebar_height = $(".sidebar").height() || 0;

        if ($("body").hasClass("fixed")) {
            $(".content-wrapper").css('min-height', window_height - footer_height);
        } else {
            var postSetWidth;

            if (window_height >= sidebar_height) {
                $(".content-wrapper").css('min-height', window_height - neg);
                postSetWidth = window_height - neg;
            } else {
                $(".content-wrapper").css('min-height', sidebar_height);
                postSetWidth = sidebar_height;
            }
        }
    }
};

Admin.sidebar = {
    activate: function (toggleBtn) {
        var screenSizes = Admin.options.screenSizes;

        $(document).on('click', toggleBtn, function (e) {
            e.preventDefault();

            if ($(window).width() > (screenSizes.sm - 1)) {
                if ($("body").hasClass('sidebar-collapse')) {
                    $("body").removeClass('sidebar-collapse').trigger('expanded.pushMenu');
                } else {
                    $("body").addClass('sidebar-collapse').trigger('collapsed.pushMenu');
                }
            }
            else {
                if ($("body").hasClass('sidebar-open')) {
                    $("body").removeClass('sidebar-open').removeClass('sidebar-collapse').trigger('collapsed.pushMenu');
                } else {
                    $("body").addClass('sidebar-open').trigger('expanded.pushMenu');
                }
            }
        });

        $(".content-wrapper").click(function () {
            //Enable hide menu when clicking on the content-wrapper on small screens
            if ($(window).width() <= (screenSizes.sm - 1) && $("body").hasClass("sidebar-open")) {
                $("body").removeClass('sidebar-open');
            }
        });

        //Enable expand on hover for sidebar mini
        if ($('body').hasClass('fixed') && $('body').hasClass('sidebar-mini')) {
            this.expandOnHover();
        }
    },
    expandOnHover: function () {
        var _this = this;
        var screenWidth = Admin.options.screenSizes.sm - 1;
        //Expand sidebar on hover
        $('.main-sidebar').hover(function () {
            if ($('body').hasClass('sidebar-mini')
                && $("body").hasClass('sidebar-collapse')
                && $(window).width() > screenWidth) {
                _this.expand();
            }
        }, function () {
            if ($('body').hasClass('sidebar-mini')
                && $('body').hasClass('sidebar-expanded-on-hover')
                && $(window).width() > screenWidth) {
                _this.collapse();
            }
        });
    },
    expand: function () {
        $("body").removeClass('sidebar-collapse').addClass('sidebar-expanded-on-hover');
    },
    collapse: function () {
        if ($('body').hasClass('sidebar-expanded-on-hover')) {
            $('body').removeClass('sidebar-expanded-on-hover').addClass('sidebar-collapse');
        }
    }
};

Admin.tree = function (menu) {
    var _this = this;
    var animationSpeed = Admin.options.animationSpeed;

    $(document).off('click', menu + ' li a')
        .on('click', menu + ' li a', function (e) {
            //Get the clicked link and the next element
            var $this = $(this);
            var checkElement = $this.next();

            //Check if the next element is a menu and is visible
            if ((checkElement.is('.treeview-menu')) && (checkElement.is(':visible')) && (!$('body').hasClass('sidebar-collapse'))) {
                //Close the menu
                checkElement.slideUp(animationSpeed, function () {
                    checkElement.removeClass('menu-open');
                    //Fix the layout in case the sidebar stretches over the height of the window
                    //_this.layout.fix();
                });
                checkElement.parent("li").removeClass("active");
            }
            //If the menu is not visible
            else if ((checkElement.is('.treeview-menu')) && (!checkElement.is(':visible'))) {
                //Get the parent menu
                var parent = $this.parents('ul').first();
                //Close all open menus within the parent
                var ul = parent.find('ul:visible').slideUp(animationSpeed);
                //Remove the menu-open class from the parent
                ul.removeClass('menu-open');
                //Get the parent li
                var parent_li = $this.parent("li");

                //Open the target menu and add the menu-open class
                checkElement.slideDown(animationSpeed, function () {
                    //Add the class active to the parent li
                    checkElement.addClass('menu-open');
                    parent.find('li.active').removeClass('active');
                    parent_li.addClass('active');
                    //Fix the layout in case the sidebar stretches over the height of the window
                    _this.layout.fix();
                });
            }
            //if this isn't a link, prevent the page from being redirected
            if (checkElement.is('.treeview-menu')) {
                e.preventDefault();
            }
        });
};

Admin.makeSlug = function (selectorsFrom, selectorTo, callback) {
    var valueFrom = $(selectorsFrom).val();

    if (valueFrom.length) {
        $.ajax({
            'url': '/dashboard/make-slug',
            'type': 'GET',
            'cache': false,
            'dataType': 'json',
            'data': {
                'str': valueFrom
            }
        }).done(function (data) {
            var $field = $(selectorTo);

            if (typeof $field.attr('maxlength') !== typeof undefined) {
                data = data.substr(0, $field.attr('maxlength'));
            }

            $field.val(data);
        }).fail(function (jqXHR, textStatus) {
            //console.log(jqXHR.responseText);
        });
    }
};

$(function () {
    var o = Admin.options;

    Admin.layout.activate();

    if (o.navbarMenuSlimscroll && typeof $.fn.slimscroll != 'undefined') {
        $(".navbar .menu").slimscroll({
            height: o.navbarMenuHeight,
            alwaysVisible: false,
            size: o.navbarMenuSlimscrollWidth
        }).css("width", "100%");
    }

    if (o.sidebar) {
        Admin.sidebar.activate(o.sidebarToggleSelector);
    }

    //Enable sidebar tree view controls
    if (o.enableControlTreeView) {
        Admin.tree('.sidebar');
    }
});