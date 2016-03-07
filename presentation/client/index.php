<!DOCTYPE html>
<html data-ng-app="app" ng-strict-di>
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge, chrome=1" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no" />
    <title data-ng-bind="title"></title>
    <style>
        /* This helps the ng-show/ng-hide animations start at the right place. */
        /* Since Angular has this but needs to load, this gives us the class early. */
        .ng-hide {
            display: none!important;
        }
    </style>

    <!-- inject-vendor:css -->
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet" />
    <link href="/assets/css/font-awesome/css/font-awesome.min.css" rel="stylesheet" />
    <!-- <link href="/assets/css/toastr.css" rel="stylesheet" /> -->
    <!-- endinject -->

    <!-- inject:css -->
    <link href="/content/customtheme.css" rel="stylesheet">
    <link href="/content/styles.css" rel="stylesheet" />
    <!-- endinject -->

    <link rel="apple-touch-icon" sizes="57x57" href="/content/images/apple-icon-57x57.png">
	<link rel="apple-touch-icon" sizes="60x60" href="/content/images/apple-icon-60x60.png">
	<link rel="apple-touch-icon" sizes="72x72" href="/content/images/apple-icon-72x72.png">
	<link rel="apple-touch-icon" sizes="76x76" href="/content/images/apple-icon-76x76.png">
	<link rel="apple-touch-icon" sizes="114x114" href="/content/images/apple-icon-114x114.png">
	<link rel="apple-touch-icon" sizes="120x120" href="/content/images/apple-icon-120x120.png">
	<link rel="apple-touch-icon" sizes="144x144" href="/content/images/apple-icon-144x144.png">
	<link rel="apple-touch-icon" sizes="152x152" href="/content/images/apple-icon-152x152.png">
	<link rel="apple-touch-icon" sizes="180x180" href="/apple-icon-180x180.png">
	<link rel="icon" type="image/png" sizes="192x192"  href="/content/images/android-icon-192x192.png">
	<link rel="icon" type="image/png" sizes="32x32" href=/content/images"/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="96x96" href="/content/images/favicon-96x96.png">
	<link rel="icon" type="image/png" sizes="16x16" href="/content/images/favicon-16x16.png">
	<link rel="manifest" href="/content/images/manifest.json">
	<meta name="msapplication-TileColor" content="#ffffff">
	<meta name="msapplication-TileImage" content="/content/images/ms-icon-144x144.png">
	<meta name="theme-color" content="#ffffff">
</head>
<body>
    <div>
        <div data-ng-include="'app/layout/shell.html'"></div>
        <div id="splash-page" data-ng-show="false">
            <div class="page-splash">
                <div class="page-splash-message">
                    AngularUI AGENDA 2.
                </div>
                <div class="progress progress-striped active page-progress-bar">
                    <div class="bar"></div>
                </div>
            </div>
        </div>
    </div>

    <div>
        <div data-ng-include="'app/layout/shell.html'"></div>
        <div id="splash-page" data-ng-show="false">
            <div class="page-splash">
                <div class="page-splash-message">
                    ALT TITLU
                </div>
                <div class="progress progress-striped active page-progress-bar">
                    <div class="bar"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- inject-vendor:js -->
    <script src="/assets/js/jquery-1.12.0.min.js"></script>
    <script src="/assets/js/angular.js"></script>
    <script src="/assets/js/angular-animate.js"></script>
    <script src="/assets/js/angular-route.js"></script>
    <script src="/assets/js/angular-sanitize.js"></script>
    <script src="/assets/js/angular-pagination.js"></script>
    <script src="/assets/js/ui-bootstrap-tpls-1.1.2.min.js"></script>
    <script src="/assets/js/bootstrap.js"></script>
    <script src="/assets/js/toastr.js"></script>
    <script src="/assets/js/moment.js"></script>
    <script src="/assets/js/ngplus.js"></script>
    <!-- endinject -->


    <!-- inject:js -->
    <!-- Bootstrapping -->
    <script src="/app/app.module.js"></script>

    <!-- Reusable Blocks/Modules -->
    <script src="/app/blocks/exception/exception.module.js"></script>
    <script src="/app/blocks/exception/exception-handler.provider.js"></script>
    <script src="/app/blocks/exception/exception.js"></script>
    <script src="/app/blocks/logger/logger.module.js"></script>
    <script src="/app/blocks/logger/logger.js"></script>
    <script src="/app/blocks/router/router.module.js"></script>
    <script src="/app/blocks/router/routehelper.js"></script>

    <!-- core module -->
    <script src="/app/core/core.module.js"></script>
    <script src="/app/core/constants.js"></script>
    <script src="/app/core/dataservice.js"></script>
    <script src="/app/core/config.js"></script>


    <!-- layout -->
    <script src="/app/layout/layout.module.js"></script>
    <script src="/app/layout/shell.js"></script>
    <script src="/app/layout/sidebar.js"></script>

    <!--widgets-->
    <script src="/app/widgets/widgets.module.js"></script>
    <script src="/app/widgets/ccSidebar.js"></script>
    <script src="/app/widgets/ccSpinner.js"></script>
    <script src="/app/widgets/ccWidgetClose.js"></script>
    <script src="/app/widgets/ccWidgetHeader.js"></script>
    <script src="/app/widgets/ccWidgetMinimize.js"></script>

    <!-- dashboard -->
    <script src="/app/dashboard/dashboard.module.js"></script>
    <script src="/app/dashboard/config.route.js"></script>
    <script src="/app/dashboard/dashboard.js"></script>

    <!--agenda module-->
    <script src="/app/agenda/agenda.module.js"></script>
    <script src="/app/agenda/config.route.js"></script>
    <script src="/app/agenda/agenda.js"></script>
    <!-- endinject -->
    
    <!--contact module-->
    <script src="/app/contact/new-contact.module.js"></script>
    <script src="/app/contact/config.route.js"></script>
    <script src="/app/contact/new-contact.js"></script>
    <script src="/app/contact/edit-contact.module.js"></script>
    <script src="/app/contact/edit-contact.js"></script>
    <!-- endinject -->
</body>
</html>
