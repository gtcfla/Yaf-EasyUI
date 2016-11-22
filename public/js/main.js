var clearForm = function() {
    $("#m_form").form("clear");
}

var openAdd = function() {
    $("#m_div").dialog({
        title:"新增",
        iconCls:"icon-add"
    });
    $("#m_div").dialog("open");
}

var setEditTitle = function() {
    $("#m_div").dialog({
        title:"编辑",
        iconCls:"icon-edit"
    });
}
/**
一、data是一个对象，包含state,msg等属性
二、state=1时弹出提示信息(界面右下角显示)，state=0时弹出错误框(界面中间位置显示)
*/
var showMsg = function(data) {
    if (data.ack == 1)
    {
    	$.messager.show({
            title:"提示消息",
            msg:data.msg,
            timeout:2500,
            showType:"slide"
        });
        
    }else {
    	$.messager.alert("操作提示", data.msg,"error");
    }   
}