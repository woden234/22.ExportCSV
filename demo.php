<?php

// ExportCSV abstract class
require "ExportCSV.class.php";

// 定义继承类
class myexport extends ExportCSV{

    // 要导出的数据，实际情况会从db读取
    protected $data = array(
        array('1','傲雪星枫"','男'),
        array('2','傲雪星枫","','男'),
        array('3','傲雪星枫","','男'),
        array('4',"傲雪星枫\"\"\r\n换行",'男'),
        array('5','傲雪星枫,,','男'),
        array('6','傲雪星枫"','男'),
        array('7','傲雪星枫','男'),
        array('8','傲雪星枫','男'),
        array('9','傲雪星枫','男'),
        array('10','傲雪星枫','男')
    );

    /* 返回总导出记录数
    * @return int
    */
    protected function getExportTotal(){
        return count($this->data);
    }

    /** 返回导出的列名
    * @return Array
    */
    protected function getExportFields(){
        $title = array('id','name','gender');
        return $title;
    }

    /* 返回每批次的记录
    * @param  int $offset 偏移量
    * @param  int $limit  获取的记录条数
    * @return Array
    */
    protected function getExportData($offset, $limit){
        return array_slice($this->data, $offset, $limit);
    }

}

// 导出
$obj = new myexport();
$obj->setPageSize(1);
$obj->setExportName('myexport.csv');
$obj->setSeparator(',');
$obj->setDelimiter('"');
$obj->export();

?>