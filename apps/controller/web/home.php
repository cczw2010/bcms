<?php
/**
* 首页+关于我们
*/
class Home
{
    
    function __construct()
    {
        # code...
    }

    /**
     * [index 首页]
     * @return [type] [description]
     */
    public function index()
    {
        $datas['type'] = 'index';
        $res = Module_Number::getItem();
        if ($res['code'] == 1) {
            $datas['need_user_num'] = $res['data']['need_user_num'];
            $datas['designer_num'] = $res['data']['designer_num'];
        } else {
            $datas['need_user_num'] = 0;
            $datas['designer_num'] = 0;
        }
        $this->view->load('web/index', $datas);
    }

    /**
     * [about 关于我们]
     * @param  string $value [description]
     * @return [type]        [description]
     */
    public function about()
    {
        $datas['type'] = 'about';
        $this->view->load('web/about', $datas);
    }

}
