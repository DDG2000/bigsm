<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/20 0020
 * Time: 上午 10:16
 */
namespace app\admin\model ;
use think\db\Query;
use think\Model;
class CityModel extends AdminModel
{
    protected $table = 'city' ;
    protected $pk = 'id' ;

    /**
     * 获取所有的省及id
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getAllPro(){
        return $this->where('parent_id',0)->field('id,name')->select();
    }

    /**获取所有的城市
     * @param $pid
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getAllCities($pid){
        return $this->where('parent_id',$pid)->field('id,name')->select();
    }
    
    public function getCityCaches($pid){
    	//cache('cityArrCache',null);
    	//先读缓存文件，木有再查
    	$cityArr = cache('cityArrCache');
    	if(!$cityArr){
	    	$wheres = [];
	    	$wheres['state'] = 1;
	    	if($pid){
	    		$wheres['parent_id'] = $pid;
	    	}
	    	$citys = $this->where($wheres)
	    		->field('id,name,parent_id')
	    		->order(['sort'=>'desc','parent_id'=>'asc'])
	    		->select();
	    	
	    	$cityArr = [];
	    	$nbsp = "&nbsp;&nbsp;";
	    	if($citys){
	    		foreach ($citys as $k=>$v){
	    			if($v['parent_id'] == 0){//省
	    				$v['ids'] = ','.$v['id'].',';
	    				$cityArr[$k] = $v;
	    				foreach ($citys as $k1=>$v1){
	    					if($v1['parent_id'] == $v['id']){//市
	    						$v1['name'] = $nbsp.'——'.$v1['name'];
	    						$cityArr[$k1] = $v1;
	    						
	    						$v['ids'] = $v['ids'].$v1['id'].',';
	    						$cityArr[$k] = $v;
	    						foreach ($citys as $k2=>$v2){
	    							if($v2['parent_id'] == $v1['id']){//区
	    								$v2['name'] = $nbsp.$nbsp.'——'.$v2['name'];
	    								$cityArr[$k2] = $v2;
	    								
	    								$v['ids'] = $v['ids'].$v2['id'].',';
	    								$cityArr[$k] = $v;
	    							}
	    						}
	    					}
	    				}
	    			}
	    		}
	    	}
	    	//写入缓存
	    	cache('cityArrCache',$cityArr,['expire'=>7200]);
    	}
    	
    	if($pid>0){
    		
    	}
    	return $cityArr;
    }
    
  
   
    //////////////////////////////////////////
    public function getAllProsCache(){
    	//cache('AllProsCache',null);
    	$allProsCache = cache('AllProsCache');
    	if(!$allProsCache){
    		$list = $this->where('parent_id',0)->field('id,name')->select();
    		$allProsCache = $list;
    		//写入缓存
    		cache('AllProsCache',$list);
    	}
    	return $allProsCache;
    }
    public function getAllCitiesCache($pid){
    	$allCitiesCache = null;
    	if($pid){
    		cache('AllCitiesCache_'.$pid,null);
	    	$allCitiesCache = cache('AllCitiesCache_'.$pid);
	    	if(!$allCitiesCache){
	    		$list = $this->where('parent_id',$pid)->field('id,name')->select();
	    		$allCitiesCache = $list;
	    		//写入缓存
	    		cache('AllCitiesCache_'.$pid,$allCitiesCache);
	    	}
    	}
    	return $allCitiesCache;
    }
}
