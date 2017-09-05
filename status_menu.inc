<?php

$pages = Array(
	'name' => "Controller Check",
	'type' => "status",
	'page' => "plugin_setup.php");

?>

<li><a href="<?php echo preg_replace('/.*\/plugins\/(.*)/', 'plugin.php?plugin=$1&page=plugin_setup.php', dirname(__FILE__)); ?>"><?echo $pages['name'];?></a></li>