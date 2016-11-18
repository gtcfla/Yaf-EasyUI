var $tgrid = $("#d_table"), $tfrid = $("#m_frid");

$(function() {
    $tgrid.treegrid({
        remoteSort:false,
        url:"/menu/_getTree/",
        idField:"id",
        treeField:"text",
        columns:[ [ {
            field:"text",
            title:"名称",
            width:200
        }, {
            field:"controller",
            title:"控制器",
            width:100
        }, {
            field:"action",
            title:"方法",
            width:150
        }, {
            field:"display",
            title:'<span id="statusHelp" class="l-btn-empty icon-help" style="display:inline-block;width:16px;">&nbsp;</span> 状态',
            width:70,
            align:"center",
            iconCls:"icon-help",
            formatter:function(value, rec) {
                if (rec.display == 1) {
                    return "<a href='javascript:;' onclick='stateChange(\"" + rec.id + "\",0)' ><span class='l-btn-text icon-ok' style='padding-left: 20px;'>&nbsp;</span>";
                } else {
                    return "<a href='javascript:;' onclick='stateChange(\"" + rec.id + "\",1)' ><span class='l-btn-text icon-no' style='padding-left: 20px;'>&nbsp;</span>";
                }
            }
        }, {
            field:"sort",
            title:"排序",
            width:50,
            align:"center",
            sortable:true,
            sorter:function(a, b) {
                return a > b ? 1 :-1;
            }
        }, {
            field:"id",
            title:"操作",
            width:100,
            align:"center",
            formatter:function(value) {
                var ctrs =  '<a title="编辑" onclick="updateDialog(\'' + value + '\');" class="" type="update" id=' + value + ">编辑</a>" ;
                ctrs +=  '&nbsp;&nbsp;<a title="删除" onclick="deleteDialog(\'' + value + '\');" class="" type="delete" id=' + value + ">删除</a>" ;
                return ctrs;
            }
        } ] ],
        onLoadSuccess:function(row, data) {
            var concatData = [ {
                id:"0",
                text:"默认"
            } ].concat(data);
            $tfrid.combotree({
                data:concatData,
                valueField:"id",
                textField:"text",
                onLoadSuccess:function() {
                    $tfrid.combotree("setValue", "0");
                }
            });
            
             /* 问号提示 */
            $("#statusHelp").tooltip({
                position:"top",
                content:'<span style="color:#fff">点击切换状态隐藏或显示</span>',
                onShow:function() {
                    $(this).tooltip("tip").css({
                        backgroundColor:"#666",
                        borderColor:"#666"
                    });
                }
            });
        }
    });
});

/* 提交表单 */
var submitForm = function() {
    if ($("#m_form").form("validate")) {
        var id = $("#m_id").val();
        var isUpdate = !!id;
        var postUrl = isUpdate ? "/menu/_update/" :"/menu/_add/";
        var formData = $("#m_form").serialize();
        $.ajax({
            url:postUrl,
            type:"POST",
            async:true,
            data:formData,
            dataType:"json",
            success:function(data) {
                showMsg(data);
                $("#m_div").dialog("close");
                $tgrid.treegrid("reload");
                $tfrid.combotree("reload");
            },
            error:function() {}
        });
    }
};

/* 编辑用户 */
var updateDialog = function(_id) {
    if (!_id) {
        showMsg({state:1,msg:"编辑的记录ID无效"});
    } else {
        var queryParam = {
            id:_id,
        };
        $.ajax({
            url:"/menu/_query/",
            type:"GET",
            async:true,
            data:queryParam,
            dataType:"json",
            success:function(data) {
                if (data.rows.length == 0) {
                    showMsg({state:1,msg:"没有找到指定记录！"});
                } else {
                    var _data = data.rows[0];
                    $("#m_form").form("load", {
                        id:_data.id,
                        name:_data.name,
                        sort:_data.sort,
                        controller:_data.controller,
                        action:_data.action,
                        display:_data.display
                    });
                    $tfrid.combotree("setValue", _data.pid);
                    setEditTitle();
                    $("#m_div").dialog("open");
                }
            },
            error:function() {}
        });
    }
};

/* 删除菜单 */
var deleteDialog = function(_id) {
    if (!_id) {
        showMsg({state:1,msg:"记录ID无效"});
    } else {
    	$.messager.confirm("删除", "你确定要删除该菜单吗?", function(r) {
            if (r) {
                var param = {};                
                param.id = _id;
                $.ajax({
                    url:"/menu/_delete/",
                    type:"POST",
                    async:true,
                    data:param,
                    dataType:"json",
                    success:function(data) {                    	
                        showMsg(data);
                        if (data.ack == 1) {
                        	$tgrid.treegrid("reload");
                        }                        
                    },
                    error:function() {}
                });
            }
        });
    }
};

/* 禁用、启用 */
var stateChange = function(_id, _state) {
    var operStr = _state == 0 ? "隐藏" :"显示";
    $.messager.confirm(operStr, "你确定要 " + operStr + " 条记录吗?", function(r) {
        if (r) {
            var dataParam = {};
            dataParam.display = _state;
            dataParam.id = _id;
            $.ajax({
                url:"/menu/_update/",
                type:"POST",
                async:true,
                data:dataParam,
                dataType:"json",
                success:function(data) {
                    showMsg(data);
                    $tgrid.treegrid("reload");
                },
                error:function() {}
            });
        }
    });
};

//一键导入菜单
var importMenu = function() {
    $.ajax({
        url:"/menu/_refresh/",
        type:"POST",
        async:true,
        dataType:"json",
        success:function(data) {
            showMsg(data);
            $tgrid.treegrid("reload");
        },
        error:function() {}
    });
    
}