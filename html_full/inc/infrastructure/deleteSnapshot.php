<?php
include ('../../functions/config.php');
require_once('../../functions/functions.php');
if (!check_session()){
    echo json_encode(array('error' => _('Please login first')));
    exit;
}
slash_vars();
$type='';
$credential_type='';
if (isset($_POST['credential_type']))
    $credential_type = $_POST['credential_type'];
if (isset($_POST['type']))
    $type=$_POST['type'];

if ($type == 'delete'){
    $id=$_POST['id'];
    $credid = $_POST['credid'];

    foreach ($credid as $id){
        $dem=1;

        $idvm1=get_SQL_array("SELECT vmid from list_snapshot where id='$id'");
        $idvm=$idvm1[0][0];
        $child_vms = get_SQL_array("SELECT hypervisor FROM vms WHERE id='$idvm'");
        $id_ini = $child_vms[0][0];
        $h_reply = get_SQL_line("SELECT * FROM hypervisors WHERE  id= '$id_ini'");
        ssh_connect($h_reply[2].":".$h_reply[3]);
        $child_vms_name = get_SQL_line("SELECT name FROM vms WHERE id='$idvm'");
        ssh_command("sudo virsh shutdown " . $child_vms_name[0], true);
        $snapshot_name=get_SQL_line("SELECT name FROM list_snapshot WHERE id='$id'");
        ssh_command("sudo virsh snapshot-delete " . $child_vms_name[0] . " " . $snapshot_name[0],true);

        add_SQL_line("DELETE FROM list_snapshot WHERE id='$id' LIMIT 1");
    }
    //echo json_encode(array('success' => _($snapshot_name[0])));
    echo json_encode(array('success' => _('Updated successfully')));
    exit;
}
