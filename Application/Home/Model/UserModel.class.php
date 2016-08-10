<?php
//用户模型
namespace Home\Model;
use Think\Model;
class UserModel extends Model {
	function adduser($data){
		$r = $this->add($data);
		return $r;
	}
}