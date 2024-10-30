<?php
class Calculation_Froms_Import_Export_Form { 
	function __construct(){
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
	}
	function add_meta_boxes() {
        add_meta_box(
            'form-builder-import-export',
            esc_html__( 'Import/Export Form', 'calculation-forms' ),
            array( $this, 'form_main' ),
            'calculation_forms',
            'side',
            'high'
        );
    }
    function form_main($post){
    	?>
    	<a class="button button-large calculation-form-import" href="#"><?php esc_html_e("Import Form","calculation-forms") ?></a>
    	<a class="button button-large calculation-form-export" href="#"><?php esc_html_e("Export Form","calculation-forms") ?></a>
    	<?php
    }
}
new Calculation_Froms_Import_Export_Form;