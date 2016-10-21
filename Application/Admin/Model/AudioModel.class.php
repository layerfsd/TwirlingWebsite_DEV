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
    public function selectAll()
    {
        $Model = M();
        $sql = "SELECT b.`id`, b.`title`, CONCAT(b.`oss_path`, b.`folder`, b.`coverphoto`) 'cover', CONCAT(b.`oss_path`, b.`folder`, b.`audio_path`) 'audio', b.`tag` FROM `twirling_dev`.`tl_audio` b ORDER BY `id` ASC";
        $result = $Model->query($sql);
        if ($result != NULL) {
            return $result;
        } else {
            return false;
        }
    }
}