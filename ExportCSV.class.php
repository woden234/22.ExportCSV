<?php
/** Export CSV abstract class,根据总记录数与每批次记录数,计算总批次,循环导出。
*   Date:   2014-05-16
*   Author: fdipzone
*   Ver:    1.0
*
*   Func:
*   public  setPageSize      设置每批次导出的记录条数
*   public  setExportName    设置导出的文件名
*   public  setSeparator     设置分隔符
*   public  setDelimiter     设置定界符
*   public  export           执行导出
*   private getPageCount     计算导出总批次
*   private setHeader        设置导出文件header
*   private formatCSV        将数据格式化为csv格式
*   private escape           转义字符串
*   abstract getExportTotal  获取总记录条数,抽象方法,需继承类实现
*   abstract getExportFields 获取导出的列名,抽象方法,需继承类实现
*   abstract getExportData   获取每页记录,抽象方法,需继承类实现
*/

abstract class ExportCSV{ // class start

    // 定义子类必须要实现的方法
    
    /** 获取总记录条数
    * @return int
    */
    abstract protected function getExportTotal();

    /** 获取导出的列名
    * @return Array
    */
    abstract protected function getExportFields();

    /** 获取每批次数据
    * @param  int $offset 偏移量
    * @param  int $limit  获取的记录条数
    * @return Array
    */
    abstract protected function getExportData($offset, $limit);


    // 定义类属性
    protected $total = 0;                 // 总记录数
    protected $pagesize = 500;            // 每批次导出的记录数
    protected $exportName = 'export.csv'; // 导出的文件名
    protected $separator = ',';           // 设置分隔符
    protected $delimiter = '"';           // 设置定界符


    /** 设置每次导出的记录条数
    * @param int $pagesize 每次导出的记录条数
    */
    public function setPageSize($pagesize=0){
        if(is_numeric($pagesize) && $pagesize>0){
            $this->pagesize = $pagesize;
        }
    }


    /** 设置导出的文件名
    * @param String $filename 导出的文件名
    */
    public function setExportName($filename){
        if($filename!=''){
            $this->exportName = $filename;
        }
    }


    /** 设置分隔符
    * @param String $separator 分隔符
    */
    public function setSeparator($separator){
        if($separator!=''){
            $this->separator = $separator;
        }
    }


    /** 设置定界符
    * @param String $delimiter 定界符
    */
    public function setDelimiter($delimiter){
        if($delimiter!=''){
            $this->delimiter = $delimiter;
        }
    }


    /** 导出csv */
    public function export(){

        // 获取总记录数
        $this->total = $this->getExportTotal();

        // 没有记录
        if(!$this->total){
            return false;
        }

        // 计算导出总批次
        $pagecount = $this->getPageCount();

        // 获取导出的列名
        $fields = $this->getExportFields();

        // 设置导出文件header
        $this->setHeader();

        // 循环导出
        for($i=0; $i<$pagecount; $i++){

            $exportData = '';

            if($i==0){ // 第一条记录前先导出列名
                $exportData .= $this->formatCSV($fields);
            }

            // 设置偏移值
            $offset = $i*$this->pagesize;

            // 获取每页数据
            $data = $this->getExportData($offset, $this->pagesize);

            // 将每页数据转换为csv格式
            if($data){
                foreach($data as $row){
                    $exportData .= $this->formatCSV($row);
                }
            }

            // 导出数据
            echo $exportData;
        }

    }


    /** 计算总批次 */
    private function getPageCount(){
        $pagecount = (int)(($this->total-1)/$this->pagesize)+1;
        return $pagecount;
    }


    /** 设置导出文件header */
    private function setHeader(){
        header('content-type:application/x-msexcel');

        $ua = $_SERVER['HTTP_USER_AGENT'];

        if(preg_match("/MSIE/", $ua)){
            header('content-disposition:attachment; filename="'.rawurlencode($this->exportName).'"');
        }elseif(preg_match("/Firefox/", $ua)){
            header("content-disposition:attachment; filename*=\"utf8''".$this->exportName.'"');
        }else{
            header('content-disposition:attachment; filename="'.$this->exportName.'"');
        }

        ob_end_flush();
        ob_implicit_flush(true);
    }


    /** 格式化为csv格式数据
    * @param Array $data 要转换为csv格式的数组
    */
    private function formatCSV($data=array()){
        // 对数组每个元素进行转义
        $data = array_map(array($this,'escape'), $data);
        return $this->delimiter.implode($this->delimiter.$this->separator.$this->delimiter, $data).$this->delimiter."\r\n";
    }


    /** 转义字符串
    * @param  String $str
    * @return String
    */
    private function escape($str){
        return str_replace($this->delimiter, $this->delimiter.$this->delimiter, $str);
    }

} // class end

?>