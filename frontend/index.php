<?php 
class Calculation_Forms_Frontend {
	function __construct(){
		add_action('calculation_form_render_form_js', array($this,'add_lib'));
	}
	function add_lib() {
		$settings = get_option("calculation_forms_settings");
		wp_enqueue_style("calculation-forms",CALCULATION_FORMS_PLUGIN_URL ."frontend/css/calculation-forms.css",array());
		wp_register_script("math-expression-evaluator",CALCULATION_FORMS_PLUGIN_URL ."libs/formula_evaluator-min.js",array());
		wp_enqueue_script("calculation-forms",CALCULATION_FORMS_PLUGIN_URL ."frontend/js/calculation-forms.js",array("jquery","wp-i18n"));
		wp_enqueue_script("calculation-forms-formula",CALCULATION_FORMS_PLUGIN_URL ."frontend/js/formula.js",array("jquery","math-expression-evaluator"));
		wp_enqueue_script("calculation-forms-logic",CALCULATION_FORMS_PLUGIN_URL ."frontend/js/logic.js",array("jquery"));
		wp_enqueue_script("calculation-forms-multistep",CALCULATION_FORMS_PLUGIN_URL ."frontend/js/multi-step.js",array("jquery"));
		wp_enqueue_script("autonumeric",CALCULATION_FORMS_PLUGIN_URL ."libs/autonumeric/autoNumeric-1.9.45.js",array("jquery"));
		
		$recaptcha_site_key ="";
        if( isset($settings["recaptcha_site_key"]) && $settings["recaptcha_site_key"] !="" ){
        	$recaptcha_site_key = $settings["recaptcha_site_key"];
        	wp_enqueue_script( 'google-recaptcha',
				add_query_arg(
					array(
						'render' => $recaptcha_site_key,
					),
					'https://www.google.com/recaptcha/api.js'
				),
				array(),
				'3.0',
				true
			);
        }
        wp_localize_script( 'calculation-forms', 'calculations',
            array( 'ajax_url' => admin_url( 'admin-ajax.php' ),
            		'recaptcha_site_key' => $recaptcha_site_key
        ) );
	}
}
new Calculation_Forms_Frontend;