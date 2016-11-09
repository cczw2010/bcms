<?php
//----------------------------------------------
/*
 * redis 缓存类配置如下,比较常用的php链接redis库:phpredis(依赖c扩展,不宜扩展,较快)
 * predis(纯php实现,支持redis-cluster和redis-sentinel,易于扩展,略慢) 这里使用predis
 * 实现机制为cluster(集群),sentinel(哨兵)暂未实现 , 分组使用前缀方式实现,没有使用set,list也没用db来分组,因为怕其他系统共用误删除db by awen
 *  $config =>array(
 *         'ttl'=>1000 ;
 *         'servers'=>array (
 *                          array (
 *                              'host'      =>  '127.0.0.1' ,   //默认 127.0.0.1
 *                              'port'      =>  6379 ,          //默认 6379
 *                              'database'  =>  15 ,            //默认使用redis自己默认的数据库
 *                              'password'  =>  '',             //链接密码,如果没有密码移除此项,默认无密码
 *                              'timeout'   =>  5.0             //链接服务器超时时间, 默认5s
 *                              'read_write_timeout' =>  5.0    //读写超时时间,默认为系统设置,-1为不读写永不超时
 *                          ) ,
 *                          array (
 *                              'host'      =>  '127.0.0.1' ,
 *                              'port'      =>  6380 ,
 *                              'database'  =>  15 ,
 *                              'password'  =>  '',
 *                              'alias'     =>  'second' ,
 *                          ) ,
*           ),
 *        ),
 *  );
 */
 // 引入文件
require __DIR__.'/predis-1.1/src/Autoloader.php';
Predis\Autoloader::register();

class Cache_redis implements Cache{
    public static $redis;
    private $ttl=0;
    function __construct($config=array()){

        //如果由配置信息，则根据配置信息初始化，否则加载默认配置
        if (!isset(self::$redis)) {
            // $options    = ['cluster' => 'redis'];
            $options = array(
                'cluster' => function () {
                            $distributor = new NaiveDistributor();
                            $strategy = new Predis\Cluster\PredisStrategy($distributor);
                            $cluster = new Predis\Connection\Aggregate\PredisCluster($strategy);
                            return $cluster;
                        });
            $redis = new Predis\Client($config['servers'],$options);
            self::$redis = $redis;
        }
        if (isset($config['ttl'])) {
            $this->ttl=$config['ttl'];
        }
    }
    public function get($group,$key){
        if (isset(self::$redis)) {
            $_key = $group.'_'.$key;
            return self::$redis->get($_key);
        }else{
            return FALSE;
        }
    }
    public function set($group,$key,$val,$ttl=-1){
        if (isset(self::$redis)) {
            if ($ttl<0) {
                $ttl=$this->ttl;
            }
            $_key = $group.'_'.$key;
            if ($ttl>0) {
                return self::$redis->setex($_key, $ttl,$val);
            }else{
                return self::$redis -> set ($_key, $val);
            }
            // self::$redis -> expire ( 'foo' ,  1 ) ;  //设置有效期为1秒 
            // self::$redis -> ttl ( 'foo' ) ;  //返回有效期值1s 
            // self::$redis -> persist ( 'foo' ) ;  //取消expire行为 
        }else{
            return FALSE;
        }
    }
    public function delete($group,$key){
        if (isset(self::$redis)) {
            $_key = $group.'_'.$key;
            return self::$redis->del($_key);
        }else{
            return FALSE;
        }
    }
    //清空当前库
    public function clear($group=false){
        if (isset(self::$redis)) {
            // cluster 不支持keys 所以要自己封
            $connection = self::$redis -> getConnection();
            foreach ($connection as $connectid => $conn) {
                $client = self::$redis -> getClientFor ( $connectid );
                if (empty($group)) {
                    $client ->flushdb();
                }else{
                    // var_dump($client->info());
                    $keys = $client -> keys ($group.'_*');
                    foreach ($keys as $k) {
                        $client ->del($k);
                    }
                }
            }
            return true;
        }else{
            return FALSE;
        }
    }
}

class NaiveDistributor implements Predis\Cluster\Distributor\DistributorInterface, Predis\Cluster\Hash\HashGeneratorInterface
{
    private $nodes;
    private $nodesCount;
    public function __construct()
    {
        $this->nodes = array();
        $this->nodesCount = 0;
    }
    public function add($node, $weight = null)
    {
        $this->nodes[] = $node;
        ++$this->nodesCount;
    }
    public function remove($node)
    {
        $this->nodes = array_filter($this->nodes, function ($n) use ($node) {
            return $n !== $node;
        });
        $this->nodesCount = count($this->nodes);
    }
    public function getSlot($hash)
    {
        return $this->nodesCount > 1 ? abs($hash % $this->nodesCount) : 0;
    }
    public function getBySlot($slot)
    {
        return isset($this->nodes[$slot]) ? $this->nodes[$slot] : null;
    }
    public function getByHash($hash)
    {
        if (!$this->nodesCount) {
            throw new RuntimeException('No connections.');
        }
        $slot = $this->getSlot($hash);
        $node = $this->getBySlot($slot);
        return $node;
    }
    public function get($value)
    {
        $hash = $this->hash($value);
        $node = $this->getByHash($hash);
        return $node;
    }
    public function hash($value)
    {
        return crc32($value);
    }
    public function getHashGenerator()
    {
        return $this;
    }
}