<?php
class Calculation_Froms_upgrade{ 
	private $link_pro = "https://codecanyon.net/item/calculator-forms-builder/34591796";
	function __construct(){
		if(!CALCULATION_FORMS_PREMIUM){
			add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
		}
		add_filter( 'plugin_action_links_cost-calculator-cost-estimation/calculation-forms.php' , array( $this, 'add_action' ) );
		add_filter( 'plugin_action_links_calculation-forms/calculation-forms.php' , array( $this, 'add_action' ) );
	}
	function add_action($links){
		$link_pro = $this->link_pro;
		$settings_link = get_admin_url()."edit.php?post_type=calculation_forms&page=calculation-forms-settings";
		$document = array(
			'<a href="'.esc_url($settings_link) .'">'.esc_html__("Settings","calculation-forms").'</a>',
		);
		$links = array_merge($document,$links);
		$mylinks = array(
				'<a href="'.esc_url("https://dev.wall-f.com/calculator/?page_id=10") .'">'.esc_html__("Document","calculation-forms").'</a>',
		    );
		if( !CALCULATION_FORMS_PREMIUM) {
			$mylinks[] = '<a style="color: #43B854; font-weight: bold" target="_blank" href="'.esc_url($link_pro) .'">'.esc_html__("Go Pro Version","calculation-forms")."</a>";
		}
	    return array_merge( $links, $mylinks );
	}
	function add_meta_boxes() {
        add_meta_box(
            'form-builder-upgrade',
            esc_html__( 'Upgrade Pro Version', 'calculation-forms' ),
            array( $this, 'form_main' ),
            'calculation_forms',
            'side',
            'core'
        );
    }
    function form_main($post){
    	$link_pro = $this->link_pro;
    	?>
    	<a target="_blank" href="<?php echo esc_url($link_pro) ?>">
    		<img src="<?php echo esc_url(CALCULATION_FORMS_PLUGIN_URL."backend/images/upgrade.png") ?>" alt="">
    	</a>
    	<?php
    }
}
new Calculation_Froms_upgrade;