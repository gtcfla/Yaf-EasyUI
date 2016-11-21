var $grid = $("#d_table");

$(function() {
    $grid.datagrid({
        remoteSort:false,
        url:"/user/_query/",
        frozenColumns:[ [ {
            field:"ck",
            checkbox:true
        } ] ],
        columns:[ [ {
            field:"name",
            title:"帐号",
            width:100,
            align:"center",
            sortable:true,
            sorter:function(a, b) {
                return a > b ? 1 :-1;
            }
        }, {
            field:"realname",
            title:"姓名",
            width:100,
            align:"center"
        }, {
            field:"create_time",
            title:"注册时间",
            width:200,
            align:"center",
            sortable:true,
            sorter:function(a, b) {
                return a > b ? 1 :-1;
            }
        }, {
            field:"state",
            title:'<span id="statusHelp" class="l-btn-empty icon-help" style="display:inline-block;width:16px;">&nbsp;</span> 状态',
            width:70,
            align:"center",
            iconCls:"icon-help",
            formatter:function(value, rec) {
                if (rec.state == 1) {
                    return "<a href='javascript:;' onclick='stateChange(\"" + rec.id + "\",2)' ><span class='l-btn-text icon-ok' style='padding-left: 20px;'>&nbsp;</span>";
                } else {
                    return "<a href='javascript:;' onclick='stateChange(\"" + rec.id + "\",1)' ><span class='l-btn-text icon-no' style='padding-left: 20px;'>&nbsp;</span>";
                }
            }
        }, {
            field:"operate",
            title:"操作",
            width:50,
            align:"center",
            formatter:function(value, rec) {
                return "<a href='javascript:;' onclick='updateDialog(\"" + rec.id + "\")';>编辑</a>";
            }
        } ] ],
        pageSize:20,
        pageList:[ 10, 20, 30, 40, 50 ],
        onLoadSuccess:function(row, data) {
            /* 问号提示 */
            $("#statusHelp").tooltip({
                position:"top",
                content:'<span style="color:#fff">点击切换状态禁用或启用</span>',
                onShow:function() {
                    $(this).tooltip("tip").css({
                        backgroundColor:"#666",
                        borderColor:"#666"
                    });
                }
            });
        }
    });
    /* 查询 */
    $("#search").click(function() {
        var name = $("#s_name").val();
        var uname = $("#s_uname").val();
        var state = $("#s_state").combobox("getValue");
        var queryParam = {
            name:name,
            realname:uname,
            state:state,
        };
        $grid.datagrid({
            queryParams:queryParam
        });
    });
	
});

/* 判断是否已经存在用户 */
var isExistUser = function(cb) {
    var url = "/user/_query/";
    var data = {
        name:$("#m_uname").textbox("getValue")
    };
    $.ajax({
        url:url,
        type:"get",
        data:data,
        dataType:"json",
        success:function(data) {
            if (cb) {
                cb(data);
            }
        }
    });
};

/* 编辑 */
var updateDialog = function(_id) {
    $("#m_uname").textbox("disable");
    $("#m_pwd").textbox("disableValidation");
    if (!_id) {
        showMsg({ack:1,msg:"编辑的记录ID无效"});
    } else {
        var queryParam = {
            id:_id,
            page:1
        };
        $.ajax({
            url:"/user/_query/",
            type:"GET",
            async:true,
            data:queryParam,
            dataType:"json",
            success:function(data) {
                if (data.rows.length == 0) {
                    showMsg({state:1,msg:"没有找到指定记录"});
                } else {
                    var _data = data.rows[0];
                    $("#m_form").form("load", {
                        id:_data.id,
                        realname:_data.realname,						
                        name:_data.name,
                        type:_data.type,
                        create_time:_data.create_time,
                        state:_data.state
                    });
                    $("#m_div").dialog({
                        title:"编辑",
                        iconCls:"icon-edit"
                    });
                    $("#m_div").dialog("open");
                }
            },
            error:function() {}
        });
    }
};

/* 禁用、启用 */
var stateChange = function(_id, _state) {
    var operStr = _state == 0 ? "禁用" :"启用";
    $.messager.confirm(operStr, "你确定要 " + operStr + " 这条记录吗?", function(r) {
        if (r) {
            var dataParam = {};
            dataParam.ack = _state;
            dataParam.id = _id;
            $.ajax({
                url:"/user/_update/",
                type:"POST",
                async:true,
                data:dataParam,
                dataType:"json",
                success:function(data) {
                    showMsg(data);
                    $grid.datagrid("reload");
                },
                error:function() {}
            });
        }
    });
};

/* 提交表单 */
var submitForm = function() {
    if ($("#m_form").form("validate")) {
        var id = $("#m_id").val();
        var isUpdate = !!id;
        var postUrl = isUpdate ? "/user/_update/" :"/user/_add/";
        var formData = $("#m_form").serialize();
        var postUserData = function() {
            $.ajax({
                url:postUrl,
                type:"POST",
                async:true,
                data:formData,
                dataType:"json",
                success:function(data) {
                    showMsg(data);
                    $("#m_div").dialog("close");
                    $grid.datagrid("reload");
                },
                error:function() {}
            });
        };
        if (isUpdate) {
            postUserData();
        } else {
            isExistUser(function(data) {
                var total = parseInt(data.total, 10);
                if (total >= 1) {
                    //已经存在
                    showMsg({ack:1,msg:"用户已经存在"});
                    return;
                } else {
                    postUserData();
                }
            });
        }
    }
};

var userAdd = function() {
    $("#m_div").dialog({
        title:"新增",
        iconCls:"icon-add"
    });
    $("#m_uname").textbox("enable");
    $("#m_pwd").textbox("enableValidation");
    $("#m_div").dialog("open");
};

/* 批量禁用 */
var batchOperate = function(_type) {
    var ids = [];
    var rows = $grid.datagrid("getSelections");
    for (var i = 0; i < rows.length; i++) {
        ids.push(rows[i].id);
    }
    var operStr = _type == 2 ? "禁用" :"启用";
    $.messager.confirm(operStr, "你确定要 " + operStr + " 这条记录吗?", function(r) {
    	if (r){
	        if (ids.length <= 0) {
	        	showMsg({ack:1,msg:"请选择您要操作的记录"});
	        } else {
	            var dataParam = {};
	            dataParam.state = _type;
	            dataParam.id = ids;
	            $.ajax({
	                url:"/user/_update/",
	                type:"POST",
	                async:true,
	                data:dataParam,
	                dataType:"json",
	                success:function(data) {
	                    showMsg(data);
	                    $grid.datagrid("reload");
	                },
	                error:function() {}
	            });
	        }        
    	}
    	})
};