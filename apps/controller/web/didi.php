<?php
/**
* 滴滴动态
*/
class Didi
{
    function __construct()
    {
        # code...
    }

    /**
     * [design 滴滴设计告白列表]
     * @return [type] [description]
     */
    public function design()
    {
        $params = Uri::getParams();
        $params = $params['params'];
        if (isset($params['page'])) {
            $page = $params['page'];
        } else {
            $page = 1;
        }
        $psize = 10;
        $datas['type'] = 'didi';
        $datas['type_two'] = 'design';
        $res = Module_Article::getItems();
        $datas['list'] = $res['list'];
        $datas['pageDiv'] = set_php_page_menu($res['total'], $psize, $page);
        $this->view->load('web/didi', $datas);
    }

    /**
     * [activity 活动消息列表]
     * @param  string $value [description]
     * @return [type]        [description]
     */
    public function activity()
    {
        $datas['type'] = 'didi';
        $datas['type_two'] = 'activity';
        $this->view->load('web/didi', $datas);
    }

    /**
     * [report 媒体报道列表]
     * @return [type] [description]
     */
    public function report()
    {
        $datas['type'] = 'didi';
        $datas['type_two'] = 'report';
        $this->view->load('web/didi', $datas);
    }

    /**
     * [article_detail 文章详情]
     * @return [type] [description]
     */
    public function article_detail()
    {
        # code...
    }
}
