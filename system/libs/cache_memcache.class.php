<?php
//----------------------------------------------
/*
 *memcache 缓存类,批出一块内存存储分组（命名空间）信息  by awen
 *配置文件格式
 *   array(
 *       'servers'=>array(
 *           'memcache1'=>array('host'=>'127.0.0.1','port'=>11211,'weight'=>1),
 *       ),
 *       'ttl'=>60*24, //秒，0代表永不过期
 *       'default'=true,
 *   ),
 */
class Cache_memcache implements Cache{
	public  static $memcache;
	private $ttl=0;
	function __construct($config=array()){
		if (!isset(self::$memcache)) {
			self::$memcache = new Memcache;
		}
		if (isset($config['ttl'])) {
			$this->ttl=$config['ttl'];
		}
		//如果由配置信息，则根据配置信息初始化，否则加载默认配置
		if (isset($config['servers']) && is_array($config['servers'])) {
			foreach ($config['servers'] as $server) { 
				self::$memcache->addServer($server['host'],$server['port'],TRUE,$server['weight']);
			}
		}else{
            throw new Exception('memcache连接失败。请检查memcache配置信息。', 1);
		}
	}
    public function get($group,$key){
        if (isset(self::$memcache)) {
            $gkey = $this->buildKey($group,$key);
        	return self::$memcache->get($gkey);
        }else{
        	return FALSE;
        }
    }
    public function set($group,$key,$val,$ttl=null){
 		if (isset(self::$memcache)) {
 			$ttl=is_null($ttl)?$this->ttl:$ttl;
            // 如果系统默认设置的也是-1，不缓存
            if ($this->ttl==-1) {
                return FALSE;
            }
            $gkey = $this->buildKey($group,$key);
        	return self::$memcache->set($gkey, $val, 0, $ttl);
        }else{
        	return FALSE;
        }
    }
    public function delete($group,$key){
 		if (isset(self::$memcache)) {
            $gkey = $this->buildKey($group,$key);
        	return self::$memcache->delete($gkey,0);
        }else{
        	return FALSE;
        }
    }
    public function clear($group=false){
    	if (isset(self::$memcache)) {
            if (empty($group)) {
        	   return self::$memcache->flush();
            }else{
                $keys = $this->getGroupKeys($group);
                foreach ($keys as $key) {
                    self::$memcache->delete($key,0);
                }
            }
        }else{
        	return FALSE;
        }
    }
    /**
     * 根据分组和键生成 唯一的键值
     * @param string $group 分组名
     */
    private function buildKey($group,$key){
        $prefix = $this->buildGPrefix($group);
        return $prefix.$key;
    }
    private function buildGPrefix($group){
        $group = empty($group)?'default':$group;
        return '__'.$group.'__';
    }
    /**
     * 获取某组（命名空间）下的所有键值，
     * 虽然这种方式效率堪忧，但是考虑到该方法顶多再后台执行一下，还可以接受
     * @param  [type] $group [description]
     * @return [type]        [description]
     */
    private function getGroupKeys($group){
        $gprefix = $this->buildGPrefix($group);
        $options = self::$memcache->getExtendedStats('items');
        $keys      = array();
        foreach ($options as $hp=>$option) {
            if(isset($option['items'])){
                foreach ($option['items'] as $number => $item) {
                    $_items    = self::$memcache->getExtendedStats('cachedump', $number, 0);
                    $line   = $_items[$hp];
                    if (is_array($line) && count($line) > 0){
                        foreach ($line as $key => $value) {
                            if (preg_match('/^'.$gprefix.'/',$key)) {
                                $keys[] = $key;
                            }
                        }
                    }
                    // dump($hp,$item,$_items);
                }
            }
        }
        return array_unique($keys);
    }
}