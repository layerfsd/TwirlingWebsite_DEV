﻿<?php
$id = $_GET['id'];
?>
<!DOCTYPE HTML>
<!--
	Beijing Tuoling Inc. 2015 - twirlingvr.com
-->
<html>
<head>
	<title>Twirling Player</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0" />
	<meta name="apple-mobile-web-app-capable" content="yes" />
	<meta name="apple-mobile-web-app-status-bar-style" content="black" />
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
	<meta http-equiv="x-ua-compatible" content="IE=edge" />
	<style>
		@-ms-viewport { width:device-width; }
		@media only screen and (min-device-width:800px) { html { overflow:hidden; } }
		html { height:100%; }
		body { height:100%; overflow:hidden; margin:0; padding:0; font-family:Arial, Helvetica, sans-serif; font-size:16px; color:#FFFFFF; background-color:#000000; }
	</style>
</head>
<body>

<script src="viewer/twirlingvrPlayer.js"></script>
<!--
<script src="krpano.js"></script>
-->
<div id="pano" style="width:100%;height:100%;">
	<noscript><table style="width:100%;height:100%;"><tr style="vertical-align:middle;text-align:center;"><td>ERROR:<br><br>Javascript not activated<br><br></td></tr></table></noscript>
	<script>
		embedpano({swf:"viewer/twirlingvrPlayer.swf", xml:"videopano_xml.php?id=<?php echo $id;?>", target:"pano", html5:"auto", mobilescale:1.0, passQueryParameters:true});
	</script>
</div>

</body>
</html>