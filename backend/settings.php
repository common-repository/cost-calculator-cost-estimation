<?php
class Calculation_Froms_Setiings
{
    public function __construct(){
        add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
    }
    function add_plugin_page(){
		add_submenu_page('edit.php?post_type=calculation_forms',esc_html__("Settings","calculation-forms") , esc_html__("Settings","calculation-forms"), 'manage_options','calculation-forms-settings', array($this,'settings_page')  );
		add_submenu_page('edit.php?post_type=calculation_forms',esc_html__("Add-ons","calculation-forms"), esc_html__("Add-ons","calculation-forms"), 'manage_options','calculation-forms-add-ons', array($this,'settings_page')  );
		add_action( 'admin_init', array($this,'register_settings') );
		add_action("calculation_forms_settings_render_settings",array($this,"settings_page_render"));
	}
	function register_settings(){
		register_setting( 'calculation_forms_settings', 'calculation_forms_settings' );
	}
	function settings_page_render(){
		$datas = get_option("calculation_forms_settings",array("currencies","USD","paypal_client"=>"","paypal_secret_key"=>"","recaptcha_site_key"=>"","recaptcha_secret_key"=>"","paypal_sandbox"=>"ok"));
		?>
		<div class="calculation_forms_settings_title"><?php esc_html_e("General Settings","calculation-forms") ?></div>
		<form method="post" action="options.php">
					    <?php settings_fields( 'calculation_forms_settings' ); ?>
					    <?php do_settings_sections( 'calculation_forms_settings' ); ?>
					    <table class="form-table">
					      <tr valign="top">
						        <th scope="row"><?php esc_html_e("Currency","calculation-forms") ?> </th>
						        <td>
						        	<div class="wp-core-ui">
						        		<select name="calculation_forms_settings[currencies]">
						                    <option value="USD"><?php esc_html_e("U.S. Dollar","calculation-forms") ?></option>
						                    <option <?php selected( $datas["currencies"], "AUD") ?> value="AUD"><?php esc_html_e("Australian Dollar","calculation-forms") ?></option>
						                    <option <?php selected( $datas["currencies"], "BRL") ?> value="BRL"><?php esc_html_e("Brazilian Real","calculation-forms") ?></option>
						                    <option <?php selected( $datas["currencies"], "CAD") ?> value="CAD"><?php esc_html_e("Canadian Dollar","calculation-forms") ?></option>
						                    <option <?php selected( $datas["currencies"], "CZK") ?> value="CZK"><?php esc_html_e("Czech Koruna","calculation-forms") ?></option>
						                    <option <?php selected( $datas["currencies"], "DKK") ?> value="DKK"><?php esc_html_e("Danish Krone","calculation-forms") ?></option>
						                    <option <?php selected( $datas["currencies"], "EUR") ?> value="EUR"><?php esc_html_e("Euro","calculation-forms") ?></option>
						                    <option <?php selected( $datas["currencies"], "HKD") ?> value="HKD"><?php esc_html_e("Hong Kong Dollar","calculation-forms") ?></option>
						                    <option <?php selected( $datas["currencies"], "HUF") ?> value="HUF"><?php esc_html_e("Hungarian Forint","calculation-forms") ?></option>
						                    <option <?php selected( $datas["currencies"], "ILS") ?> value="ILS"><?php esc_html_e("Israeli New Sheqel","calculation-forms") ?></option>
						                    <option <?php selected( $datas["currencies"], "JPY") ?> value="JPY"><?php esc_html_e("Japanese Yen","calculation-forms") ?></option>
						                    <option <?php selected( $datas["currencies"], "MYR") ?> value="MYR"><?php esc_html_e("Malaysian Ringgit","calculation-forms") ?></option>
						                    <option <?php selected( $datas["currencies"], "MXN") ?> value="MXN"><?php esc_html_e("Mexican Peso","calculation-forms") ?></option>
						                    <option <?php selected( $datas["currencies"], "NOK") ?> value="NOK"><?php esc_html_e("Norwegian Krone","calculation-forms") ?></option>
						                    <option <?php selected( $datas["currencies"], "NZD") ?> value="NZD"><?php esc_html_e("New Zealand Dollar","calculation-forms") ?></option>
						                    <option <?php selected( $datas["currencies"], "PHP") ?> value="PHP"><?php esc_html_e("Philippine Peso","calculation-forms") ?></option>
						                    <option <?php selected( $datas["currencies"], "PLN") ?> value="PLN"><?php esc_html_e("Polish Zloty","calculation-forms") ?></option>
						                    <option <?php selected( $datas["currencies"], "GBP") ?> value="GBP"><?php esc_html_e("Pound Sterling","calculation-forms") ?></option>
						                    <option <?php selected( $datas["currencies"], "RUB") ?> value="RUB"><?php esc_html_e("Russian Ruble","calculation-forms") ?></option>
						                    <option <?php selected( $datas["currencies"], "SGD") ?> value="SGD"><?php esc_html_e("Singapore Dollar","calculation-forms") ?></option>
						                    <option <?php selected( $datas["currencies"], "SEK") ?> value="SEK"><?php esc_html_e("Swedish Krona","calculation-forms") ?></option>
						                    <option <?php selected( $datas["currencies"], "CHF") ?> value="CHF"><?php esc_html_e("ASwiss Franc","calculation-forms") ?></option>
						                    <option <?php selected( $datas["currencies"], "TWD") ?> value="TWD"><?php esc_html_e("Taiwan New Dollar","calculation-forms") ?></option>
						                    <option <?php selected( $datas["currencies"], "THB") ?> value="THB"><?php esc_html_e("Thai Baht","calculation-forms") ?></option>
						                    <option <?php selected( $datas["currencies"], "TRY") ?> value="TRY"><?php esc_html_e("Turkish Lira","calculation-forms") ?></option>
						        		</select>
						        	</div>
						        </td>
					        </tr>
					    </table>
					    <?php do_action("calculation_forms_settings_payment",$datas) ?>
					    <h3><?php esc_html_e("reCaptcha Settings (v3)","calculation-forms") ?></h3>
					    <table class="form-table">
					      <tr valign="top">
						        <th scope="row"><?php esc_html_e("reCAPTCHA Site Key","calculation-forms") ?> </th>
						        <td>
						        	<input name="calculation_forms_settings[recaptcha_site_key]" type="text" value="<?php echo esc_attr($datas["recaptcha_site_key"]) ?>" class="regular-text">
						        </td>
					       </tr>
					       <tr valign="top">
						        <th scope="row"><?php esc_html_e("reCAPTCHA Secret Key","calculation-forms") ?> </th>
						        <td>
						        	<input name="calculation_forms_settings[recaptcha_secret_key]" type="text" value="<?php echo esc_attr($datas["recaptcha_secret_key"]) ?>" class="regular-text">
						        </td>
					       </tr>
					    </table>
					    <?php do_action("calculation_forms_settings",$datas) ?>
					    <?php submit_button(); ?>
					</form>
					<?php
	}
	function settings_page(){
		$tab_view = "settings";
		?>
		<div class="wrap">
		<h1 class="text-center"><?php esc_html_e("Calculation Forms Settings","calculation-forms") ?></h1>
		<div class="calculation-forms-container-gird">
			<div class="calculation-forms-container-gird-nav">
				<ul>
				<?php 
					$tabs = array("settings"=>array("label"=>__("Settings","calculation-forms") )); 
					$tabs = apply_filters("calculation_forms_settings_menu",$tabs); 
					foreach($tabs as $key => $tab ){
						$link = add_query_arg(array("page"=>"calculation-forms-settings","tab"=>$key),get_admin_url()."edit.php?post_type=calculation_forms");
						$class ="";
						if( isset($_GET["tab"])){
							$tab_view = sanitize_text_field($_GET['tab']);
						}
						if($tab_view == $key){
							$class ="active";
						}
						?>
						<li class=" <?php echo esc_attr($class) ?>"><a href="<?php echo esc_url($link) ?>"><?php echo esc_html($tab["label"]) ?></a></li>
						<?php
					}
				?>
				</ul>
			</div>
			<div class="calculation_forms_settings-container">
				 <div class="calculation_forms_settings-tab-settings">
				 	<?php
				 		do_action("calculation_forms_settings_render");
				 		do_action("calculation_forms_settings_render_".$tab_view);
				 	 ?>
				 </div>
			</div>
		</div>
	</div>
		<?php
	}
}
new Calculation_Froms_Setiings;