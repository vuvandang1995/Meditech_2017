function statusChecker(poolid){
    if (retries && !vm_booted){
//  console.log("checking");
    call_vm(poolid)
    }
    retries--;
}
    //$('.pools').click(function() {
    $(document).delegate(".pools","click",function(){
    $('#loadingVM').modal('show');
    if (!html5_client){
        document.title = ""
        document.title = "kvm-vdi-msg:" + $(this).attr('id')
    }
    else{
        heatbet_enabled=0;
        vm_booted=0;
        retries=4;
        var pool= $(this).attr('id');
        call_vm(pool);
        checker_object = setInterval(function(){ statusChecker(pool);}, 4000); //since ajax calls are asyncronous, we need to make some kind of scheduler for them not to be called at once
    }
    })
//    $('.shutdown').click(function() {
    $(document).delegate(".shutdown","click",function(){
    if (!html5_client){
        document.title = ""
        document.title = "kvm-vdi-msg:PM:shutdown:" + $(this).attr('id');
    }
    else {
        $.ajax({
            type : 'POST',
            url : 'client_power.php',
            engine: engine,
            data: {
            'vm': $(this).attr('id'),
            'action': 'shutdown',
            }
        });
    }
    })
//    $('.terminate').click(function() {
    $(document).delegate(".terminate","click",function(){
    if (!html5_client){
        document.title = ""
        document.title = "kvm-vdi-msg:PM:destroy:" + $(this).attr('id')
    }
    else {
        $.ajax({
            type : 'POST',
            url : 'client_power.php',
            data: {
            'vm': $(this).attr('id'),
            'action': 'destroy',
            }
        });
    }
    })
})


function statusChecker(poolid){
    if (retries && !vm_booted){
//  console.log("checking");
    call_vm(poolid)
    }
    retries--;
}
    //$('.pools').click(function() {
    $(document).delegate(".pools","click",function(){
    $('#loadingVM').modal('show');
    if (!html5_client){
        document.title = ""
        document.title = "kvm-vdi-msg:" + $(this).attr('id')
    }
    else{
        heatbet_enabled=0;
        vm_booted=0;
        retries=4;
        var pool= $(this).attr('id');
        call_vm(pool);
        checker_object = setInterval(function(){ statusChecker(pool);}, 4000); //since ajax calls are asyncronous, we need to make some kind of scheduler for them not to be called at once
    }
    })
//    $('.shutdown').click(function() {
    $(document).delegate(".shutdown","click",function(){
    if (!html5_client){
        document.title = ""
        document.title = "kvm-vdi-msg:PM:shutdown:" + $(this).attr('id');
    }
    else {
        $.ajax({
            type : 'POST',
            url : 'client_power.php',
            engine: engine,
            data: {
            'vm': $(this).attr('id'),
            'action': 'shutdown',
            }
        });
    }
    })
//    $('.terminate').click(function() {
    $(document).delegate(".terminate","click",function(){
    if (!html5_client){
        document.title = ""
        document.title = "kvm-vdi-msg:PM:destroy:" + $(this).attr('id')
    }
    else {
        $.ajax({
            type : 'POST',
            url : 'client_power.php',
            data: {
            'vm': $(this).attr('id'),
            'action': 'destroy',
            }
        });
    }
    })
})