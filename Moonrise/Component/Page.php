<?php
/**
 * 分页组件 todo 等待重构
 *
 * @author itsmikej
 */

namespace Moonrise\Component;

class Page
{
    private $paged;         # 当前页
    private $total;         # 总数据条数
    private $show_num;      # 每页显示数据条数
    private $total_page;    # 总页数
    private $url;           # 去除分页后的url
    private $list_num;      # 分页按钮显示数量

    private $config = array(
        'first' => '首页',
        'last'  => '尾页',
        'prev'  => '上一页',
        'next'  => '下一页',
    );

    public function __construct($total, $show_num, $list_num=5)
    {
        $this->total = $total;
        $this->show_num = $show_num;
        $this->paged = !empty($_GET['paged']) ? $_GET['paged'] : 1;
        $this->total_page = ceil($this->total/$this->show_num);
        if ($this->paged > $this->total_page) {
            $this->paged = $this->total_page;
        }
        if ($this->paged < 1) {
            $this->paged = 1;
        }

        $this->url = $this->getUrl();
        $this->list_num = $list_num;
    }

    public function addConfig($config)
    {
        foreach ($config as $k => $v) {
            $this->config[$k] = $v;
        }
    }

    private function getUrl()
    {
        $url = $_SERVER['REQUEST_URI'] . (strpos($_SERVER["REQUEST_URI"], '?') ? '' : "?");
        // $parse = parse_url($url);
        // if (isset($parse['query'])) {
        // 	parse_str($parse['query'], $params);
        // 	unset($params['paged']);
        // 	$url = $parse['path'] . '?' . http_build_query($params);
        // }
        $url = preg_replace("/\&paged\=\d*/", "", $url);
        return $url;
    }

    private function getLink($paged, $show)
    {
        return '<li><a href="'. $this->url .'&paged='. $paged .'">'. $show .'</a></li>';
    }

    private function first()
    {
        return ($this->paged == 1) ? '' : $this->getLink(1, $this->config['first']);
    }

    private function last()
    {
        return ($this->paged == $this->total_page) ? '' : $this->getLink($this->total_page, $this->config['last']);
    }

    private function prev()
    {
        return ($this->paged == 1) ? '' : $this->getLink(($this->paged-1), $this->config['prev']);
    }

    private function next()
    {
        return ($this->paged == $this->total_page) ? '' : $this->getLink(($this->paged+1), $this->config['next']);
    }

    private function pageList()
    {
        $page_list = '';
        $listNum = floor($this->list_num/2);
        if ($this->paged > ($this->total_page - $listNum)) {
            for ($i = ($this->total_page - $this->list_num + 1); $i < ($this->paged - $listNum); $i++) {
                $page_list .= $this->getLink($i, $i);
            }
        }

        for ($i = $listNum; $i >= 1 ; $i--) {
            if (1 > ($paged = $this->paged-$i)) {
                continue;
            }
            $page_list .= $this->getLink($paged, $paged);
        }

        $page_list .= '<li><a href="'. $this->url .'&paged='. $this->paged .'">'. $this->paged .'</a></li>';

        for ($i = 1; $i <= $listNum; $i++) {
            if ($this->total_page < ($paged = $this->paged+$i)) {
                break;
            }
            $page_list .= $this->getLink($paged, $paged);
        }

        if ($paged < $this->list_num) {
            for ($i = ($paged+1); $i <= $this->list_num; $i++) {
                $page_list .= $this->getLink($i, $i);
            }
        }

        return $page_list;
    }

    public function showPager($display=array(0,1,2,3,4,5))
    {
        $output = array();
        $output[0] = '<li>共'. $this->total .'条记录,'. $this->total_page .'页,当前第'. $this->paged .'页</li>';
        $output[1] = $this->first();
        $output[2] = $this->prev();
        $output[3] = $this->pageList();
        $output[4] = $this->next();
        $output[5] = $this->last();
        $html = '';
        foreach ($display as $v) {
            $html .= empty($output[$v]) ? '' : $output[$v];
        }
        unset($output);
        return '<ul>'.$html.'</ul>';
    }
}

/* use it:
$page = new Page(99, 10);
echo $page->showPager(array(1,2,3,4,5));
*/
