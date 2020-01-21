<?php
/*
 * 初始化数据库连接
 * */
function initMySqlConnector(){
    $link = mysqli_connect('localhost','Sheng','123456');
    if(!$link){
        die('连接数据库失败! ' .mysqli_connect_error());
    }
    mysqli_query($link,'set names utf8');
    mysqli_query($link,'use `usedbookmarket`');
    return $link;
}
/*
 * 查询数据库记录
 */
function query($link,$sql){
    if($result = mysqli_query($link,$sql)){
        return $result;
    }else{
        echo '失败SQL:',$sql,'<br/>';
        echo '错误代码"',mysqli_errno($link),'<br/>';
        echo '错误信息:',mysqli_error($link),'<br/>';
    }
}
/*
 * 遍历多条结果数据集合
 */
function fetchAll($link,$sql){
    if($result = query($link,$sql)){
        //遍历结果集
        $rows = array();
        while ($row=mysqli_fetch_assoc($result)){
            $rows[] = $row;
        }
        //释放结果集
        mysqli_free_result($result);
        return $rows;
    }else{
        return false;
    }
}
/*
 * 遍历单条结果数据集合
 */
function fetchRow($link,$sql){
    if($result = query($link,$sql)){
        $row=mysqli_fetch_array($result,MYSQLI_ASSOC);
        return $row;
    }else{
        return false;
    }
}

function safeHandle($link,$data){
    $data = htmlspecialchars($data);
    $data = mysqli_real_escape_string($link,$data);
}

function initPage($page,$total){
    $params = $_GET;
    unset($params['page']);
    $params = http_build_query($params);
    if($params){
        $params.='&';
    }
    //计算下一页
    $next_page = $page+1;
    //判断下一页页码是否大于总页码
    if($next_page>$total){
        $next_page=$total;
    }
    //计算上一页
    $pre_page = $page-1;
    //判断上一页页码是否小于1
    if($pre_page<1){
        $pre_page=1;
    }
    //重新拼接分页链接的html代码
    $page_html = '<a href="?'.$params.'page=1">首页</a>';
    $page_html .= '<a href="?'.$params.'page='.$pre_page.'">上一页</a>';
    for($i=1;$i<=$total;$i++){
        $page_html.='<a href="?'.$params.'page='.$i.'">'.$i.'</a>';
    }
    $page_html .= '<a href="?'.$params.'page='.$next_page.'">下一页</a>';
    $page_html .= '<a href="?'.$params.'page='.$total.'">尾页</a>';
    return $page_html;
}