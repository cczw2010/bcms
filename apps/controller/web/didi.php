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
        $datas['type'] = 'didi';
        $datas['type_two'] = 'design';
        if (isset($_GET['page'])) {
            $page = $_GET['page'];
        } else {
            $page = 1;
        }
        $psize = 1;
        $res = Module_Article::getItems(array('cateid'=>42), 'order by id desc', $page, $psize);
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
        if (isset($_GET['page'])) {
            $page = $_GET['page'];
        } else {
            $page = 1;
        }
        $psize = 1;
        $res = Module_Article::getItems(array('cateid'=>43), 'order by id desc', $page, $psize);
        $datas['list'] = $res['list'];
        $datas['pageDiv'] = set_php_page_menu($res['total'], $psize, $page);
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
        if (isset($_GET['page'])) {
            $page = $_GET['page'];
        } else {
            $page = 1;
        }
        $psize = 1;
        $res = Module_Article::getItems(array('cateid'=>44), 'order by id desc', $page, $psize);
        $datas['list'] = $res['list'];
        $datas['pageDiv'] = set_php_page_menu($res['total'], $psize, $page);
        $this->view->load('web/didi', $datas);
    }

    /**
     * [article_detail 文章详情]
     * @return [type] [description]
     */
    public function article_detail()
    {
        $datas['type'] = 'didi';
        $datas['type_two'] = $_GET['type_two'];
        $res = Module_Article::getItem($_GET['id']);
        $datas['data'] = $res['data'];
        $this->view->load('web/article', $datas);
    }
}
