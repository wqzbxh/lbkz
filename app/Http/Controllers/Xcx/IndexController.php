<?php
/**
 * Created by : PhpStorm
 * User: 哑巴湖大水怪（王海洋）
 * Date: 2023/5/3
 * Time: 20:44
 */

namespace App\Http\Controllers\Xcx;

use App\Models\Permission;
use Illuminate\Support\Facades\Redis;

class IndexController
{
  public function index(){
      var_dump(timezone_identifiers_list());
//      $per = new  Permission();
//      $data = $per->getInfo();
//      $this->digui($data);

  }

  //递归测试
    public function digui($data,$pid = 0)
    {
        $aaa =[];
        foreach ($data as $key=>$value){
            if($value['father_id'] == $pid){
                $aaa[$key] = $this->digui($data,$value['id']);
            }else{
                return false;
            }
        }
        var_dump($aaa);
    }
}
