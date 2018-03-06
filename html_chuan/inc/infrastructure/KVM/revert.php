<?php
include dirname(__FILE__) . '/../../../functions/config.php';
require_once(dirname(__FILE__) . '/../../../functions/functions.php');
if (!check_session()){
    echo json_encode(array('error' => _('Please login first')));
    exit;
}
slash_vars();
$snapshotid = $_POST['snapshotid'];
if (empty($snapshotid)){
    echo json_encode(array('error' => _('Missing source VM.')));
    exit;
}else{
    $hihi = get_SQL_line("SELECT vmid FROM list_snapshot WHERE id='$snapshotid'");
    $vm=$hihi[0];
    $child_vms = get_SQL_array("SELECT hypervisor FROM vms WHERE id='$vm'");
    $id_ini = $child_vms[0][0];
    $h_reply = get_SQL_line("SELECT * FROM hypervisors WHERE  id= '$id_ini'");
    ssh_connect($h_reply[2].":".$h_reply[3]);
    $child_vms_name = get_SQL_line("SELECT name FROM vms WHERE id='$vm'");
    ssh_command("sudo virsh shutdown " . $child_vms_name[0], true);
    $haha = get_SQL_line("SELECT name FROM list_snapshot WHERE id='$snapshotid'");
    $snapshot_name = $haha[0];
    ssh_command("sudo virsh snapshot-revert " . $child_vms_name[0] . " --snapshotname " . $snapshot_name);
    date_default_timezone_set('Asia/Ho_Chi_Minh');
    $xxx=date("Y-m-d H:i:s");
    add_SQL_line("UPDATE list_snapshot SET datelog = '$xxx' WHERE id='$snapshotid'");
    $not = get_SQL_array("SELECT id FROM list_snapshot WHERE status='1'");
    if (!empty($not)){
        $z=0;
        while ($z < sizeof($not)) {
            $idsnap=$not[$z][0];
            add_SQL_line("UPDATE list_snapshot SET status = '0' WHERE id='$idsnap'");
            ++$z;
        }
    }
    add_SQL_line("UPDATE list_snapshot SET status = '1' WHERE id='$snapshotid'");
    }
echo json_encode(array('success' => _('Machine reverted.')));
exit;
