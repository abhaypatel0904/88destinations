<?php
/**
 * @class    Helper
 * @category Class
 **/
class Helper
{
    public static $_instance = null;
    public static function get_instance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function __construct()
    {

    }

    // load any template in specific folder
    public function get_template($template_name, $template_path = '', $args = array(), $return = false)
    {

        $located = $this->locate_template($template_name, $template_path);

        if ($args && is_array($args)) {
            extract($args);
        }

        if ($return) {
            ob_start();
        }

        // include file located
        if (file_exists($located)) {
            include $located;
        }

        if ($return) {
            return ob_get_clean();
        }
    }
    // find location for template
    public function locate_template($template_name, $template_path)
    {
        $template = __DIR__ . '/' . $template_path . '/templates/' . $template_name;
        return $template;
    }

}
function helper_object()
{
    return Helper::get_instance();
}
helper_object();
