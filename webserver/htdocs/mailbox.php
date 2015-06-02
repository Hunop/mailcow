<?php
require_once("inc/header.inc.php");
if (isset($_SESSION['fufix_cc_loggedin']) && $_SESSION['fufix_cc_loggedin'] == "yes") {
?>
<div class="container">
	<div class="row">
		<div class="col-md-14">
			<div class="panel panel-default">
				<div class="panel-heading">
				<h3 class="panel-title">Domains</h3>
				<div class="pull-right">
					<span class="clickable filter" data-toggle="tooltip" title="Toggle table filter" data-container="body">
						<i class="glyphicon glyphicon-filter"></i>
					</span>
					<a href="do.php?adddomain"><span class="glyphicon glyphicon-plus"></span></a>
				</div>
				</div>
				<div class="panel-body">
					<input type="text" class="form-control" id="domaintable-filter" data-action="filter" data-filters="#domaintable" placeholder="Filter" />
				</div>
				<div class="table-responsive">
				<table class="table table-striped" id="domaintable">
					<thead>
						<tr>
							<th>Domain</th>
							<th>Aliases</th>
							<th>Mailboxes</th>
							<th>Max. quota per mailbox</th>
							<th>Domain Quota</th>
							<th>Active</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody>
<?php
$result = mysqli_query($link, "SELECT domain, aliases, mailboxes, maxquota, quota, active FROM 
domain WHERE 
domain IN (SELECT domain from domain_admins WHERE username='$logged_in_as') OR 'admin'='$logged_in_role'");
while ($row = mysqli_fetch_array($result)) {
	echo "<tr><td>", $row['domain'],
	"</td><td>", mysqli_result(mysqli_query($link, "SELECT count(*) FROM alias WHERE domain='$row[domain]' and address NOT IN (SELECT username FROM mailbox)")),
		" of ", $row['aliases'],
	"</td><td>", mysqli_result(mysqli_query($link, "SELECT count(*) FROM mailbox WHERE domain='$row[domain]'")),
		" of ", $row['mailboxes'],
	"</td><td>", $row['maxquota'], "M",
	"</td><td>", mysqli_result(mysqli_query($link, "SELECT coalesce(round(sum(quota)/1048576), 0) FROM mailbox WHERE domain='$row[domain]'")),
		"M of ", $row['quota'], "M",
	"</td><td>", $row['active'],
	"</td><td><a href=\"do.php?deletedomain=", $row['domain'], "\">delete</a> | <a href=\"do.php?editdomain=", $row['domain'], "\">edit</a>",
	"</td></tr>";
}
?>
					</tbody>
				</table>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-14">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">Domain Aliases</h3>
					<div class="pull-right">
						<span class="clickable filter" data-toggle="tooltip" title="Toggle table filter" data-container="body">
							<i class="glyphicon glyphicon-filter"></i>
						</span>
						<a href="do.php?addaliasdomain"><span class="glyphicon glyphicon-plus"></span></a>
					</div>
				</div>
				<div class="panel-body">
					<input type="text" class="form-control" id="domainaliastable-filter" data-action="filter" data-filters="#domainaliastable" placeholder="Filter" />
				</div>
				<div class="table-responsive">
				<table class="table table-striped" id="domainaliastable">
					<thead>
						<tr>
							<th>Alias domain</th>
							<th>Target domain</th>
							<th>Active</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody>
<?php
$result = mysqli_query($link, "SELECT alias_domain, target_domain, active FROM 
alias_domain WHERE 
target_domain IN (SELECT domain from domain_admins WHERE username='$logged_in_as') OR 'admin'='$logged_in_role'");
while ($row = mysqli_fetch_array($result)) {
	echo "<tr><td>", $row['alias_domain'],
	"</td><td>", $row['target_domain'],
	"</td><td>", $row['active'],
	"</td><td><a href=\"do.php?deletealiasdomain=", $row['alias_domain'], "\">delete</a>",
	"</td></tr>";
}
?>
					</tbody>
				</table>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-14">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">Mailboxes</h3>
					<div class="pull-right">
						<span class="clickable filter" data-toggle="tooltip" title="Toggle table filter" data-container="body">
							<i class="glyphicon glyphicon-filter"></i>
						</span>
						<a href="do.php?addmailbox"><span class="glyphicon glyphicon-plus"></span></a>
					</div>
				</div>
				<div class="panel-body">
					<input type="text" class="form-control" id="mailboxtable-filter" data-action="filter" data-filters="#mailboxtable" placeholder="Filter" />
				</div>
				<div class="table-responsive">
				<table class="table table-striped" id="mailboxtable">
					<thead>
						<tr>
							<th>Username</th>
							<th>Name</th>
							<th>Domain</th>
							<th>Quota</th>
							<th>In use</th>
							<th>Msg #</th>
							<th>Active</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody>
<?php
$result = mysqli_query($link, "SELECT mailbox.username, name, active, domain, quota, bytes, messages 
FROM mailbox, quota2 WHERE (mailbox.username = quota2.username) AND 
(domain IN (SELECT domain from domain_admins WHERE username='$logged_in_as') OR 'admin'='$logged_in_role')");
while ($row = mysqli_fetch_array($result)) {
	echo "<tr><td>", $row['username'],
	"</td><td>", $row['name'],
	"</td><td>", $row['domain'],
	"</td><td>";
if((formatBytes($row['quota'], 2)) == "0" ) { echo "&#8734;"; } else { echo formatBytes($row['quota'], 2); }
	echo "</td><td>", formatBytes($row['bytes'], 2),
	"</td><td>", $row['messages'],
	"</td><td>", $row['active'],
	"</td><td><a href=\"do.php?deletemailbox=", $row['username'], "\">delete</a> | <a href=\"do.php?editmailbox=", $row['username'], "\">edit</a>",
	"</td></tr>";
}
?>
					</tbody>
				</table>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-14">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">Aliases</h3>
					<div class="pull-right">
						<span class="clickable filter" data-toggle="tooltip" title="Toggle table filter" data-container="body">
							<i class="glyphicon glyphicon-filter"></i>
						</span>
						<a href="do.php?addalias"><span class="glyphicon glyphicon-plus"></span></a>
					</div>
				</div>
				<div class="panel-body">
					<input type="text" class="form-control" id="aliastable-filter" data-action="filter" data-filters="#aliastable" placeholder="Filter" />
				</div>
				<div class="table-responsive">
				<table class="table table-striped" id="aliastable">
					<thead>
						<tr>
							<th>Alias address</th>
							<th>Destination</th>
							<th>Domain</th>
							<th>Active</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody>
<?php
$result = mysqli_query($link, "SELECT address, goto, domain, active FROM alias WHERE 
(address NOT IN (SELECT username FROM mailbox) AND address!=goto) AND 
(domain IN (SELECT domain from domain_admins WHERE username='$logged_in_as') OR 
'admin'='$logged_in_role')");
while ($row = mysqli_fetch_array($result)) {
	echo "<tr><td>", $row['address'],
	"</td><td>", $row['goto'],
	"</td><td>", $row['domain'],
	"</td><td>", $row['active'],
	"</td><td><a href=\"do.php?deletealias=", $row['address'], "\">delete</a>",
	"</td></tr>";
}
?>
					</tbody>
				</table>
				</div>
			</div>
		</div>
	</div>
</div>
<?php }
else {
	header('Location: admin.php');
}
 ?>
<script src="//code.jquery.com/jquery-1.11.3.min.js"></script>
<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
<script src="js/ripples.min.js"></script>
<script src="js/material.min.js"></script>
<script>
$(document).ready(function() {
        $.material.init();
});
(function(){
    'use strict';
	var $ = jQuery;
	$.fn.extend({
		filterTable: function(){
			return this.each(function(){
				$(this).on('keyup', function(e){
					$('.filterTable_no_results').remove();
					var $this = $(this),
                        search = $this.val().toLowerCase(),
                        target = $this.attr('data-filters'),
                        $target = $(target),
                        $rows = $target.find('tbody tr');
					if(search == '') {
						$rows.show();
					} else {
						$rows.each(function(){
							var $this = $(this);
							$this.text().toLowerCase().indexOf(search) === -1 ? $this.hide() : $this.show();
						})
						if($target.find('tbody tr:visible').size() === 0) {
							var col_count = $target.find('tr').first().find('td').size();
							var no_results = $('<tr class="filterTable_no_results"><td colspan="'+col_count+'">No results found</td></tr>')
							$target.find('tbody').append(no_results);
						}
					}
				});
			});
		}
	});
	$('[data-action="filter"]').filterTable();
})(jQuery);

$(function(){
	$('[data-action="filter"]').filterTable();
	$('.container').on('click', '.panel-heading span.filter', function(e){
		var $this = $(this),
		$panel = $this.parents('.panel');
		$panel.find('.panel-body').slideToggle("fast");
		if($this.css('display') != 'none') {
			$panel.find('.panel-body input').focus();
		}
	});
	$('[data-toggle="tooltip"]').tooltip();
})
</script>
</body>
</html>
