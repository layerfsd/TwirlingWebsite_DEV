<?php
namespace Admin\Model;

use Think\Model\RelationModel;

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/15 0015
 * Time: 上午 11:37
 */
class LiveModel extends RelationModel
{
    public function selectLive()
    {
        $result = $this->select();
        if ($result != NULL) {
            return $result[0];
        } else {
            return false;
        }
    }
}