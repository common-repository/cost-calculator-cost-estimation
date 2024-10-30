<?php
class Calculation_Froms_Settings_Analytics {
  function __construct(){
        add_filter("calculation_forms_settings_menu",array($this,"add_menu_settings"));
        add_action("calculation_forms_settings_render_analytics",array($this,"settings_page_render"));
        add_action( 'admin_init', array($this,'register_settings') );
        add_action("wp_head",array($this,"add_head"));
        add_action('calculation_form_render_form_js', array($this,'add_lib'));
    }
    function add_lib(){
        $code = get_option("calculation_forms_settings_analytics_code","");
        if($code != ""){
            wp_enqueue_script("calculation-forms-google-analytics",CALCULATION_FORMS_PLUGIN_URL ."modules/analytics/analytics.js",array("jquery"));
        }
    }
    function add_head(){
        $add = get_option("calculation_forms_settings_analytics","yes");
        $code = get_option("calculation_forms_settings_analytics_code","");
        if($add == "yes" && $code !="" ){
            ?>
            <!-- Google Analytics -->
            <script>
            (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
            (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
            m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
            })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
             
            ga('create', '<?php echo esc_attr($code) ?>', 'auto');
            ga('send', 'pageview');
            </script>
            <!-- End Google Analytics -->
            <?php
        }
    }
     function add_menu_settings($datas){
        $datas["analytics"] = array("label"=>__("Google Analytics","calculation-forms"));
        return $datas;
    }
    function register_settings(){
        register_setting( 'calculation_forms_settings_analytics', 'calculation_forms_settings_analytics' );
        register_setting( 'calculation_forms_settings_analytics', 'calculation_forms_settings_analytics_code' );
    }
    function settings_page_render(){
        $datas = get_option("calculation_forms_settings_analytics","yes");
        $code = get_option("calculation_forms_settings_analytics_code","");
        ?>
        <div class="calculation_forms_settings_title"><?php esc_html_e("Google Analytics Settings","calculation-forms") ?></div>
        <form method="post" action="options.php">
            <?php settings_fields( 'calculation_forms_settings_analytics' ); ?>
            <?php do_settings_sections( 'calculation_forms_settings_analytics' ); ?>
            <table class="form-table">
              <tr valign="top">
                    <th scope="row"><?php esc_html_e("Google Analytics library JS:","calculation-forms") ?> </th>
                    <td>
                        <input <?php checked($datas,"yes") ?> name="calculation_forms_settings_analytics" type="checkbox" value="yes">
                    </td>
               </tr>
               <tr valign="top">
                    <th scope="row"><?php esc_html_e("Tracking Code:","calculation-forms") ?> </th>
                    <td>
                        <input name="calculation_forms_settings_analytics_code" type="text" value="<?php echo esc_attr($code) ?>" class="regular-text">
                    </td>
               </tr>
            </table>
            <?php submit_button(); ?>
        </form>
        <?php
    }
}
new Calculation_Froms_Settings_Analytics;