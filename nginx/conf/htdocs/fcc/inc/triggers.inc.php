<?php
if (isset($_POST["login_user"]) && isset($_POST["pass_user"])) {
	if (check_login($_POST["login_user"], $_POST["pass_user"], "/var/www/mail/pfadmin/config.local.php") == true) { $_SESSION['fufix_cc_loggedin'] = "yes"; }
}
if (isset($_SESSION['fufix_cc_loggedin']) && $_SESSION['fufix_cc_loggedin'] == "yes") {
	if (isset($_GET["del"])) {
		del_fufix_opendkim_entry($_GET["del"]);
	}
	if (isset($_POST["vtapikey"]) && ctype_alnum($_POST["vtapikey"])) {
		file_put_contents($VT_API_KEY, $_POST["vtapikey"]);
	}
	if (isset($_POST["maxmsgsize"]) && ctype_alnum($_POST["maxmsgsize"])) {
		set_fufix_msg_size($_POST["maxmsgsize"]);
	}
	if (isset($_POST["sender"])) {
		set_fufix_sender_access($_POST["sender"]);
		postfix_reload();
	}
	if (isset($_POST["dkim_selector"])) {
		add_fufix_opendkim_entry($_POST["dkim_selector"], $_POST["dkim_domain"]);
	}
	if (isset($_POST["ext"])) {
		if (isset($_POST["virustotaltoggle"]) && $_POST["virustotaltoggle"] == "on") {
			set_fufix_reject_attachments($_POST["ext"], "filter");
		} else {
			set_fufix_reject_attachments($_POST["ext"], "reject");
		}
		postfix_reload();
	}
	if (isset($_POST["anonymize_"])) {
		if (!isset($_POST["anonymize"])) { $_POST["anonymize"] = ""; }
		set_fufix_anonymize_headers($_POST["anonymize"]);
		postfix_reload();
	}
	if (isset($_POST["logout"])) {
		$_SESSION['fufix_cc_loggedin'] = "no";
	}
}
?>
