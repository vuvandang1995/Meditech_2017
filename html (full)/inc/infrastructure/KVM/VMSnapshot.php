<?php
include dirname(__FILE__) . '/../../../functions/config.php';
require_once(dirname(__FILE__) . '/../../../functions/functions.php');
if (!check_session()){
    echo json_encode(array('error' => _('Please login first')));
    exit;
}
slash_vars();
$vm = $_POST['vm'];
$action = $_POST['action'];
if (empty($vm)){
    echo json_encode(array('error' => _('Missing source VM.')));
    exit;
}

if ($action == "single"){
    $snapshot = get_SQL_line("SELECT snapshot FROM vms WHERE id = '$vm'");
    if ($snapshot[0] == "true"){
        $child_vms = get_SQL_array("SELECT hypervisor FROM vms WHERE id='$vm'");
        $id_ini = $child_vms[0][0];
        $h_reply = get_SQL_line("SELECT * FROM hypervisors WHERE  id= '$id_ini'");
        ssh_connect($h_reply[2].":".$h_reply[3]);
        $child_vms_name = get_SQL_line("SELECT name FROM vms WHERE id='$vm'");
        ssh_command("sudo virsh shutdown " . $child_vms_name[0], true);
        $snapshot_name = str_replace("\n", "", (ssh_command("sudo virsh snapshot-list --domain $child_vms_name[0] | grep : | awk '{print $1}' ",true)));
        //ssh_command("sudo virsh snapshot-delete " . $child_vms_name[0] . " " . $snapshot_name,true);
        add_SQL_line("UPDATE vms SET snapshot = 'false' WHERE id = '$vm'");
        //add_SQL_line("DELETE FROM list_snapshot WHERE vmid='$vm'");
        echo json_encode(array('success' => _('Snapshot deleted.')));
    }else{
        $child_vms = get_SQL_array("SELECT hypervisor FROM vms WHERE id='$vm'");
        $id_ini = $child_vms[0][0];
        $h_reply = get_SQL_line("SELECT * FROM hypervisors WHERE  id= '$id_ini'");
        ssh_connect($h_reply[2].":".$h_reply[3]);
        $child_vms_name = get_SQL_line("SELECT name FROM vms WHERE id='$vm'");
        $snapshot_name = "'" . uniqid() . "'";
        $snapshot_cm = "sudo virsh snapshot-create-as " . $child_vms_name[0] . " --name " . $snapshot_name . " --atomic";
        ssh_command($snapshot_cm,true);
        add_SQL_line("UPDATE vms SET snapshot = 'true' WHERE id = '$vm'");
        add_SQL_line("INSERT INTO list_snapshot(name,vmid) VALUES($snapshot_name,'$vm')");
        echo json_encode(array('success' => _('Snapshot created.')));
        //echo json_encode(array('success' => _($snapshot_name)));
    }
}
exit;
