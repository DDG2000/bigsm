<?php
error_reporting(0);
include_once( '../include/VerifyCode/CreateVerifyCode.class.php' );


function w_log($str){
	$fp = fopen('log.txt', 'a+');
	fwrite($fp, "\r\n".date('Y-m-d H:i:s')."\t".$str);
	fclose($fp);
}

$img = new Securimage();
$img->image_width = 82;
$img->image_height = 30;
$img->draw_lines = false;
$img->font_size = 18;
$img->ttf_file = 'BodoniMTBold.TTF';
$img->draw_lines_over_text=false;
$img->draw_lines =false;
//$img->arc_linethrough=false;
$img->arc_line_colors="#a2a2ff";
//$img->line_distance = 20;
//$img->image_bg_color ="#f6f6f6";
$img->use_transparent_text = false;
//$img->text_transparency_percentage = 10;
$img->bgimg = "";
$img->show();
exit;
?>
