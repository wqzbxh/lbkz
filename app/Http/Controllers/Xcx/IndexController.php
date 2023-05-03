<?php
/**
 * Created by : PhpStorm
 * User: 哑巴湖大水怪（王海洋）
 * Date: 2023/5/3
 * Time: 20:44
 */

namespace App\Http\Controllers\Xcx;

use Illuminate\Support\Facades\Redis;

class IndexController
{
  public function index(){
      Redis::select(6);
      Redis::set('nn','22');
  }
}
