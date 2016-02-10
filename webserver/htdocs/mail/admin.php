<?php
require_once("inc/header.inc.php");
$_SESSION['return_to'] = $_SERVER['REQUEST_URI'];
?>
<div class="container">
<?php
if (isset($_SESSION['mailcow_cc_role']) && $_SESSION['mailcow_cc_role'] == 'admin') {
?>
<h4><span class="glyphicon glyphicon-user" aria-hidden="true"></span> <?=$lang['admin']['access'];?></h4>
<div class="panel-group" id="accordion_access">
	<div class="panel panel-danger">
		<div class="panel-heading" data-toggle="collapse" data-parent="#accordion_access" data-target="#collapseAdmin">
			<span style="cursor:pointer;" class="accordion-toggle"><?=$lang['admin']['admin_details'];?></span>
		</div>
		<div id="collapseAdmin" class="panel-collapse collapse in">
			<div class="panel-body">
				<form class="form-horizontal" autocapitalize="none" autocorrect="off" role="form" method="post">
				<?php
				$adminData = mysqli_fetch_assoc(mysqli_query($link,
					"SELECT `username` FROM `admin`
						WHERE `superadmin`='1' and active='1'"));
				?>
					<input type="hidden" name="admin_user_now" value="<?=$adminData['username'];?>">
					<div class="form-group">
						<label class="control-label col-sm-2" for="admin_user"><?=$lang['admin']['admin'];?>:</label>
						<div class="col-sm-10">
						<input type="text" class="form-control" name="admin_user" id="admin_user" value="<?=$adminData['username'];?>" required>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-2" for="admin_pass"><?=$lang['admin']['password'];?>:</label>
						<div class="col-sm-10">
						<input type="password" class="form-control" name="admin_pass" id="admin_pass" placeholder="<?=$lang['admin']['unchanged_if_empty'];?>">
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-2" for="admin_pass2"><?=$lang['admin']['password_repeat'];?>:</label>
						<div class="col-sm-10">
						<input type="password" class="form-control" name="admin_pass2" id="admin_pass2">
						</div>
					</div>
					<div class="form-group">
						<div class="col-sm-offset-2 col-sm-10">
							<button type="submit" name="trigger_set_admin" class="btn btn-default"><?=$lang['admin']['save'];?></button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>

	<div class="panel panel-warning">
	<div class="panel-heading" data-toggle="collapse" data-parent="#accordion_access" data-target="#collapseDomAdmins">
		<span style="cursor:pointer;" class="accordion-toggle"><?=$lang['admin']['domain_admins'];?></span>
	</div>
		<div id="collapseDomAdmins" class="panel-collapse collapse out">
			<div class="panel-body">
				<form method="post">
					<div class="table-responsive">
					<table class="table table-striped" id="domainadminstable">
						<thead>
						<tr>
							<th><?=$lang['admin']['username'];?></th>
							<th><?=$lang['admin']['admin_domains'];?></th>
							<th><?=$lang['admin']['active'];?></th>
							<th><?=$lang['admin']['action'];?></th>
						</tr>
						</thead>
						<tbody>
							$result = mysqli_query($link, "SELECT
								username,
								LOWER(GROUP_CONCAT(DISTINCT domain SEPARATOR ', ')) AS domain,
								CASE active WHEN 1 THEN '".$lang['admin']['yes']."' ELSE '".$lang['admin']['no']."' END AS active
									FROM `domain_admins`
										WHERE `username` NOT IN (
											SELECT `username` FROM admin
												WHERE `superadmin`='1'
										)
										AND `username` IN (
											SELECT `username` FROM `admin`
										) GROUP BY username;")
								OR die(mysqli_error($link));
							while ($row = mysqli_fetch_array($result)):
							?>
							<tr>
								<td><?=$row['username'];?></td>
								<td><?=$row['domain'];?></td>
								<td><?=$row['active'];?></td>
								<td><a href="/delete/domainadmin/<?=$row['username'];?>"><?=$lang['admin']['remove'];?></a> |
									<a href="/edit/domainadmin/<?=$row['username'];?>"><?=$lang['admin']['edit'];?></a></td>
								</td>
							</tr>
							<?php
							endwhile;
							?>
						</tbody>
					</table>
					</div>
				</form>
				<small>
				<legend><?=$lang['admin']['add_domain_admin'];?></legend>
				<form class="form-horizontal" role="form" method="post">
					<div class="form-group">
						<label class="control-label col-sm-2" for="username"><?=$lang['admin']['username'];?> <kbd>[aA-zZ,.]</kbd>:</label>
						<div class="col-sm-10">
							<input type="text" class="form-control" name="username" id="username" required>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-2" for="name"><?=$lang['admin']['admin_domains'];?>:</label>
						<div class="col-sm-10">
							<select title="Domains durchsuchen..." style="width:100%" name="domain[]" size="5" multiple>
				<?php
				$resultselect = mysqli_query($link, "SELECT domain FROM domain");
				while ($row = mysqli_fetch_array($resultselect)) {
				echo "<option>".$row['domain']."</option>";
				}
				?>
							</select>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-2" for="password"><?=$lang['admin']['password'];?>:</label>
						<div class="col-sm-10">
						<input type="password" class="form-control" name="password" id="password" placeholder="">
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-2" for="password2"><?=$lang['admin']['password_repeat'];?>:</label>
						<div class="col-sm-10">
						<input type="password" class="form-control" name="password2" id="password2" placeholder="">
						</div>
					</div>
					<div class="form-group">
						<div class="col-sm-offset-2 col-sm-10">
							<div class="checkbox">
							<label><input type="checkbox" name="active" checked> <?=$lang['admin']['active'];?></label>
							</div>
						</div>
					</div>
					<div class="form-group">
						<div class="col-sm-offset-2 col-sm-10">
							<button type="submit" name="trigger_add_domain_admin" class="btn btn-default"><?=$lang['admin']['add'];?></button>
						</div>
					</div>
				</form>
				</small>
			</div>
		</div>
	</div>
</div>

<h4><span class="glyphicon glyphicon-wrench" aria-hidden="true"></span> <?=$lang['admin']['configuration'];?></h4>
<div class="panel-group" id="accordion_config">
	<div class="panel panel-default">
		<div class="panel-heading" data-toggle="collapse" data-parent="#accordion_config" data-target="#collapseDKIM">
			<span style="cursor:pointer;" class="accordion-toggle"><?=$lang['admin']['dkim_keys'];?></span>
		</div>
		<div id="collapseDKIM" class="panel-collapse collapse">
			<div class="panel-body">
				<?php
				$dnstxt_folder	= scandir($GLOBALS["MC_ODKIM_TXT"]);
				$dnstxt_files	= array_diff($dnstxt_folder, array('.', '..'));
				foreach($dnstxt_files as $file) {
					$str = file_get_contents($GLOBALS["MC_ODKIM_TXT"]."/".$file);
					$str = preg_replace('/\r|\t|\n/', '', $str);
					preg_match('/\(.*\)/im', $str, $matches);
					if(isset($matches[0])) {
						$str = str_replace(array(' ', '"', '(', ')'), '', $matches[0]);
					}
				?>
					<div class="row">
						<div class="col-xs-2">
							<p>Domain: <strong><?=explode("_", $file)[1];?></strong> (default._domainkey)</p>
						</div>
						<div class="col-xs-9">
							<pre><?=$str;?></pre>
						</div>
						<div class="col-xs-1">
							<a href="?del=<?=$dkim_record;?>" onclick="return confirm('<?=sprintf($lang['dkim']['confirm']);?>')"><span class="glyphicon glyphicon-remove-circle"></span></a>
						</div>
					</div>
				<?php
				}
				?>
				<legend><?=$lang['admin']['dkim_add_key'];?></legend>
				<form class="form-inline" role="form" method="post">
					<div class="form-group">
						<label for="dkim_domain">Domain</label>
						<input class="form-control" id="dkim_domain" name="dkim_domain" placeholder="example.org">
					</div>
					<div class="form-group">
						<label for="dkim_selector">Selector</label>
						<input class="form-control" id="dkim_selector" name="dkim_selector" value="default">
					</div>
					<button type="submit" class="btn btn-default"><span class="glyphicon glyphicon-plus"></span> <?=$lang['admin']['add'];?></button>
				</form>
			</div>
		</div>
	</div>

	<div class="panel panel-default">
		<div class="panel-heading" data-toggle="collapse" data-parent="#accordion_config" data-target="#collapseMsgSize">
			<span style="cursor:pointer;" class="accordion-toggle"><?=$lang['admin']['msg_size'];?></span>
		</div>
		<div id="collapseMsgSize" class="panel-collapse collapse">
		<div class="panel-body">
		<form class="form-inline" method="post">
			<p><?=$lang['admin']['msg_size_limit'];?>: <strong><?=getMaxMessageSize();?>MB</strong></p>
			<p><?=$lang['admin']['msg_size_limit_details'];?></p>
			<div class="form-group">
				<input type="number" class="form-control" id="maxmsgsize" name="maxmsgsize" placeholder="in MB" min="1" max="250">
			</div>
			<button type="submit" class="btn btn-default"><?=$lang['admin']['save'];?></button>
		</form>
		</div>
		</div>
	</div>
</div>

<h4><span class="glyphicon glyphicon-dashboard" aria-hidden="true"></span> <?=$lang['admin']['maintenance'];?></h4>
<div class="panel-group" id="accordion_maint">
<div class="panel panel-default">
	<div class="panel-heading" data-toggle="collapse" data-parent="#accordion_maint" data-target="#collapseSysinfo">
		<span style="cursor:pointer;" class="accordion-toggle"><?=$lang['admin']['sys_info'];?></span>
	</div>
	<div id="collapseSysinfo" class="panel-collapse collapse in">
	<div class="panel-body">
		<div class="row">
			<div class="col-md-6">
				<legend><span class="glyphicon glyphicon-hdd" data-toggle="tooltip" title="/var/vmail" aria-hidden="true"></span> Disk <?=formatBytes(disk_total_space('/var/vmail')-disk_free_space("/var/vmail"));?> / <?=formatBytes(disk_total_space('/var/vmail'))?></legend>
				<div class="progress">
				  <div class="progress-bar progress-bar-info progress-bar-striped" role="progressbar" aria-valuenow="<?php echo sys_info('vmail_percentage');?>"
				  aria-valuemin="0" aria-valuemax="100" style="width:<?php echo sys_info('vmail_percentage');?>%">
				  </div>
				</div>
			</div>
			<div class="col-md-6">
				<legend><span class="glyphicon glyphicon-dashboard" aria-hidden="true"></span> RAM <?=formatBytes(sys_info('ram')['used']);?> / <?=formatBytes(sys_info('ram')['total']);?></legend>
				<div class="progress">
				  <div class="progress-bar progress-bar-info progress-bar-striped" role="progressbar" aria-valuenow="<?php echo sys_info('ram');?>"
				  aria-valuemin="0" aria-valuemax="100" style="width:<?=sys_info('ram')['used_percent'];?>%">
				  </div>
				</div>
			</div>
		</div>
		<legend>Postqueue</legend>
			<pre><?php echo sys_info("mailq");?></pre>
		<legend>Pflogsumm</legend>
			<textarea rows="20" style="font-family:monospace;font-size:9pt;width:100%;"><?php echo sys_info("pflog");?></textarea>
			<p><span class="glyphicon glyphicon-time" aria-hidden="true"></span> <?=round(abs(date('U') - filemtime($PFLOG)) / 60,0). " min.";?></p>
			<form method="post">
				<div class="form-group">
					<input type="hidden" name="pflog_renew" value="1">
					<button type="submit" class="btn btn-default"><span class="glyphicon glyphicon-refresh" aria-hidden="true"></span> Pflogsumm</button>
				</div>
			</form>
		<legend>Mailgraph</legend>
			<?=sys_info("mailgraph");?>
	</div>
	</div>
</div>

<?php
}
elseif (isset($_SESSION['mailcow_cc_role']) && $_SESSION['mailcow_cc_role'] == "domainadmin") {
	header('Location: /manager');
	die("Permission denied");
}
elseif (isset($_SESSION['mailcow_cc_role']) && $_SESSION['mailcow_cc_role'] == "user") {
	header('Location: /user');
	die("Permission denied");
} else {
?>
<div class="panel panel-default">
	<div class="panel-heading"><?=$lang['login']['title'];?></div>
	<div class="panel-body">
		<form method="post" autofill="off">
			<legend><?=$lang['login']['administration'];?></legend>
			<p><?=$lang['login']['administration_details'];?></p>
			<legend><?=$lang['login']['user_settings'];?></legend>
			<p><?=$lang['login']['user_settings_details'];?></p>
			<div class="form-group">
				<label for="login_user"><?=$lang['login']['username'];?>:</label>
				<input name="login_user" autocorrect="off" autocapitalize="none" type="name" id="login_user" class="form-control" required autofocus>
			</div>
			<div class="form-group">
				<label for="login_user"><?=$lang['login']['password'];?>:</label>
				<input name="pass_user" type="password" id="pass_user" class="form-control" required>
			</div>
			<button type="submit" class="btn btn-sm btn-success" value="Login"><?=$lang['login']['login'];?></button>
		<?php
		if (isset($_SESSION['ldelay']) && $_SESSION['ldelay'] != "0"):
		?>
		<p><div class="alert alert-info"><?=sprintf($lang['login']['delayed'], $_SESSION['ldelay']);?></b></div></p>
		<?php
		endif;
		?>
		</form>
	</div>
</div>

<?php
}
?>
<br />
<p><b><a href="../">&#8592; <?=$lang['login']['previous'];?></a></b></p>
</div> <!-- /container -->
<?php
require_once("inc/footer.inc.php");
?>
