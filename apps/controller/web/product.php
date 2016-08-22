<?php
/**
 * 产品详情
 * @authors Your Name (you@example.org)
 * @date    2016-08-19 10:56:12
 * @version $Id$
 */

class Product
{
    
    function __construct(){
        
    }

    /**
     * [about 产品详情]
     * @param  string $value [description]
     * @return [type]        [description]
     */
    public function detail()
    {
        $datas['type'] = 'detail';
        $this->view->load('web/detail', $datas);
    }

    /**
     * [process 使用流程]
     * @return [type] [description]
     */
    public function process()
    {
        # code...
    }

    /**
     * [company 我是公司]
     * @return [type] [description]
     */
    public function company()
    {
        # code...
    }

    /**
     * [designer 我是设计师]
     * @return [type] [description]
     */
    public function designer()
    {
        # code...
    }
}
