<?php
namespace Admin\Model;

use Think\Model\RelationModel;

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/15 0015
 * Time: 上午 11:37
 */
class AudioModel extends RelationModel
{
    public function selectAudio()
    {
        $result = $this->select();
        if ($result != NULL) {
            return $result;
        } else {
            return false;
        }
    }
}