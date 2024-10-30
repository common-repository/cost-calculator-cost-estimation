<?php
use Dompdf\Dompdf; 
use Dompdf\Options;
class Calculation_Froms_Settings_PDF {
    function __construct(){
        add_action('add_meta_boxes', array( $this, 'add_meta_boxes' ) );
        add_filter('calculation_form_mail_attachments', array( $this, 'render' ),10,4 );
        add_action("calculation_forms_tab_settings",array($this,"add_tab"));
        add_action("calculation_forms_tab_page",array($this,"add_page"));
        add_action("save_post",array($this,"save_post"));
        //add_action("calculation_form_submit_success",array($this,"remove_pdf"));
    }
    function remove_pdf(){
        $upload_dir = wp_upload_dir();
        $path_main = $upload_dir['basedir'] . '/calculation-forms/pdf/*.pdf';  
        foreach (glob($path_main) as $filename){
            wp_delete_file($filename);
        }
    }
    function add_page($post){
        $post_id = $post->ID;
        ?>
        <div class="calculation-forms-content-tab calculation-forms-content-tab-style calculation-forms-content-tab-pdf hidden">
            <h3><?php esc_html_e("PDF","calculation-forms") ?></h3>
            <?php 
            $datas = get_post_meta( $post_id, '_calculation_forms_pdf', true );
            if (  empty( $datas ) ) {
                $datas = array("filename"=>"","template"=>"default","notification"=>"","size"=>"A4",
                    "orientation"=>"portrait","font"=>"Helvetica","font_size"=>10,"font_color"=>"#000000","rtl"=>"","dpi"=>"96");
            }
            ?>
            <ul>
                <li>
                    <label for=""><?php esc_html_e("Filename (required)","calculation-forms") ?></label>
                    <?php 
                    Calculation_Froms_Backend::load_text_name("calculation_forms_pdf[filename]", $datas["filename"] );
                    ?>
                </li>
                <li>
                    <label for=""><?php esc_html_e("Template","calculation-forms") ?></label>
                    <select class="calculation_forms_pdf[template]">
                        <option><?php esc_html_e("Default","calculation-forms") ?></option>
                    </select>
                </li>
                <li>
                    <label for=""><?php esc_html_e("Notifications","calculation-forms") ?></label>
                    <p><?php esc_html_e("Send the PDF as an email attachment for the selected notification(s)","calculation-forms") ?></p>
                    <input <?php checked(@$datas["notification"],"yes") ?> value="yes" type="checkbox" name="calculation_forms_pdf[notification]">
                </li>
            </ul>
            <ul>
                <li>
                    <label><?php esc_html_e("Paper Size","calculation-forms") ?></label>
                    <p><?php esc_html_e("Set the paper size used when generating PDFs.","calculation-forms") ?></p>
                    <?php 
                    $sizes = array("A Sizes"=>array(
                        "A0"=> "A0 (841 x 1189mm)",
                        "A1"=> "A1 (594 x 841mm)",
                        "A2"=> "A2 (420 x 594mm)",
                        "A3"=> "A3 (297 x 420mm)",
                        "A4"=> "A4 (210 x 297mm)",
                        "A5"=> "A5 (148 x 210mm)",
                        "A6"=> "A6 (105 x 148mm)",
                        "A7"=> "A7 (74 x 105mm)",
                        "A8"=> "A8 (52 x 74mm)",
                        "A9"=> "A9 (37 x 52mm)",
                        "A10"=> "A10 (26 x 37mm)",
                    ),
                    "B sizes" => array(
                        "B0"=> "B0 (1414 x 1000mm)",
                        "B1"=> "B1 (1000 x 707mm)",
                        "B2"=> "B2 (707 x 500mm)",
                        "B3"=> "B3 (500 x 353mm)",
                        "B4"=> "B4 (353 x 250mm)",
                        "B5"=> "B5 (250 x 176mm)",
                        "B6"=> "B6 (176 x 125mm)",
                        "B7"=> "B7 (125 x 88mm)",
                        "B8"=> "B8 (88 x 62mm)",
                        "B9"=> "B9 (62 x 44mm)",
                        "B10"=> "B10 (44 x 31mm)",
                    )
                )
                    ?>
                    <select  name="calculation_forms_pdf[size]" >
                     <?php 
                        foreach($sizes as $group=>$options){
                            printf('<optgroup label="%s">',$group);
                                foreach($options as $key=>$value){
                                    $check ="";
                                    if( $datas["size"] == $key ){
                                        $check ="selected";
                                    }
                                    printf('<option %1$s value="%2$s">%3$s</option>',$check,$key,$value);
                                }
                            printf('</optgroup>');
                        }
                     ?>
                </select>
                </li>
                <li>
                    <label><?php esc_html_e("Paper Orientation","calculation-forms") ?></label>
                    <select name="calculation_forms_pdf[orientation]">
                        <option value="portrait"><?php esc_html_e("Portrait","calculation-forms") ?></option>
                        <option <?php selected($datas["orientation"],"landscape") ?> value="landscape"><?php esc_html_e("Landscape","calculation-forms") ?></option>
                    </select>
                </li>
                <li>
                    <label><?php esc_html_e("Font","calculation-forms") ?></label>
                    <select name="calculation_forms_pdf[font]">
                        <option value="Helvetica">Helvetica</option>
                        <option <?php selected($datas["font"],"Times-Roman") ?> value="Times-Roman">Times-Roman</option>
                        <option <?php selected($datas["font"],"Courier") ?> value="Courier">Courier</option>
                        <option <?php selected($datas["font"],"Zapf-Dingbats") ?> value="Zapf-Dingbats">Zapf-Dingbats</option>
                        <option <?php selected($datas["font"],"DejaVuSans") ?> value="DejaVuSans">DejaVuSans</option>
                    </select>
                </li>
                <li class="hidden">
                    <label><?php esc_html_e("Font Size","calculation-forms") ?></label>
                    <input type="number" name="calculation_forms_pdf[font_size]" value="<?php echo esc_attr($datas["font_size"]) ?>"> PT
                </li>
                <li class="hidden">
                    <label><?php esc_html_e("Font Color","calculation-forms") ?></label>
                    <input type="color" name="calculation_forms_pdf[font_color]" value="<?php echo esc_attr($datas["font_color"]) ?>">
                </li>
                <li class="hidden">
                    <label><?php esc_html_e("Reverse Text (RTL)","calculation-forms") ?></label>
                    <input <?php checked(@$datas["rtl"],"yes") ?> value="yes" type="checkbox" name="calculation_forms_pdf[rtl]">
                </li>
                <li>
                    <label><?php esc_html_e("DPI","calculation-forms") ?></label>
                    <p><?php esc_html_e("Control the image DPI (dots per inch) in PDFs. Set to 300 when professionally printing document.","calculation-forms") ?></p>
                    <input type="number" name="calculation_forms_pdf[dpi]" value="<?php echo esc_attr($datas["dpi"]) ?>">
                </li>
            </ul>
           </div>
        <?php
    }
    function save_post($post_id){
         /*
         * We need to verify this came from the our screen and with proper authorization,
         * because save_post can be triggered at other times.
         */
        // Check if our nonce is set.
        if ( ! isset( $_POST['calculation_forms_box_nonce'] ) ) {
            return $post_id;
        }
        $nonce = $_POST['calculation_forms_box_nonce'];
        // Verify that the nonce is valid.
        if ( ! wp_verify_nonce( $nonce, 'calculation_forms_box_nonce' ) ) {
            return $post_id;
        }
        /*
         * If this is an autosave, our form has not been submitted,
         * so we don't want to do anything.
         */
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return $post_id;
        }
        // Check the user's permissions.
        if ( 'page' == $_POST['post_type'] ) {
            if ( ! current_user_can( 'edit_page', $post_id ) ) {
                return $post_id;
            }
        } else {
            if ( ! current_user_can( 'edit_post', $post_id ) ) {
                return $post_id;
            }
        }
        /* OK, it's safe for us to save the data now. */
        // Sanitize the user input.
        $datas = array();
        if( isset( $_POST['calculation_forms_pdf'] ) && is_array($_POST['calculation_forms_pdf'])){
            foreach( $_POST['calculation_forms_pdf'] as $key => $value ) { 
                $datas[$key] = sanitize_text_field($value); 
            }
        }
        update_post_meta( $post_id, '_calculation_forms_pdf', $datas );
    }
    function add_tab(){
        ?>
        <li>
            <a data-tab=".calculation-forms-content-tab-pdf" href="#"><?php esc_html_e("PDF","calculation-forms") ?></a>
        </li>
        <?php
    }
    function add_meta_boxes() {
        add_meta_box(
            'cf_pdf',
            esc_html__( 'PDF', 'calculation-forms' ),
            array( $this, 'form' ),
            'cf_submissions',
            'side',
            'default'
        );
    }
    function form(){
        ?>
        <a class="button button-primary calculation-forms-download-pdf" href="#"><?php esc_html_e("Download PDF","calculation-forms") ?></a>
        <?php
    }
    function render($mail_attachments,$datas,$form,$form_id){
        $settings  = get_post_meta( $form_id, '_calculation_forms_pdf', true );
        if( isset($settings["filename"]) && isset($settings["notification"]) ){
            //var_dump($settings);
            $options = new Options();
            if( isset($settings["font"])){
                 $options->set('defaultFont', $settings["font"]);
            }
            $dompdf = new Dompdf($options);
            $dompdf = new Dompdf();
            $dompdf->loadHtml( $this->template($datas,$form,$form_id,$settings));
             if( isset($settings["zise"])){
                 $dompdf->setPaper($settings["zise"], $settings["orientation"]);
            }
            $dompdf->render();
            $output = $dompdf->output();
            $upload_dir = wp_upload_dir();
            $path_main = $upload_dir['basedir'] . '/calculation-forms/pdf/';  
            if (!is_dir($path_main)) {
              wp_mkdir_p($path_main);
            }
            $filename = uniqid() . 'form.pdf';
            if( isset($settings["filename"])){
                 if( isset($datas[$settings["filename"]]) ){
                    $filename = sanitize_title($datas[$settings["filename"]]).".pdf";
                 }else{
                    $filename = sanitize_title($settings["filename"]).".pdf";
                 }
            }
            $path = $path_main . $filename;
            file_put_contents($path, $output);
            $mail_attachments[] = $path;
        }
        return $mail_attachments;
    }
    function template($datas,$form,$form_id,$settings){
        ob_start();
        $form_name = get_the_title($form_id);
        $date =$date = date('Y-m-d');
        if( isset($settings["rtl"])){
            $dir="rtl"; 
        }else{
            $dir="ltr";
        }
        ?>
        <!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd"> 
<html dir="<?php echo esc_attr($dir) ?>"> 
<head> 
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<style type="text/css">
</style>
<title></title> 
</head> 
<body> 
        <div id="body">
    <div id="section_header"></div>
    <div id="content">
        <div class="page" style="font-size: 7pt;">
            <table style="width: 100%;" class="header">
                <tr>
                    <td><h1 style="text-align: left;"><?php echo utf8_decode($form_name) ?></h1></td>
                    <td><h1 style="text-align: right;">Created: <?php echo utf8_decode($date) ?></h1></td>
                </tr>
            </table>
            <ul style="list-style: none; padding: 0; margin:0">
                <?php foreach( $datas as $key=>$value ){
                    $label = Calculation_Forms_Process::get_data_element($key,"label",$form_id);
                    ?>
                    <li style="background: #e7e7e7; margin-bottom: 15px; border-radius: 5px; padding: 5px 15px;">
                        <label style="font-weight: 800;"><?php echo utf8_decode($label) ?></label>
                        <div style="margin-top: 5px;">
                            <?php 
                            if( is_array($value) ){
                                echo utf8_decode( implode("|",$value) );
                            }else{
                                echo utf8_decode($value);
                            }
                            ?>
                        </div>
                    </li>
                    <?php
                } ?>
            </ul>
            </table>
        </div>
    </div>
</div>
</body>
</html>
        <?php
        return ob_get_clean();
    }
}
new Calculation_Froms_Settings_PDF;