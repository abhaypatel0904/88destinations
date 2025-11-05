<?php
/**
 * @class    Included_files
 * @category Class
 * */
class Included_files {
    public function __construct() {
        require_once 'helper.php';
        require_once 'admin/class-custom-post-types.php';
        require_once 'admin/class-custom-taxonomy.php';
        require_once 'shortcodes/class-shortcodes.php';
    }
}
new Included_files();
