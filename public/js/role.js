var $tgrid = $("#d_table"), $rigthGrid = $("#role_grid"), $userGrid = $("#user_grid"), grantTree, $tfrid = $("#m_frid");
var $sourceFrid = $("#m_s_frid"),$targetFrid = $("#m_t_frid");

$(function() {
    $tgrid.treegrid({
        remoteSort:false,
        url:"/role/_roleTree/",
        idField:"id",
        treeField:"text",
        columns:[ [ {
            field:"text",
            title:"名称",
            width:200,
            align:"left"
        }, {
            field:"create_time",
            title:"创建时间",
            width:150,
            align:"center",
            sortable:true,
            sorter:function(a, b) {
                return a > b ? 1 :-1;
            }
        }, {
            field:"update_time",
            title:"修改时间",
            width:150,
            align:"center",
            sortable:true,
            sorter:function(a, b) {
                return a > b ? 1 :-1;
            }
        }, {
            field:"operate",
            title:"操作",
            width:50,
            align:"center",
            formatter:function(value, rec) {
                return "<a href='javascript:;' class='role_edit' rid='" + rec.id + "' >编辑</a>";
                
            }
        } ] ],
        onClickRow:function(index, row) {
            var $layout = $("#rolepage");
            
            var south = $layout.layout("panel", "east");
            if (south.panel("options").collapsed) {
                $layout.layout("expand", "east");
            }
            
            var $selected = $tgrid.treegrid("getSelected");
            grantTree.tree({
                url:"/menu/_queryTree/?role_id=" + $selected.id,
                checkbox:true,
                lines:true,
                cascadeCheck:false, //false
            });
            $userGrid.datagrid({
                url:"/user/_query/",
                queryParams:{
                    role_id:$selected.id
                }
            });
        },
        onLoadSuccess:function(row, data) {
            var $bodyView = $tgrid.data("treegrid").dc.view2;
            $bodyView.find("a.role_edit").click(function(e) {
                e.stopPropagation();
                var rid = $(this).attr("rid");
                updateDialog(rid);
            });
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
            //权限复制的源角色
            $sourceFrid.combotree({
                data:data,
                valueField:"id",
                textField:"text",
                onLoadSuccess:function() {
                    $sourceFrid.combotree("setValue", "1");
                }
            });
            //权限复制的目标角色
            $targetFrid.combotree({
                data:data,
                valueField:"id",
                textField:"text",
                onLoadSuccess:function() {
                    $targetFrid.combotree("setValue", "1");
                }
            });
        }
    });
    $rigthGrid.datagrid({
        fit:true,
        showHeader:false,
        toolbar:[ {
            text:"授权",
            iconCls:"icon-add",
            handler:toGrant
        } ]
    });
    grantTree = $("<ul/>");
    $rigthGrid.data().datagrid.dc.body2.append(grantTree);
    $userGrid.datagrid({
        remoteSort:false,
        columns:[ [ {
            field:"rolename",
            title:"角色",
            width:100,
            align:"center"
        }, {
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
        },{
            field:"create_time",
            title:"注册时间",
            width:120,
            align:"center",
            sortable:true,
            sorter:function(a, b) {
                return a > b ? 1 :-1;
            }
        }, {
            field:"state",
            title:"状态",
            width:100,
            align:"center",
            iconCls:"icon-help",
            formatter:function(value, rec) {
                if (rec.state == 1) {
                    return "<span class='l-btn-text icon-ok' style='padding-left: 20px;'>&nbsp;</span>";
                } else {
                    return "<span class='l-btn-text icon-no' style='padding-left: 20px;'>&nbsp;</span>";
                }
            }
        } ] ],
        pageSize:20,
        pageList:[ 10, 20, 30, 40, 50 ]
    });
});
/* 授权 */
var toGrant = function() {
        var ckd = grantTree.tree("getChecked", [ "checked", "indeterminate" ]);
        //var ckd = grantTree.tree("getChecked", [ "checked"]);
        var fmidArr = [];
        var selected = $tgrid.treegrid("getSelected");
        var formData = {};
        if (selected) {
            formData.FRID = selected.id;
            if (ckd.length > 0) {
                for (var i = 0, len = ckd.length; i < len; i++) {
                    fmidArr.push(ckd[i].id);
                }
                formData.FMID = fmidArr;
                $.ajax({
                    url:"/role/_updateRoleMenu/",
                    type:"POST",
                    dataType:"json",
                    data:formData,
                    success:function(data) {
                        showMsg(data);
                    }
                });
            } else {
                showMsg({ack:1,msg:"请选择授权的权限！"});
            }
        } else {
            showMsg({ack:1,msg:"请选择修改的记录！"});
        }
    }
/* 编辑用户 */
var updateDialog = function(_id) {
    if (!_id) {
        showMsg({ack:1,msg:"编辑的记录ID无效！"});
    } else {
        var queryParam = {
            id:_id,
            page:1
        };
        $.ajax({
            url:"/role/_query/",
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
                        name:_data.text,
                        sort:_data.sort
                    });
                    $tfrid.combotree("setValue", _data.pid);
                    setEditTitle();
                    $("#m_div").dialog("open");
                }
            },
            error:function() {}
        });
    }
}

/* 提交表单 */
var submitForm = function () {
    if ($("#m_form").form("validate")) {
        var id = $("#m_id").val();
        var isUpdate = !!id;
        var postUrl = isUpdate ? "/role/_update/" :"/role/_add/";
        var formData = $("#m_form").serialize();
        var postRoleData = function() {
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
        };
        postRoleData();
    }
}

var openCopy = function() {
    $("#m_copy_div").dialog({
        title:"权限复制",
        iconCls:"icon-add"
    });
    $("#m_copy_div").dialog("open");
}

var submitCopyPrevilege = function() {
    if ($("#m_copy_form").form("validate")) {        
        var postUrl = "/role/_copyPrevilege/";
        var formData = $("#m_copy_form").serialize();
        var postRoleData = function() {
            $.ajax({
                url:postUrl,
                type:"POST",
                async:true,
                data:formData,
                dataType:"json",
                success:function(data) {
                    showMsg(data);
                    $("#m_copy_div").dialog("close");
                    $sourceFrid.combotree("reload");
                    $targetFrid.combotree("reload");
                },
                error:function() {}
            });
        };
        postRoleData();
    }
}
