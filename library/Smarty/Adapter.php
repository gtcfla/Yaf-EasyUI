<?php
/**
 * Smarty.php
 * 
 * @author Laruence 
 * @date 2010-08-01 14:15
 * @version $Id$
 */
/*
require_once "sysplugins/smarty_resource.php";
require_once "sysplugins/smarty_internal_templatecompilerbase.php";
require_once "sysplugins/smarty_internal_templateparser.php";
require_once "sysplugins/smarty_internal_compilebase.php";
require_once "sysplugins/smarty_internal_write_file.php";
require_once "sysplugins/smarty_internal_templatelexer.php";
require_once "sysplugins/smarty_internal_resource_string.php";
require_once "Smarty.class.php";
*/

define('SMARTY_PATH', __DIR__);
define('SMARTY_SYSPLUGINS_DIR', __DIR__.DIRECTORY_SEPARATOR.'sysplugins'.DIRECTORY_SEPARATOR);
Yaf_Loader::import( SMARTY_PATH."/Smarty.class.php");
Yaf_Loader::import( SMARTY_SYSPLUGINS_DIR ."smarty_resource.php");

Yaf_Loader::import( SMARTY_SYSPLUGINS_DIR ."smarty_internal_templatecompilerbase.php");
Yaf_Loader::import( SMARTY_SYSPLUGINS_DIR . "smarty_internal_templatelexer.php");
Yaf_Loader::import( SMARTY_SYSPLUGINS_DIR . "smarty_internal_templateparser.php");
Yaf_Loader::import( SMARTY_SYSPLUGINS_DIR . "smarty_internal_compilebase.php");
Yaf_Loader::import( SMARTY_SYSPLUGINS_DIR . "Smarty_Internal_Compile_Private_Print_Expression.php");

Yaf_Loader::import( SMARTY_SYSPLUGINS_DIR . "smarty_internal_compile_private_modifier.php");
Yaf_Loader::import( SMARTY_SYSPLUGINS_DIR . "smarty_internal_write_file.php");
Yaf_Loader::import( SMARTY_SYSPLUGINS_DIR . "smarty_internal_resource_string.php");



class Smarty_Adapter implements Yaf_View_Interface {
	/**
	 * Smarty object
	 * 
	 * @var Smarty 
	 */
	public $_smarty;

	/**
	 * Constructor
	 * 
	 * @param string $tmplPath 
	 * @param array $extraParams 
	 * @return void 
	 */
	public function __construct( $tmplPath = null, $extraParams = array() )
	{
		$this->_smarty = new Smarty;

		if ( null !== $tmplPath ) {
			$this->setScriptPath( $tmplPath );
		} 
		foreach ( $extraParams as $key => $value ) {
			$this->_smarty->$key = $value;
		}
		return $this->_smarty;
	}
	
	public function getSmarty()
	{
	    $this->_smarty;
	}

	/**
	 * Return the template engine object
	 * 
	 * @return Smarty 
	 */
	public function getEngine()
	{
		return $this->_smarty;
	} 

	/**
	 * Set the path to the templates
	 * 
	 * @param string $path The directory to set as the path.
	 * @return void 
	 */
	public function setScriptPath( $path )
	{
		if ( is_readable( $path ) ) {
			$this->_smarty->template_dir = $path;
			return;
		} 

		throw new Exception( 'Invalid path provided' );
	} 

	/**
	 * Retrieve the current template directory
	 * 
	 * @return string 
	 */
	public function getScriptPath()
	{
		return $this->_smarty->template_dir;
	} 

	/**
	 * Alias for setScriptPath
	 * 
	 * @param string $path 
	 * @param string $prefix Unused
	 * @return void 
	 */
	public function setBasePath( $path, $prefix = 'Zend_View' )
	{
		return $this->setScriptPath( $path );
	} 

	/**
	 * Alias for setScriptPath
	 * 
	 * @param string $path 
	 * @param string $prefix Unused
	 * @return void 
	 */
	public function addBasePath( $path, $prefix = 'Zend_View' )
	{
		return $this->setScriptPath( $path );
	} 

	/**
	 * Assign a variable to the template
	 * 
	 * @param string $key The variable name.
	 * @param mixed $val The variable value.
	 * @return void 
	 */
	public function __set( $key, $val )
	{
		$this->_smarty->assign( $key, $val );
	} 

	/**
	 * Allows testing with empty() and isset() to work
	 * 
	 * @param string $key 
	 * @return boolean 
	 */
	public function __isset( $key )
	{
		return ( null !== $this->_smarty->get_template_vars( $key ) );
	} 

	/**
	 * Allows unset() on object properties to work
	 * 
	 * @param string $key 
	 * @return void 
	 */
	public function __unset( $key )
	{
		$this->_smarty->clear_assign( $key );
	} 

	/**
	 * Assign variables to the template
	 * 
	 * Allows setting a specific key to the specified value, OR passing
	 * an array of key => value pairs to set en masse.
	 * 
	 * @see __set
	 * @param string $ |array $spec The assignment strategy to use (key or
	 * array of key => value pairs)
	 * @param mixed $value (Optional) If assigning a named variable,
	 * use this as the value.
	 * @return void 
	 */
	public function assign( $spec, $value = null )
	{
		if ( is_array( $spec ) ) {
			$this->_smarty->assign( $spec );
			return;
		} 

		$this->_smarty->assign( $spec, $value );
	} 

	/**
	 * Clear all assigned variables
	 * 
	 * Clears all variables assigned to Zend_View either via
	 * {@link assign()} or property overloading
	 * ({@link __get()}/{@link __set()}).
	 * 
	 * @return void 
	 */
	public function clearVars()
	{
		$this->_smarty->clear_all_assign();
	} 

	/**
	 * Processes a template and returns the output.
	 * 
	 * @param string $name The template to process.
	 * @return string The output.
	 */
	public function render( $name, $value = null )
	{
		return $this->_smarty->fetch( $name );
	} 

	public function display( $name = null, $value = null )
	{
		$path = "";

		if ( empty( $name ) ) {
			$dispatcher = Yaf_Dispatcher::getInstance();
			$arrRequest = $dispatcher->getRequest();
			$path = strtolower( $arrRequest->getControllerName() ) . '/' . strtolower( $arrRequest->getActionName() ) . '.' . Yaf_Application::app()->getConfig()->application->view->ext;
		} else {
			$path = $name;
		} 
		echo $this->_smarty->fetch( $path );
	} 
	
	public function testInstall()
	{
	    return $this->_smarty->getTags();
	}
} 

/**
 * vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4:
 */
?>
