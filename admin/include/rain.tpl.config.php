<?php
	//set tpl and cache path
	raintpl::configure('tpl_dir', WEBROOT. 'admin/tpl/' );
	raintpl::configure('cache_dir', WEBROOT . 'admin/tmp/' );
	
	//initialize a Rain TPL object	
	$tpl = new RainTPL;

	//tmp file is enable ? //develement false ;ok - true
	$raintpl_cache = false;
	$raintpl_ver='.v1';
?>