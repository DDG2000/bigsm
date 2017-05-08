<?php
if(1){
/****************************************************************** 
**创建者：kign
**创建时间：2013-2-25
**本页： 系统运行检测 编辑
**说明：
******************************************************************/
require_once('../include/TopFile.php');

$Admin->CheckPopedoms('SC_SYS_TOOL_DETECT');

//use cache
if($raintpl_cache && $cache = $tpl->cache('server', 60, 1) ){echo $cache;	exit;}

$title = '系统运行检测';
$points = array('系统管理', '系统工具', $title);
$params = array();
$helps  = array();
$extend = array();


$Y1 = '<font color="green">YES</font>';$N1 = '<font color="red">NO</font>';
$Y = '<font color="green">支持<b>√</b></font>';$N = '<font color="red">不支持<b>×</b></font>';
$params[] = array('name'=>'系统环境信息','val'=>'','tip'=>'_Title_' );
$params[] = array('name'=>'php版本','val'=>phpversion() );
$params[] = array('name'=>'zend版本','val'=>zend_version() );
$params[] = array('name'=>'MySqlVersion版本','val'=>mysql_get_server_info() );
$params[] = array('name'=>'web服务器','val'=>$_SERVER?$_SERVER[SERVER_SOFTWARE]:getenv(SERVER_SOFTWARE) );
$params[] = array('name'=>'当前系统版本','val'=>$Config->SysVersion );

ob_start();
phpinfo(INFO_GENERAL);
$string = ob_get_contents();
ob_end_clean();
$pieces = explode("<h2", $string);
foreach($pieces as $val){
	preg_match_all("/<tr[^>]*><td[^>]*>(.*)<\/td><td[^>]*>(.*)<\/td>/Ux", $val, $sub);
	foreach($sub[0] as $key => $val) {
		if (preg_match("/Loaded Configuration File /", $val)){
			$val = preg_replace("/Loaded Configuration File /", '', $val);
			$phpini = strip_tags($val);
			break;
		}
	}
}
$params[] = array('name'=>'phpini的位置','val'=>$phpini );

$params[] = array('name'=>'系统环境检查','val'=>'','tip'=>'_Title_' );
$params[] = array('name'=>'PHP version >= 4.3.9','val'=>version_compare(phpversion(), "4.3.8") ? $Y:$N );
$params[] = array('name'=>'GD2 extension support','val'=>extension_loaded('gd')?$Y:$N );
$params[] = array('name'=>'MySQL support','val'=>function_exists( 'mysql_connect' ) ?$Y:$N );

function dir_wriable($dir){$wb = false;if(is_dir($dir)){if($fp = @fopen("$dir/test.txt", 'w')){if(@file_put_contents("$dir/test.txt","asd")){$wb=true;}@fclose($fp);@unlink("$dir/test.txt");}}else{if($fp = @fopen($dir, 'a+')){@fclose($fp);$wb = true;}}return $wb;}
$params[] = array('name'=>'data目录可写入','val'=>dir_wriable('../../data')?$Y:$N );
$params[] = array('name'=>'upload目录可写入','val'=>dir_wriable('../../upload')?$Y:$N );


$params[] = array('name'=>'phpini 设置','val'=>'','tip'=>'_Title_' );
$params[] = array('name'=>'设置项','val'=>'推荐值','tip'=>'当前设置是否是推荐值' );
$recommendedSettings = array(
	//array( 'y2k_compliance', 'y2k_compliance','YES' ),
	//array( 'display_errors', 'display_errors','YES' ),
	//array( 'register_globals', 'register_globals', 'NO'),
	//array( 'magic_quotes_gpc', 'magic_quotes_gpc','YES' ),
	//array( 'file_uploads', 'file_uploads','YES' ),
	//array( 'session.use_cookies', 'session.use_cookies','YES' ),
	array( 'session.auto_start', 'session.auto_start', 'NO'),
	//array( 'session.use_trans_sid', 'session.use_trans_sid', 'NO')
);
foreach ($recommendedSettings as $v){
	$params[] = array('name'=> $v[0],'val'=> $v[2],'tip'=> ini_get($v[1]) == $v[2] ? $Y1:$N1 );
}
$recommendedSettings1 = array(
	array( 'Image Support', 'imagecreatetruecolor','YES' ),
	array( 'JPEG Support', 'imagejpeg,imagecreatefromjpeg','YES' ),
	array( 'ICONV Support', 'iconv', 'YES'),
	array( 'BCMATH Enabled', 'bcmod','YES' ),
	array( 'Apache Header', 'apache_request_headers','YES' ),
);
function functions_exists($fun){$r=true;$f = explode(',', $fun);foreach($f as $v){$r = $r&&function_exists($v);}return $r;}
foreach ($recommendedSettings1 as $v){
	$params[] = array('name'=> $v[0],'val'=> $v[2],'tip'=> functions_exists($v[1]) ? $Y1:$N1 );
}

$params[] = array('name'=>'可能影响系统运行的设置','val'=>'','tip'=>'_Title_' );
$params[] = array('name'=>'设置项','val'=>'当前值 | 推荐值','tip'=>'小常识' );
$params[] = array('name'=>'max_execution_time','val'=>ini_get('max_execution_time').' | 30秒','tip'=>'每个脚本最大允许执行时间,按秒计。默认为30秒，这个参数有助于阻止劣质脚本无休止的占用服务器资源' );
$params[] = array('name'=>'max_input_time','val'=>ini_get('max_input_time').' | 60秒','tip'=>'每个脚本接收输入数据的最大允许时间(POST,   GET,   upload)' );
$params[] = array('name'=>'memory_limit','val'=>ini_get('memory_limit').' | 8M','tip'=>'设定一个脚本所能够申请到的最大内存字节数。这有助于防止劣质脚本消耗完服务器上的所有内存。要使用此指令必须在编译的时候激活' );
$params[] = array('name'=>'session.gc_maxlifetime','val'=>ini_get('session.gc_maxlifetime').' | 1440秒','tip'=>'Session数据在服务器端存储的最大时间(秒)。默认为1440秒，如果超过这个时间，那么Session数据就自动删除！' );


//initialize a Rain TPL object
$tpl = new RainTPL;
$tpl->assign( 'title', $title );
$tpl->assign( 'points', $points );
$tpl->assign( 'params', $params );
$tpl->assign( 'helps', $helps );
$tpl->assign( 'extend', $extend );

$tpl->draw('server'.$raintpl_ver);
exit;
}
?>