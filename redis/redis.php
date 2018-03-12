<?php

class MyRedis {
    const HOST = '127.0.0.1';
    const PORT = 6390;
    private $redis;

    public function __construct() {
        $this->redis = new Redis();
        $this->redis->connect(MyRedis::HOST, MyRedis::PORT);
    }

    public function __destruct() {
        $this->redis->close();
    }

    public function __call($method, $params) {
        return call_user_func_array(array($this->redis, $method), $params);
    }
}
