<?php
/**
 * 超级标签(拼接查询 sql)
 * 
 * @param  $ <type> $params
 * @param  $ <type> $smarty
 * @return array /int
 */
function smarty_function_sq_query( $params, $smarty )
{
    global $_G;
    
	extract( $params ); 	
	// 连接池
	$_pool = 'www';
	if ( !is_null( $pool ) && strlen( $pool ) > 0 ) {
	    $_pool = $pool;
	}
	
	$_table = "article";
	if ( !is_null( $table ) && strlen( $table ) > 0 ) {
	    $_table = $table;
	}
	//数据连接对象(创建模型实例时赋值给全局变量$_G)
	if (isset($_G['object'][$_pool])) {
	    $_dbo = $_G['object'][$_pool];
	}else {
	    $_dbo = isset($_G['object'][$_table]) ? $_G['object'][$_table] : $_G['object']['default'];
	}
		
	$_call = 'get_results';
	if ( !is_null( $call ) && in_array( $call, array( 'get_results', 'get_row', 'get_var','fetch_sql' ) ) ) {
		$_call = $call;
	} 

	$_vlist = '_LIST';
	$_vlcnt = '_LCNT';
	if ( !is_null( $variable ) || strlen( $variable ) > 0 ) {
		list( $__vlist, $__vlcnt ) = explode( ',', $variable );
		$_vlist = ( strlen( $__vlist ) > 0 ) ? $__vlist : $_vlist;
		$_vlcnt = ( strlen( $__vlcnt ) > 0 ) ? $__vlcnt : $_vlcnt;
	} 

	$_columns = "*";
	if ( !is_null( $columns ) && strlen( $columns ) > 0 ) {
		$_columns = $columns;
	} 
    
	
	$_sql  = "SELECT " . $_columns . " FROM " . $_table;
	$_csql = "SELECT COUNT(1) FROM " . $_table;
	$_psql = '';
	if ( !is_null( $join ) && strlen( $join ) > 0 ) {
		$_psql .= " {$join}";
	} 
	if ( !is_null( $on ) && strlen( $on ) > 0 ) {
		$_psql .= " ON {$on}";
	} 
	if ( !is_null( $conditions ) && strlen( $conditions ) > 0 ) {
		$_psql .= " WHERE {$conditions}" ;
	} 
	if ( !is_null( $group ) && strlen( $group ) > 0 ) {
		$_psql .= " GROUP BY {$group}" ;
	} 
	if ( !is_null( $order ) && strlen( $order ) > 0 ) {
		$_psql .= " ORDER BY {$order}" ;
	} 

	$_csql .= $_psql; // 统计总记录数(可用于分页)         
	// 查询条数限制 start
	$_pno = 1;
	if ( !is_null( $pno ) && strlen( $pno ) > 0 ) {
		$_pno = ( int )$pno;
	} 
	$_psize = 20;
	if ( !is_null( $psize ) && strlen( $psize ) > 0 ) {
		$_psize = ( int )$psize;
	} 
	if ( $_pno < 1 ) {
		$_pno = 1;
	} 
	$_start = ( $_pno - 1 ) * $_psize;
	$_psql .= " LIMIT {$_start},{$_psize}"; 
	// 查询条数限制 end
	$_sql .= $_psql;	
	
	$_RESULT = array(); // 结果集
	$_LCNT = 0; // 数量
	if ( method_exists( $_dbo, $_call ) ) {
		if ( 'get_var' == $_call ) {
			$_RESULT = $_dbo->$_call( $_sql );
		} else { // 一维数组或二维数组
			$_RESULT = $_dbo->$_call( $_sql, ARRAY_A );
			$_LCNT = $_dbo->get_var( $_csql ); // 总条数
		} 
	} else { // 本地调试
		return $_sql;
	} 
	
	$smarty->assign( $_vlist, $_RESULT );
	$smarty->assign( $_vlcnt, $_LCNT );
} 

/**
 * 指定分类文章ID的上一篇和下一篇标签
 * 
 * @param  $ <type> $params
 * @param  $ <type> $smarty
 * @return array 
 */
