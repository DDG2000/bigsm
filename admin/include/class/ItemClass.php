<?php
// require_once(WEBROOT.'include' . L . 'PDOMySql.class.php');
require_once(WEBROOTINCCLASS.'Base.php');

class ItemClass extends Base{
    
    public  function getItemClassListPage($CurrPage,$pagesize,$I_mall_classID){
        
        $tables = 'sm_item_class where Status=1 and I_mall_classID='.$I_mall_classID;
        $sql = "SELECT * FROM {$tables} order by I_order asc";
        $sqlcount = "SELECT count(*) FROM {$tables} ";
        return $this->Db->GetPageResult($sql,$sqlcount,$CurrPage,$pagesize);
        
    }
    public  function getItemClassOne($Id){
        
        $sql='select * from sm_item_class where id='.$Id.' limit 0,1';
       
        return $this->Db->GetResultOne($sql);

        
    }
    
    
    
    
    
}