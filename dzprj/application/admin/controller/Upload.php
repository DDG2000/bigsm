<?php
namespace app\admin\controller ;
use \think\Request;
class Upload extends AdminController
{
    public function up(){
            return getJsonStr(fileUp($_FILES['Filedata']));
    }
    public function del(){
        $path=Request::instance()->param('path');
        return getJsonStr(fileDel($path));
    }
}