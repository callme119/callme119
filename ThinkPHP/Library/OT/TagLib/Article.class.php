<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2013 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi.cn@gmail.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------
namespace OT\TagLib;
use Think\Template\TagLib;
/**
 * 文档模型标签库
 */
class Article extends TagLib{
    /**
     * 定义标签列表
     * @var array
     */
    protected $tags   =  array(
        'partlist' => array('attr' => 'id,field,page,name', 'close' => 1), //段落列表
        'partpage' => array('attr' => 'id,listrow', 'close' => 0), //段落分页
        'prev'     => array('attr' => 'name,info', 'close' => 1), //获取上一篇文章信息
        'next'     => array('attr' => 'name,info', 'close' => 1), //获取下一篇文章信息
        'page'     => array('attr' => 'cate,listrow', 'close' => 0), //列表分页
        'position' => array('attr' => 'pos,cate,limit,filed,name', 'close' => 1), //获取推荐位列表
        'list'     => array('attr' => 'name,category,child,page,row,field', 'close' => 1), //获取指定分类列表
    );

    public function _list($tag, $content){
        //参数设置，name和category为必填项。
        $name   = $tag['name'];
        $cate   = $tag['category'];

        //为『选填』的项增加默认值。
        $child  = empty($tag['child']) ? 'false' : $tag['child'];
        $row    = empty($tag['row'])   ? '10' : $tag['row'];
        $field  = empty($tag['field']) ? 'true' : $tag['field'];

        //拼接php字符串，最终执行时，将以下字符串，直接解析为PHP语句进行解析。
        $parse  = '<?php ';
        $parse .= '$__CATE__ = D(\'Category\')->getChildrenId('.$cate.');';
        $parse .= '$__LIST__ = D(\'Document\')->page(!empty($_GET["p"])?$_GET["p"]:1,'.$row.')->lists(';
        $parse .= '$__CATE__, \'`level` DESC,`id` DESC\', 1,';
        $parse .= $field . ');';
        $parse .= ' ?>';

        //由于需要循环输出，所以使用volist。当然了，我们看到，在标签库中，也是可以使用其它标签库的。
        $parse .= '<volist name="__LIST__" id="'. $name .'">';

        //$content 即是我们的模板中的html代码，所以涉及到替换的，都需要拼接$content.
        $parse .= $content;
        $parse .= '</volist>';
        return $parse;
    }

    /* 推荐位列表 */
    public function _position($tag, $content){
        $pos    = $tag['pos'];
        $cate   = $tag['cate'];
        $limit  = empty($tag['limit']) ? 'null' : $tag['limit'];
        $field  = empty($tag['field']) ? 'true' : $tag['field'];
        $name   = $tag['name'];
        $parse  = '<?php ';
        $parse .= '$__POSLIST__ = D(\'Document\')->position(';
        $parse .= $pos . ',';
        $parse .= $cate . ',';
        $parse .= $limit . ',';
        $parse .= $field . ');';
        $parse .= ' ?>';
        $parse .= '<volist name="__POSLIST__" id="'. $name .'">';
        $parse .= $content;
        $parse .= '</volist>';
        return $parse;
    }

    /* 列表数据分页 */
    public function _page($tag){
        $cate    = $tag['cate'];
        $listrow = $tag['listrow'];
        $parse   = '<?php ';
        $parse  .= '$__PAGE__ = new \Think\Page(get_list_count(' . $cate . '), ' . $listrow . ');';
        $parse  .= 'echo $__PAGE__->show();';
        $parse  .= ' ?>';
        return $parse;
    }

    /* 获取下一篇文章信息 */
    public function _next($tag, $content){
        $name   = $tag['name'];
        $info   = $tag['info'];
        $parse  = '<?php ';
        $parse .= '$' . $name . ' = D(\'Document\')->next($' . $info . ');';
        $parse .= ' ?>';
        $parse .= '<notempty name="' . $name . '">';
        $parse .= $content;
        $parse .= '</notempty>';
        return $parse;
    }

    /* 获取上一篇文章信息 */
    public function _prev($tag, $content){
        $name   = $tag['name'];
        $info   = $tag['info'];
        $parse  = '<?php ';
        $parse .= '$' . $name . ' = D(\'Document\')->prev($' . $info . ');';
        $parse .= ' ?>';
        $parse .= '<notempty name="' . $name . '">';
        $parse .= $content;
        $parse .= '</notempty>';
        return $parse;
    }

    /* 段落数据分页 */
    public function _partpage($tag){
        $id      = $tag['id'];
        if ( isset($tag['listrow']) ) {
            $listrow = $tag['listrow'];
        }else{
            $listrow = 10;
        }
        $parse   = '<?php ';
        $parse  .= '$__PAGE__ = new \Think\Page(get_part_count(' . $id . '), ' . $listrow . ');';
        $parse  .= 'echo $__PAGE__->show();';
        $parse  .= ' ?>';
        return $parse;
    }

    /* 段落列表 */
    public function _partlist($tag, $content){
        $id     = $tag['id'];
        $field  = $tag['field'];
        $name   = $tag['name'];
        if ( isset($tag['listrow']) ) {
            $listrow = $tag['listrow'];
        }else{
            $listrow = 10;
        }
        $parse  = '<?php ';
        $parse .= '$__PARTLIST__ = D(\'Document\')->part(' . $id . ',  !empty($_GET["p"])?$_GET["p"]:1, \'' . $field . '\','. $listrow .');';
        $parse .= ' ?>';
        $parse .= '<?php $page=(!empty($_GET["p"])?$_GET["p"]:1)-1; ?>';
        $parse .= '<volist name="__PARTLIST__" id="'. $name .'">';
        $parse .= $content;
        $parse .= '</volist>';
        return $parse;
    }
}