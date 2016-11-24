$(function() {
    openPwd();
    $('#editpass').click(function() {
        $('#w').window('open');
    });
    $('#btnEp').click(function() {
        updatePwd();
    })
    $('#btnCancel').click(function() {
        closePwd();
    })
    $('#loginOut').click(function() {
        $.messager.confirm('系统提示', '您确定要退出本次登录吗?',
        function(r) {
            if (r) {
            	$.ajax({ 
            		url:'/login/_logout/',
            		type:'get',
            		dataType:'json',
            		success:function(data){ 
            			var state = data.ack;
            			if(parseInt(state,10) == 1){ 
                			location.href = data.data.url;
            			}
            		}
            	});
            }
        });
    })
    $('#refreshAuth').click(function(){
        $.ajax({
            url:'/user/_refresh/',
            type:'get',
            dataType:'json',
            success:function(data){ 
                if(data.state == 1){ 
                    parent.location.reload();
                }
				alert(data.msg);
            }
        })
    });        
});

//设置登录窗口
var openPwd = function openPwd() {
    $('#w').window({
        title: '修改密码',
        modal: true,
        shadow: true,
        closed: true,
        resizable: false
    });
}

//关闭登录窗口
var closePwd = function closePwd() {
    $('#w').window('close');
}

//修改密码
var updatePwd = function updatePwd() {
    var $oldPass = $('#txtOldPass');
    var $newpass = $('#txtNewPass');
    var $rePass = $('#txtRePass');                
    if ($oldPass.val() == '') {
        $.messager.alert('系统提示', '请输入原密码！', 'warning');
        return false;
    }                
    if ($newpass.val() == '') {
        $.messager.alert('系统提示', '请输入密码！', 'warning');
        return false;
    }
    if ($rePass.val() == '') {
        $.messager.alert('系统提示', '请在一次输入密码！', 'warning');
        return false;
    }
    if ($newpass.val() != $rePass.val()) {
        $.messager.alert('系统提示', '两次密码不一至！请重新输入', 'warning');
        return false;
    }
    var data={ 
    	oldPassword:$.trim($oldPass.val()),
    	newPassword:$.trim($rePass.val())
    };
    $.ajax({ 
    	url:'/user/_updatePassWord/',
    	dataType:'json',
    	type:'post',
    	data:data,
    	success:function(data){ 
    		$.messager.show({
                title: '提示消息',
                msg: data.msg,
                timeout: 3000,
                showType: 'slide'
            });
    	}
    });
   
    $newpass.val('');
    $rePass.val('');
    $('#w').window('close');
}