function smarty_function_sq_apn( $params, $smarty )
{
    global $_G;
    
	extract( $params ); 
	
	// 连接池
	$_pool = 'www';
	if ( !is_null( $pool ) && strlen( $pool ) > 0 ) {
	    $_pool = $pool;
	}
	
	$_table = "article";
	if ( !is_null( $table ) && strlen( $table ) > 0 ) {
	    $_table = $table;
	}	
	//数据连接对象(创建模型实例时赋值给全局变量$_G)
	if (isset($_G['object'][$_pool])) {
	    $_dbo = $_G['object'][$_pool];
	}else {
	    $_dbo = isset($_G['object'][$_table]) ? $_G['object'][$_table] : $_G['object']['default'];
	}
	
	$_vprow = '_PROW';
	$_vnrow = '_NROW';
	if ( !is_null( $variable ) || strlen( $variable ) > 0 ) {
		list( $__vprow, $__vnrow ) = explode( ',', $variable );
		$_vprow = ( strlen( $__vprow ) > 0 ) ? $__vprow : $_vprow;
		$_vnrow = ( strlen( $__vnrow ) > 0 ) ? $__vnrow : $_vnrow;
	}
	
	$_format = "SELECT DISTINCT article.*, article_content.`CONTENT`, article_category.`CID` FROM article_category LEFT JOIN article ON article_category.AID = article.ID LEFT JOIN article_content ON article_category.AID = article_content.AID WHERE article.`STATE` = 1 AND article_category.`CID` = %d AND article.ID %s %d ORDER BY article.`SORT_DATE` %s LIMIT 1";

	$_PRESULT = array(); // 上一篇结果集
	$_NRESULT = array(); // 下一篇结果集
	
	if ( $cid && $aid && is_object( $_dbo ) ) {
	    $_psql = sprintf( $_format, $cid, '<', $aid, 'DESC' ); // 上一篇
	    $_nsql = sprintf( $_format, $cid, '>', $aid, 'ASC' ); // 下一篇
	}
	
	if (function_exists($_dbo,'get_row')) {
	    $_PRESULT = $_dbo->get_row( $_psql, ARRAY_A );
	    $_NRESULT = $_dbo->get_row( $_nsql, ARRAY_A );
	}else {
	    return array($_psql,$_nsql);
	}
	
	$smarty->assign( $_vprow, $_PRESULT );
	$smarty->assign( $_vnrow, $_NRESULT );
} 

/**
 * 生成页面翻页条
 * 
 * @param  $ <type> $params
 * @param  $ <type> $smarty
 * @return string 
 */
function smarty_function_sq_pbar( $params, $smarty )
{
    global $_G;
    
	extract( $params );
	$_vbar = '_BAR';
	if ( !is_null( $variable ) || strlen( $variable ) > 0 ) {
		list( $__vbar ) = explode( ',', $variable );
		$_vbar = ( strlen( $__vbar ) > 0 ) ? $__vbar : $_vbar;
	} 

	$_pno = 1; // 当前页码
	if ( !is_null( $pno ) && strlen( $pno ) > 0 ) {
		$_pno = ( int )$pno;
	} 
	if ( $_pno < 1 ) {
		$_pno = 1;
	} 

	$_psize = 20; // 每页显示数量
	if ( !is_null( $psize ) && strlen( $psize ) > 0 ) {
		$_psize = ( int )$psize;
	} 

	$_lcnt = 0; // 列表总记录数
	if ( !is_null( $lcnt ) && strlen( $lcnt ) > 0 ) {
		$_lcnt = ( int )$lcnt;
	} 

	$_pmax = 0; // 最大页码显示限制(一般不用限制)
	if ( !is_null( $pmax ) && strlen( $pmax ) > 0 ) {
		$_pmax = ( int )$pmax;
	} 
    	
	$make = $_G['object']['make_html'];

	$_BAR = $make->categoryNavBar( $_lcnt, $_psize, $_pno, $_pmax );
	$smarty->assign( $_vbar, $_BAR );
} 

