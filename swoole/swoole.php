<?php
class Server{
    private  $serv;

    public function __construct(){
        $this->serv=new swoole_server('0.0.0.0',9501);
        $this->serv->set(array(
                'worker_num'=>8,
                'daemonize' => false,
                'max_request'=>1000,
                'dispatch_mode'=>2,
                'debug_mod'=>1,
                 'task_worker_num' => 8
        ));
        $this->serv->on('Start', array($this, 'onStart'));
        $this->serv->on('Connect', array($this, 'onConnect'));
        $this->serv->on('Receive', array($this, 'onReceive'));
        $this->serv->on('Close', array($this, 'onClose'));
        $this->serv->on('Task', array($this, 'onTask'));
        $this->serv->on('Finish', array($this, 'onFinish'));
        $this->serv->start();
    }

    public function onStart($serv){
        echo "Start\r\n";
    }
    public function onConnect( $serv, $fd, $from_id ) {
        $serv->send( $fd, "Hello {$fd}!" );
    }

    public function onReceive( swoole_server $serv, $fd, $from_id, $data ) {
        echo "Get Message From Client {$fd}:{$data}\n";
        $param=array(
            'fd'=>$fd
        );
        $serv->task(json_encode($param));
        echo "Continue Handle Worker\r\n";
    }

    public function onTask($serv,$task_id,$from_id,$data){
        echo "This Task {$task_id} from worker {$from_id}\n";
        echo "Data:{$data}\n";
        for($i=0;$i<10;$i++){
            sleep(1);
            echo "Task {$task_id} Handle {$i} times ...\n";
        }
        $fd=json_decode($data,true)['fd'];
        $serv->send($fd,"Data in Task {$task_id}");
        return "Task {$task_id}'s result'";
    }

    public function onFinish($serv,$task_id, $data) {
        echo "Task {$task_id} finish\n";
        echo "Result: {$data}\n";
    }

    public function onClose( $serv, $fd, $from_id ) {
        echo "Client {$fd} close connection\n";
    }





}

$server=new Server();
