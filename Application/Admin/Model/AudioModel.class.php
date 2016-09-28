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

    public function selectLive($mobile, $openid)
    {
        $Model = M();
        if ($mobile !== 'null') {
            $sql = "SELECT m.`id` ID, m.`user_id` UserID, m.`name` Name, m.`folder`Folder, m.`image`Image, m.`sort` Sort, m.`vr_audio` VrAudio, s.`app_android_offline` AppAndroidOffline, s.`app_ios_offine` AppIOSOffline, s.`app_android_online` AppAndroidOnline, s.`app_ios_online` AppIOSOnline, s.`browser_pc` BrowserPC, s.`browser_android` BroswerAndroid, s.`browser_ios` BroswerIOS, s.`browser_ios_mp3` BroswerIOSMp3, m.`describe` 'Describe' FROM `tl_app_movies` m LEFT JOIN `tl_movie_show` s ON m.`movie_id`= s.`id` WHERE m.`sort`= 'public' UNION SELECT m.`id` ID, m.`user_id` UserID, m.`name` Name, m.`folder`Folder, m.`image`Image, m.`sort` Sort, m.`vr_audio` VrAudio, s.`app_android_offline` AppAndroidOffline, s.`app_ios_offine` AppIOSOffline, s.`app_android_online` AppAndroidOnline, s.`app_ios_online` AppIOSOnline, s.`browser_pc` BrowserPC, s.`browser_android` BroswerAndroid, s.`browser_ios` BroswerIOS, s.`browser_ios_mp3` BroswerIOSMp3, m.`describe` 'Describe' FROM `tl_app_movies` m LEFT JOIN `tl_movie_show` s ON m.`movie_id`= s.`id` LEFT JOIN `tl_user` u ON m.`user_id`= u.`id` WHERE m.`sort`= 'private' and u.`mobile=` " . $mobile;
        } elseif ($openid !== 'null') {
            $sql = "SELECT m.`id` ID, m.`user_id` UserID, m.`name` Name, m.`folder`Folder, m.`image`Image, m.`sort` Sort, m.`vr_audio` VrAudio, s.`app_android_offline` AppAndroidOffline, s.`app_ios_offine` AppIOSOffline, s.`app_android_online` AppAndroidOnline, s.`app_ios_online` AppIOSOnline, s.`browser_pc` BrowserPC, s.`browser_android` BroswerAndroid, s.`browser_ios` BroswerIOS, s.`browser_ios_mp3` BroswerIOSMp3, m.`describe` 'Describe' FROM `tl_app_movies` m LEFT JOIN `tl_movie_show` s ON m.`movie_id`= s.`id` WHERE m.`sort`= 'public' UNION SELECT m.`id` ID, m.`user_id` UserID, m.`name` Name, m.`folder`Folder, m.`image`Image, m.`sort` Sort, m.`vr_audio` VrAudio, s.`app_android_offline` AppAndroidOffline, s.`app_ios_offine` AppIOSOffline, s.`app_android_online` AppAndroidOnline, s.`app_ios_online` AppIOSOnline, s.`browser_pc` BrowserPC, s.`browser_android` BroswerAndroid, s.`browser_ios` BroswerIOS, s.`browser_ios_mp3` BroswerIOSMp3, m.`describe` 'Describe' FROM `tl_app_movies` m LEFT JOIN `tl_movie_show` s ON m.`movie_id`= s.`id` LEFT JOIN `tl_user` u ON m.`user_id`= u.`id` WHERE m.`sort`= 'private' and u.`openid`= '" . $openid . "'";
        } else {
            $sql = "SELECT m.`id` ID, m.`user_id` UserID, m.`name` Name, m.`folder`Folder, m.`image`Image, m.`sort` Sort, m.`vr_audio` VrAudio, s.`app_android_offline` AppAndroidOffline, s.`app_ios_offine` AppIOSOffline, s.`app_android_online` AppAndroidOnline, s.`app_ios_online` AppIOSOnline, s.`browser_pc` BrowserPC, s.`browser_android` BroswerAndroid, s.`browser_ios` BroswerIOS, s.`browser_ios_mp3` BroswerIOSMp3, m.`describe` 'Describe' FROM `tl_app_movies` m LEFT JOIN `tl_movie_show` s ON m.`movie_id`= s.`id` WHERE m.`sort`= 'public' UNION SELECT m.`id` ID, m.`user_id` UserID, m.`name` Name, m.`folder`Folder, m.`image`Image, m.`sort` Sort, m.`vr_audio` VrAudio, s.`app_android_offline` AppAndroidOffline, s.`app_ios_offine` AppIOSOffline, s.`app_android_online` AppAndroidOnline, s.`app_ios_online` AppIOSOnline, s.`browser_pc` BrowserPC, s.`browser_android` BroswerAndroid, s.`browser_ios` BroswerIOS, s.`browser_ios_mp3` BroswerIOSMp3, m.`describe` 'Describe' FROM `tl_app_movies` m LEFT JOIN `tl_movie_show` s ON m.`movie_id`= s.`id` LEFT JOIN `tl_user` u ON m.`user_id`= u.`id` WHERE m.`sort`= 'private' and u.`mobile`= 'null'";
        }
        $result = $Model->query($sql);
        if ($result != NULL) {
            return $result;
        } else {
            return false;
        }
    }
}