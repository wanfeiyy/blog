<?php
  
   class SocketServer{	
   private $_Port;
   private $_address;
   private $_socket_server;
   private static $_instance;
   public function createSocket(){
	// 创建端口
	if (($this->_socket_server = socket_create ( AF_INET, SOCK_STREAM, SOL_TCP )) === false) {
		exit("socket_create() failed :reason:" . socket_strerror ( socket_last_error () ) . "\n");
	}// 绑定
	if (socket_bind ( $this->_socket_server, $this->_address, $this->_port ) === false) {
		exit("socket_bind() failed :reason:" . socket_strerror ( socket_last_error ( $this->_socket_server ) ) . "\n");
	}
	// 监听
	if (socket_listen ( $this->_socket_server, 5 ) === false) {
		exit("socket_bind() failed :reason:" . socket_strerror ( socket_last_error ( $this->_socket_server ) ) . "\n");
	}
	return $this->_socket_server;

}	
		  		
  public static  function getInstance(){

	if (is_null ( self::$_instance )) {
			self::$_instance = new self ();
			self::$_instance->_address='127.0.0.1';
			self::$_instance->_port=9000;
			return self::$_instance;
		}


}
  public function __get($name){
		return $this->$name;

	}	
  /**
	 * socket发送字符串
	 * 
	 * @param resource $socket        	
	 * @param array $data        	
	 * @return number
	 */
	public function send($socket_client, $data = array()) {
		$data = json_encode ( $data );
		echo "send:{$data}\r\n";
		return socket_write ($socket_client,$data,strlen ( $data ));
	}





	public function close($socket_client) {
		socket_close ($socket_client);
	}

}

   class Socket{
	private $_socket_server;//服务器连接服务
	private $_client_socket_list = array ();//通过用户验证后的客户端
	private $_error;
	public function start(){
		PHP_SAPI=='cli' or exit('本程序只允许CLI模式');
		$socket_server=new SocketServer();
		$this->_socket_server=$socket_server::getInstance();
		$this->_socket_server->createSocket();
		do{
			$socket_list=array_merge($this->_client_socket_list,array($this->_socket_server->_socket_server));
			if(socket_select($socket_list,$write,$except=null,null)){
				foreach($socket_list as $socket){
					if($this->_socket_server->_socket_server==$socket){
						$client_socket=socket_accept ($socket);
						$count = count ( $this->_client_socket_list ) + 1;
						//把新来的用户加入 客户端数组里
						$this->_client_socket_list[]=$client_socket;
						echo "new connection:\r\n";//服务器端输出当前正在连接的客户端数量
						echo "current connection:{$count}\r\n";
					}else{
						//如果是客户端,那么肯定是有数据发送过来了.
						$msg=$this->_socket_server->read($socket);
						//如果不是为空那么就向客户端发送数据
						if(!empty($msg)){
							$data['op']=$msg['type'];
							if($msg['type']=='operate1'){
								//业务逻辑1
								$data['msg']='I am fina';
							}else if($msg['type']=='operate2'){
								//业务逻辑2
								$data['op']='yes';
							}else if($msg['type']=='operate3'){
								//业务逻辑3
								$data['op']='your sister!';
							}
							echo "client:{$msg['msg']}\r\n";
							echo "server:{$data['msg']}\r\n";
						}else{
							$this->_close($socket);
						}
					}
				}
			}
		}while(true);
	}
	private function _close($socket){
		$this->_socket_server->close($socket);
		unset($this->_client_socket_list[array_search($socket,$this->_client_socket_list)]);
	}
}


$s=new Socket();
$s->start();









