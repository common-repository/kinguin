<?php

namespace ILKinguinVendor;

require_once __DIR__ . '/vendor/autoload.php';
if (!\class_exists('ILKinguinVendor\\WPDesk\\Notice\\AjaxHandler')) {
    require_once __DIR__ . '/src/WPDesk/Notice/AjaxHandler.php';
}
if (!\class_exists('ILKinguinVendor\\WPDesk\\Notice\\Notice')) {
    require_once __DIR__ . 'src/WPDesk/Notice/Notice.php';
}
if (!\class_exists('ILKinguinVendor\\WPDesk\\Notice\\PermanentDismissibleNotice')) {
    require_once __DIR__ . '/src/WPDesk/Notice/PermanentDismissibleNotice.php';
}
if (!\class_exists('ILKinguinVendor\\WPDesk\\Notice\\Factory')) {
    require_once __DIR__ . '/src/WPDesk/Notice/Factory.php';
}
require_once __DIR__ . '/src/WPDesk/notice-functions.php';
