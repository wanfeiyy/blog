<?php 
class SocketClient{
	private $_port;
	private $_address;
	private $_socket;
	private static $_instance;
	/**
	 * 单例入口
	 */
	public static function getInstance() {
		if (is_null ( self::$_instance )) {
			self::$_instance = new self ();
			self::$_instance->_socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
			if(self::$_instance->_socket==false){
				self::$_instance->_error=socket_last_error(self::$_instance->_socket);
				return false;
			}
			self::$_instance->_address='127.0.0.1';
			self::$_instance->_port=9000;
			$result = socket_connect(self::$_instance->_socket, self::$_instance->_address, self::$_instance->_port);
			if($result==false){
				self::$_instance->_error=socket_last_error(self::$_instance->_socket);
				return false;
			}
		}
		return self::$_instance;
	}
	/**
	 * socket发送字符串
	 * @param resource $socket
	 * @param array $data
	 * @return number
	 */
	public function send($data){
		$data=json_encode($data);
		socket_write(self::$_instance->_socket, $data,strlen($data));
	}
	public function __destruct(){
		$this->close();
	}
	/**
	 * 获取socket传过来的字符串
	 * @param resource $socket
	 * @return mixed
	 */
	public function read(){
		$c=socket_read($this->_socket,8192);
		return json_decode($c,true);
	}
	/**
	 * 关闭socket连接
	 * @param resource $socket
	 */
	public function close(){
		return socket_close($this->_socket);
	}
}
$operate1_data['op']='operate1';
$operate1_data['msg']='how are you!';
$operate2_data['op']='operate2';
$operate2_data['msg']='are you online!';
$operate3_data['op']='operate3';
$operate3_data['msg']='hehe!';
 
$socket=SocketClient::getInstance();
$socket->send($operate1_data);
 
sleep(1);
$socket->send($operate2_data);
 
sleep(1);
$socket->send($operate3_data);
