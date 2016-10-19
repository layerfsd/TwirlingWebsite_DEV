<?php
namespace Admin\Model;

use Think\Model\RelationModel;

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/15 0015
 * Time: 上午 11:37
 */
class SoftwareModel extends RelationModel
{
    public function selectAll()
    {
        $Model = M();
        $sql = "SELECT b.`id`, b.`name`,b.`version`, CONCAT(b.`oss_path`, b.`folder`, b.`file`, '_', b.`version`, '_', b.`file_create_date`, '.', b.`format`) 'file', b.`website_show_date`, b.`sort`FROM `twirling_dev`.`tl_software` b ORDER BY `id` ASC";
        $result = $Model->query($sql);
        if ($result != NULL) {
            return $result;
        } else {
            return false;
        }
    }
}