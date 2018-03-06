<?php
include ('../../functions/config.php');
require_once('../../functions/functions.php');
if (!check_session()){
    echo json_encode(array('error' => _('Please login first')));
    exit;
}
slash_vars();
if (!empty($_POST['snapshotname'])){
	$snapshot_name = $_POST['snapshotname'];
}else {
    echo json_encode(array('error' => _('Empty snapshot name')));
    exit;
}

if (!empty($_POST['idvm']))
	$vm = $_POST['idvm'];

$existing = get_SQL_line("SELECT name FROM list_snapshot WHERE name = '$snapshot_name'");
if (!empty($existing[0])){
    echo json_encode(array('error' => _("Snapshot $snapshot_name already exists")));
    exit;
}else {
	$child_vms = get_SQL_array("SELECT hypervisor FROM vms WHERE id='$vm'");
	$id_ini = $child_vms[0][0];
	$h_reply = get_SQL_line("SELECT * FROM hypervisors WHERE  id= '$id_ini'");
	ssh_connect($h_reply[2].":".$h_reply[3]);
	$child_vms_name = get_SQL_line("SELECT name FROM vms WHERE id='$vm'");
	$snapshot_cm = "sudo virsh snapshot-create-as " . $child_vms_name[0] . " --name " . $snapshot_name . " --atomic";
	ssh_command($snapshot_cm,true);
	add_SQL_line("UPDATE vms SET snapshot = 'true' WHERE id = '$vm'");
	add_SQL_line("INSERT INTO list_snapshot(name,vmid) VALUES('$snapshot_name','$vm')");
}

echo json_encode(array('success' => _('Snapshot created.')));
exit;
