<?php

//includes
	require_once "root.php";
	require_once "resources/require.php";
	require_once "resources/check_auth.php";

//check permissions	
	if (permission_exists('webphone_view')) {
		//access granted
	}
	else {
		echo "access denied";
		exit;
	}

//add multi-lingual support
	$language = new text;
	$text = $language->get();


	if (is_uuid($_GET['id'])) {
		$extension_uuid = $_GET['id'];
	}
	
//get the user ID
	$sql = "SELECT extension, password,effective_caller_id_name ";
	$sql .= "FROM v_extensions ";
	$sql .= "WHERE extension_uuid = '" . $extension_uuid . "' ";
	$sql .= "AND v_extensions.domain_uuid = '" . $_SESSION["domain_uuid"] . "' LIMIT 1";
	
	$prep_statement = $db->prepare($sql);
	if ($prep_statement) {
		$prep_statement->execute();
		$row = $prep_statement->fetch(PDO::FETCH_ASSOC);
		$user_extension = $row['extension'];
		$user_password = $row['password'];
		$effective_caller_id_name = $row['effective_caller_id_name'];
	}
	
echo "<head>\n";
echo "    <meta charset='utf-8' />\n";
echo "    <meta name='viewport' content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no'>\n";
echo "    <title>" . $text['title-webphone'] . "</title>\n";
echo "    <link rel='icon' type='image/gif' href='/themes/default/favicon.ico'/>\n";
echo "    <link rel='stylesheet' type='text/css' href='/resources/bootstrap/css/bootstrap.min.css.php'>\n";
echo "    <link rel='stylesheet' type='text/css' href='/resources/fontawesome/css/all.min.css.php'>\n";
echo "    <link rel='stylesheet' type='text/css' href='resources/css/ctxSip.css'>\n";
echo "</head>\n";

echo "<body id='sipClient'>\n";
echo "<div class='container-fluid'>\n";

echo "    <div class='clearfix sipStatus'>\n";
echo "        <div id='txtCallStatus' class='pull-right'>&nbsp;</div>\n";
echo "        <div id='txtRegStatus'></div>\n";
echo "    </div>\n";

echo "    <div class='form-group' id='phoneUI'>\n";
echo "        <div class='input-group'>\n";
echo "            <div class='input-group-btn'>\n";
echo "                <button class='btn btn-sm btn-primary dropdown-toggle' data-toggle='dropdown' title='Show Keypad'>\n";
echo "                    <i class='fa fa-th'></i>\n";
echo "                </button>\n";
echo "                <div id='sip-dialpad' class='dropdown-menu'>\n";
echo "                    <button type='button' class='btn btn-default digit' data-digit='1'>1<span>&nbsp;</span></button>\n";
echo "                    <button type='button' class='btn btn-default digit' data-digit='2'>2<span>ABC</span></button>\n";
echo "                    <button type='button' class='btn btn-default digit' data-digit='3'>3<span>DEF</span></button>\n";
echo "                    <button type='button' class='btn btn-default digit' data-digit='4'>4<span>GHI</span></button>\n";
echo "                    <button type='button' class='btn btn-default digit' data-digit='5'>5<span>JKL</span></button>\n";
echo "                    <button type='button' class='btn btn-default digit' data-digit='6'>6<span>MNO</span></button>\n";
echo "                    <button type='button' class='btn btn-default digit' data-digit='7'>7<span>PQRS</span></button>\n";
echo "                    <button type='button' class='btn btn-default digit' data-digit='8'>8<span>TUV</span></button>\n";
echo "                    <button type='button' class='btn btn-default digit' data-digit='9'>9<span>WXYZ</span></button>\n";
echo "                    <button type='button' class='btn btn-default digit' data-digit='*'>*<span>&nbsp;</span></button>\n";
echo "                    <button type='button' class='btn btn-default digit' data-digit='0'>0<span>+</span></button>\n";
echo "                    <button type='button' class='btn btn-default digit' data-digit='#'>#<span>&nbsp;</span></button>\n";
echo "                    <div class='clearfix'>&nbsp;</div>\n";
echo "                    <button class='btn btn-success btn-block btnCall' title='Send'>\n";
echo "                        <i class='fa fa-play'></i> Send\n";
echo "                    </button>\n";
echo "                </div>\n";
echo "            </div>\n";
echo "            <input type='text' name='number' id='numDisplay' class='form-control text-center input-sm' value='' placeholder='Enter number...' autocomplete='off' />\n";
echo "            <div class='input-group-btn input-group-btn-sm'>\n";
echo "                <button class='btn btn-sm btn-primary dropdown-toggle' id='btnVol' data-toggle='dropdown' title='Volume'>\n";
echo "                    <i class='fa fa-fw fa-volume-up'></i>\n";
echo "                </button>\n";
echo "                <div class='dropdown-menu dropdown-menu-right'>\n";
echo "                    <input type='range' min='0' max='100' value='100' step='1' id='sldVolume' />\n";
echo "                </div>\n";
echo "            </div>\n";
echo "        </div>\n";
echo "    </div>\n";