/**
 * 获取分类信息标签
 * 
 * @param  $ <type> $params
 * @param  $ <type> $smarty
 * @return array 
 */
function smarty_function_sq_pcate( $params, $smarty )
{
    global $_G;
    
	extract( $params );
	$_vcate = '_CATE';
	if ( !is_null( $variable ) || strlen( $variable ) > 0 ) {
		list( $__cate ) = explode( ',', $variable );
		$_vcate = ( strlen( $__cate ) > 0 ) ? $__cate : $_vcate;
	} 

	$_cid = 0; // 分类ID
	if ( !is_null( $cid ) && strlen( $cid ) > 0 ) {
		$_cid = ( int )$cid;
	} 

	$_CATE = null;
	if ( $_cid ) {
		$make = $_G['object']['make_html'];
		$_CATE = $make->getCategory( $_cid );
	} 
	$smarty->assign( $_vcate, $_CATE );
} 

/**
 * 获取导航
 * 
 * @param  $ <type> $params
 * @param  $ <type> $smarty
 * @return string 
 */
function smarty_function_sq_pnav( $params, $smarty )
{
    global $_G;
    
	extract( $params );
	$_vnav = '_NAV';
	if ( !is_null( $variable ) || strlen( $variable ) > 0 ) {
		list( $__vnav ) = explode( ',', $variable );
		$_vnav = ( strlen( $__vnav ) > 0 ) ? $__vnav : $_vnav;
	} 

	$_cid = 0; // 分类ID
	if ( !is_null( $cid ) && strlen( $cid ) > 0 ) {
		$_cid = ( int )$cid;
	} 

	$_NAV = null;
	if ( $_cid ) {
	    $make = $_G['object']['make_html'];
	    
		$_NAV = $make->getCategoryNav( $_cid );
	} 
	$smarty->assign( $_vnav, $_NAV );
} 

/**
 * 获取链接(分类或文章)
 * 
 * @param  $ <type> $params
 * @param  $ <type> $smarty
 * @return string 
 */
function smarty_function_sq_purl( $params, $smarty )
{
    global $_G;
    
	extract( $params );
	$_vurl = '_URL';
	if ( !is_null( $variable ) || strlen( $variable ) > 0 ) {
		list( $__vurl ) = explode( ',', $variable );
		$_vurl = ( strlen( $__vurl ) > 0 ) ? $__vurl : $_vurl;
	} 

	$_cid = 0; // 分类ID
	if ( !is_null( $cid ) && strlen( $cid ) > 0 ) {
		$_cid = ( int )$cid;
	} 

	$_att = 1; // 属性
	if ( !is_null( $att ) && strlen( $att ) > 0 ) {
		$_att = ( int )$att;
	} 

	$_no = 1; // 页码或文章ID
	if ( !is_null( $no ) && strlen( $no ) > 0 ) {
		$_no = ( int )$no;
	} 

	$_tpl = null; // 强制使用模板
	if ( !is_null( $tpl ) && strlen( $tpl ) > 0 ) {
		$_tpl = $tpl;
	} 
    
	// 强制文件名
	if ( !is_null( $falias ) && strlen( $falias ) > 0 ) {
		$_no = $falias;
	} 

	$_URL = null;
	if ( $_cid ) {
	    $make = $_G['object']['make_html'];
		list( , , $_URL ) = $make->getPath( $_cid, $_att, $_no, $_tpl );
	} 
	$smarty->assign( $_vurl, $_URL );
} 

/**
 * 测试自定义标签
 * 
 * @param  $ <type> $params
 * @param  $ <type> $smarty
 * @return <type>
 */
function smarty_function_sq_test( $params, $smarty )
{
    
	extract( $params );
	$_vvar = '_VAR';
	if ( !is_null( $variable ) || strlen( $variable ) > 0 ) {
		list( $__vvar ) = explode( ',', $variable );
		$_vvar = ( strlen( $__vvar ) > 0 ) ? $__vvar : $_vvar;
	} 
	$_RESULT = 'Link:' . date( 'Y-m-d' );
	$smarty->assign( $_vvar, $_RESULT );
}

?>