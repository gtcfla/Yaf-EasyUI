<?php
/**
 * @name ErrorController
 * @desc 错误控制器, 在发生未捕获的异常时刻被调用
 * @see http://www.php.net/manual/en/yaf-dispatcher.catchexception.php
 * @author root
 */
class ErrorController extends Yaf_Controller_Abstract {

	//从2.1开始, errorAction支持直接通过参数获取异常
	public function errorAction($exception) {
		//1. assign to view engine
		$this->getView()->assign("exception", $exception);
		//5. render by Yaf 
	}
	
		public $_config;
	
		public function init()
		{
			$this->_config = Yaf_Application::app()->getConfig();
		}
	
		/**
		 * @author liaofuqian
		 * @param Exception $exception
		 */
		public function errorAction( Exception $exception )
		{
			$errorMsg = array(
					// 预定义错误
					0 => 'CUSTOM_EXCEPTION',
					1 => 'E_ERROR',
					4096 => 'E_RECOVERABLE_ERROR',
					2 => 'E_WARNING',
					4 => 'E_PARSE',
					8 => 'E_NOTICE',
					2048 => 'E_STRICT',
					8192 => 'E_DEPRECATED',
					16 => 'E_CORE_ERROR',
					32 => 'E_CORE_WARNING',
					64 => 'E_COMPILE_ERROR',
					128 => 'E_COMPILE_WARNING',
					256 => 'E_USER_ERROR',
					512 => 'E_USER_WARNING',
					1024 => 'E_USER_NOTICE',
					16384 => 'E_USER_DEPRECATED',
					32767 => 'E_ALL',
					512 => 'ERR_STARTUP_FAILED',
					513 => 'ERR_ROUTE_FAILED',
					514 => 'ERR_DISPATCH_FAILED',
					520 => 'ERR_AUTOLOAD_FAILED',
					515 => 'ERR_NOTFOUND_MODULE',
					516 => 'ERR_NOTFOUND_CONTROLLER',
					517 => 'ERR_NOTFOUND_ACTION',
					518 => 'ERR_NOTFOUND_VIEW',
					519 => 'ERR_CALL_FAILED',
					521 => 'ERR_TYPE_ERROR',
			);
			 
			$code = $exception->getCode();;
			$emsg = !empty( $errorMsg[$code] ) ? $errorMsg[$code] : $code;
			 
			switch ( $code )
			{
				case YAF_ERR_NOTFOUND_MODULE:
				case YAF_ERR_NOTFOUND_CONTROLLER:
				case YAF_ERR_NOTFOUND_ACTION:
				case YAF_ERR_NOTFOUND_VIEW:
					if ( strpos( $this->getRequest()->getRequestUri(), '.css' ) !== false ||
					strpos( $this->getRequest()->getRequestUri(), '.jpg' ) !== false ||
					strpos( $this->getRequest()->getRequestUri(), '.js' ) !== false ||
					strpos( $this->getRequest()->getRequestUri(), '.png' ) !== false ||
					strpos( $this->getRequest()->getRequestUri(), '.ico' ) !== false ||
					strpos( $this->getRequest()->getRequestUri(), '.gif' ) !== false
					) {
						header( 'HTTP/1.1 404 Not Found' );
					}
					break;
				case YAF_ERR_STARTUP_FAILED:
				case YAF_ERR_ROUTE_FAILED:
				case YAF_ERR_DISPATCH_FAILED:
				case YAF_ERR_AUTOLOAD_FAILED:
				case YAF_ERR_CALL_FAILED:
				case YAF_ERR_TYPE_ERROR:
				default:
					Log::error( $exception->getTraceAsString() . ' 文件 ' . $exception->getFile() . ' 行 ' . $exception->getLine(), $emsg ); // 记录错误日志
			}
	
			$this->exceptionHandler($exception);
	
		}
	
		/**
		 * 异常处理
		 * @author liaofuqian
		 * @param unknown $exception
		 */
		private function exceptionHandler(Exception $exception)
		{
			//自定义异常处理
			if ($this->_config->application->showErrors)
			{
				$exceptionText = $exception->getMessage()."<br>";
				$exceptionText .= '所在文件:'.$exception->getFile().";";
				$exceptionText .= '所在行:'.$exception->getLine()."<br>";
				$exceptionText .= $exception->getTraceAsString();
	
				$result = array();
				$result['total'] = 0;
				$result['rows']  = array();
				$result['msg']   = $exceptionText;
				$result['state'] = 0;
				exit(json_encode($result));
			}
		}
	
}
