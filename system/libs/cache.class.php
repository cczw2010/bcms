<?php
//----------------------------------------------
/*
 *cache 缓存接口  by awen
 */
interface Cache{
	/**
     * 获取缓存
     * @param  string $group 分组（或者称为命名空间）
     * @param  string $key   键值
     * @return mix
     */
    public function get($group,$key);
    /**
     * 设置缓存
     * @param  string $group 分组（或者称为命名空间）
     * @param  string $key   键
     * @param  string $val 值
     * @param  int $ttl 缓存时间（秒）0表示永不过期,默认使用配置文件设置的缓存
    */
    public function set($group,$key,$val,$ttl=null);
    /*
    *删除某个键值对应的缓存
    */
    public function delete($group,$key);
    /*
    *清空所有缓存,如果传入了group则只删除该组（命名空间）的数据
    */
    public function clear($group=false);
}