echo "   <div class=well-sip'>\n";
echo "       <div id='sip-splash' class='text-muted text-center panel panel-default'>\n";
echo "           <div class='panel-body'>\n";
echo "                <h3 class='page-header'>\n";
echo "                <span class='fa-stack fa-2x'>\n";
echo "                    <i class='fa fa-circle fa-stack-2x text-success'></i>\n";
echo "                    <i class='fa fa-phone fa-stack-1x fa-inverse'></i>\n";
echo "                </span><br>\n";
echo "                This is your phone.</h3>\n";
echo "                <p class='lead'>To make a call enter a number in the box above.</p>\n";
echo "                <small>Closing this window will cause calls to go to voicemail.</small>\n";
echo "            </div>\n";
echo "        </div>\n";

echo "        <div id='sip-log' class='panel panel-default hide'>\n";
echo "            <div class='panel-heading'>\n";
echo "                <h4 class='text-muted panel-title'>Recent Calls <span class='pull-right'><i class='fa fa-trash text-muted sipLogClear' title='Clear Log'></i></span></h4>\n";
echo "            </div>\n";
echo "            <div id='sip-logitems' class='list-group'>\n";
echo "                <p class='text-muted text-center'>No recent calls from this browser.</p>\n";
echo "            </div>\n";
echo "        </div>\n";
echo "    </div>\n";

echo "    <div class='modal fade' id='mdlError' tabindex='-1' role='dialog' aria-hidden='true' data-backdrop='static' data-keyboard='false'>\n";
echo "        <div class='modal-dialog modal-sm'>\n";
echo "            <div class='modal-content'>\n";
echo "                <div class='modal-header'>\n";
echo "                    <h4 class='modal-title'>Sip Error</h4>\n";
echo "                </div>\n";
echo "                <div class='modal-body text-center text-danger'>\n";
echo "                    <h3><i class='fa fa-3x fa-ban'></i></h3>\n";
echo "                    <p class='lead'>Sip registration failed. No calls can be handled.</p>\n";
echo "                </div>\n";
echo "            </div>\n";
echo "        </div>\n";
echo "    </div>\n";

echo "</div>\n";

echo "<audio id='ringtone' src='resources/sounds/incoming.mp3' loop></audio>\n";
echo "<audio id='ringbacktone' src='resources/sounds/outgoing.mp3' loop></audio>\n";
echo "<audio id='dtmfTone' src='resources/sounds/dtmf.mp3'></audio>\n";
echo "<audio id='dtmf-0' src='resources/sounds/dtmf-0.mp3'></audio>\n";
echo "<audio id='dtmf-1' src='resources/sounds/dtmf-1.mp3'></audio>\n";
echo "<audio id='dtmf-2' src='resources/sounds/dtmf-2.mp3'></audio>\n";
echo "<audio id='dtmf-3' src='resources/sounds/dtmf-3.mp3'></audio>\n";
echo "<audio id='dtmf-4' src='resources/sounds/dtmf-4.mp3'></audio>\n";
echo "<audio id='dtmf-5' src='resources/sounds/dtmf-5.mp3'></audio>\n";
echo "<audio id='dtmf-6' src='resources/sounds/dtmf-6.mp3'></audio>\n";
echo "<audio id='dtmf-7' src='resources/sounds/dtmf-7.mp3'></audio>\n";
echo "<audio id='dtmf-8' src='resources/sounds/dtmf-8.mp3'></audio>\n";
echo "<audio id='dtmf-9' src='resources/sounds/dtmf-9.mp3'></audio>\n";
echo "<audio id='dtmf-pound' src='resources/sounds/dtmf-pound.mp3'></audio>\n";
echo "<audio id='dtmf-star' src='resources/sounds/dtmf-star.mp3'></audio>\n";

echo "<audio id='audioRemote'></audio>\n";

echo "<script type='text/javascript' src='/resources/jquery/jquery.min.js.php'></script>\n";
echo "<script type='text/javascript' src='resources/js/popper.min.js'></script>\n";
echo "<script type='text/javascript' src='/resources/bootstrap/js/bootstrap.min.js.php'></script>\n";
echo "<script type='text/javascript' src='/resources/momentjs/moment-with-locales.min.js.php'></script>\n";

echo "<script type='text/javascript' src='resources/js/SIP.js/sip.min.js'></script>\n";

echo "<script type='text/javascript'>\n";
echo 	"var user = {'User' : '" . $user_extension. "', ";
echo    " 'Pass' : '".$user_password."', ";
echo    " 'Realm' : '".$_SESSION["domain_name"]."', ";
echo    " 'Display' : '".$effective_caller_id_name."', ";
echo    " 'WSServer'  : 'wss://".$_SESSION["domain_name"].":7443' ";
echo "};\n";
echo "</script>\n";

echo "<script type='text/javascript' src='resources/js/app.js'></script>\n";

echo "</body>\n";
?>
