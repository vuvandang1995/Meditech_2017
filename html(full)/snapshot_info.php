<?php
/*
KVM-VDI
Tadas Ustinavičius

Vilnius University.
Center of Information Technology Development.


Vilnius,Lithuania.
2017-05-03
*/
include ('functions/config.php');
require_once('functions/functions.php');
if (!check_session()){
    header ("Location: $serviceurl/?error=1");
    exit;
}
slash_vars();
$vm=$_GET['vm'];
$hypervisor=$_GET['hypervisor'];
if (empty($vm)||empty($hypervisor)&&$engine!='OpenStack'){
    exit;
}
if ($engine=='OpenStack'){
    $v_reply=get_SQL_array("SELECT * FROM vms WHERE osInstanceId='$vm'");
    $source_machines_reply=get_SQL_array("SELECT * FROM vms WHERE (machine_type='sourcemachine' OR machine_type='initialmachine') AND id<>'$vm' ORDER BY name");
}
else {
    $h_reply=get_SQL_line("SELECT * FROM hypervisors WHERE id='$hypervisor'");
    $v_reply=get_SQL_array("SELECT * FROM vms WHERE id='$vm'");
    $source_machines_reply=get_SQL_array("SELECT * FROM vms WHERE hypervisor='$hypervisor' AND (machine_type='sourcemachine' OR machine_type='initialmachine') AND id<>'$vm'  ORDER BY name");
}


if(isset ($_GET['credential_type']))
    $credential_type=$_GET['credential_type'];
set_lang();

if ($credential_type=='snapshot')
    $cred_reply=get_SQL_array("SELECT * FROM list_snapshot ORDER BY name");

$list_snapshot = get_SQL_array("SELECT * FROM list_snapshot WHERE vmid='$vm'");
?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <script src="inc/js/kvm-vdi.js"></script>
</head>
<body>
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">
            <?php
                echo _("Manage snapshot");
            ?>
            </h4>
        </div>
        <div class="modal-body">
            <div class="row pre-scrollable credential-list-div">
                <div class="col-md-1"></div>
                <div class="col-md-10">
                    <div class="row">
                        <div class="col-md-6 users-line">
                            <?php echo _("Snapshot name");?>
                        </div>
                        <div class="col-md-6 users-line"></div>
                    </div>
<?php
                    $x=0;
                    while ($x<sizeof($list_snapshot)){
                        echo '<div class="row snapshots-list" id="row-snapshot_name-' . $list_snapshot[$x]['id']  . '">
                                <div class="col-md-5 snapshots-line snapshot_name-' . $list_snapshot[$x]['id']  . '">' . $list_snapshot[$x]['name'] . '</div>
                                <div class="col-md-7 snapshots-line">
                                    <input class="hide" type="checkbox" snapshot_name="users[]" value="' . $list_snapshot[$x]['id']  . '" id="snapshots-' . $list_snapshot[$x]['id']  . '">';
                                    echo '<button type="button" class="btn btn-warning DeleteSnapshotButton"  data-id="' . $list_snapshot[$x]['id']  . '"><i class="fa fa-trash-o fa-lg fa-fw"></i>' . _("Delete") . '</button>';
                                    echo '<button type="button" class="btn btn-primary RevertSnapshotButton"  data-id="' . $list_snapshot[$x]['id']  . '"><i class="fa fa-trash-o fa-lg fa-fw"></i>' . _("Revert") . '</button>';
                                    if ($list_snapshot[$x]['status'] == '1'){
                                        echo '<button><span data-id="' . $list_snapshot[$x]['id'] . '" class="glyphicon showstatus">&#xe013;</span></button>';
                                    }else{
                                        echo '<button><span data-id="' . $list_snapshot[$x]['id'] . '" class="glyphicon glyphicon-remove showstatus1"></span></button>';
                                    }

                                    if ($list_snapshot[$x]['datelog'] != '0000-00-00 00:00:00'){
                                        echo '<p id="time-' . $list_snapshot[$x]['id'] . '" class="hide">' . $list_snapshot[$x]['datelog'] . '</p>';
                                    }
                          echo '</div>
                            </div>';
                        ++$x;
                    }?>


                </div>
                <div class="col-md-1"></div>
            </div>
        </div>


        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _("Close");?></button>
            <button type="button" class="btn btn-primary hide" id="SubmitButton"><?php echo _("Save changes");?></button>
            <input type="hidden" id="credential_type" value="<?php echo $credential_type;?>">
        </div>
    </div>
</body>
</html>