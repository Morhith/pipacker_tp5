<?php

namespace app\api\controller;

use think\Controller;
use think\Request;
use think\Db;
use app\api\controller\baseControll;

class Home extends baseControll
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $param = Request::instance()->param();
        $id=$param["user_id"];
        $pp_list = Db::table("pp_user")
                ->where("user_id=$id")
                ->select();
        // print_r($pp_list);exit();
        if (null == $pp_list[0]["user_bg"] || "" == $pp_list[0]["user_bg"]) {
           $pp_list[0]["user_bg"] = "";
        }
        if (null == $pp_list[0]["user_photo"] || "" == $pp_list[0]["user_photo"]) {
            $pp_list[0]["user_photo"] = "/public/static/pipacker/images/none-square.jpg";
        } 
        if(!empty($pp_list)){                
            $this->reJson("0",$pp_list); 
        }else{
           $this->reJson("1");
        }
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        //
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        $param = Request::instance()->param();

        $id=$param["user_id"];
        // 获取文件
        $files = Request()->file("user_bg");
        $info = $files->move("upload");
        if ($info){
            // 图片保存成功
            $param['user_bg'] = '/public/upload/'.$info->getSaveName();
            Db::table("pp_user")
                ->where("user_id=$id")
                ->update($param);
                // print_r($info);
            $this->reJson("1",$param['user_bg'],"成功更新");
        }else{
            // print_r($info);
            $this->reJson("0",array(),"数据丢失了...");exit();
        }  
    }

    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        //
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * 保存更新的资源
     *
     * @param  \think\Request  $request
     * @param  int  $id
     * @return \think\Response
     */
    public function update()
    {
        $param = Request::instance()->param();
        if(!empty($param)){
            $wid=$param["user_id"];
            Db::table("pp_user")
                    ->where("user_id=$wid")
                    ->update($param);
            $this->reJson("1",$param,"成功更新");
        }else{
            $this->reJson("0",array(),"数据丢失了...");
        }
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete()
    {
        //
        $param = Request::instance()->param();
        // print_r($param);exit();
        if(!empty($param)){
        // unset($param["comment_id"]);
        $comment_id = $param["comment_id"];
        if(Db::table("pp_message")->where("comment_id=$comment_id")->delete()){
            $this->reJson("0",array(),"已删除评论");
        }else{
            $this->reJson("2",array(),"服务器处理失败");
        }
        }else{
        $this->rejson("1",array(),"数据丢失了...");
        }
    }

    public function savehead(Request $request)
    {
        $param = Request::instance()->param();

        $id=$param["user_id"];
        // 获取文件
        $files = Request()->file("user_photo");
        $info = $files->move("upload");
        if ($info){
            // 图片保存成功
            $param['user_photo'] = '/public/upload/'.$info->getSaveName();
            Db::table("pp_user")
                ->where("user_id=$id")
                ->update($param);
            $this->reJson("1",$param['user_photo'],"成功更新");
        }else{
            $this->reJson("0",array(),"数据丢失了...");exit();
        }  
    }

    public function message()
    {
         $param = Request::instance()->param();
        if(!empty($param)){
            $comment_list = Db::table("pp_message")
                    ->where($param)
                    ->join("pp_user","pp_user.user_id = pp_message.user_id")
                    ->field("pp_user.user_name,pp_user.user_phone,pp_user.user_photo,pp_user.user_id,pp_message.comment,pp_message.update_time,pp_message.works_id,pp_message.comment_id")
                    ->order("pp_message.update_time desc")
                    ->limit(10)
                    ->select();
            session_start();
            if(!empty($_SESSION["user_info"])){
                $user_id = $_SESSION["user_info"]["user_id"];
                foreach($comment_list as $key => $val){
                    if($user_id == $val["user_id"]){
                        $comment_list[$key]["del_val"]=0;
                    }else{
                        $comment_list[$key]["del_val"]=1;
                    }
                }
            }else{
                foreach($comment_list as $key => $val){
                    $comment_list[$key]["del_val"]=1;
                }
            }
            $this->reJson("0",$comment_list);
        }else{
            $this->reJson("1",array(),"数据丢失了...");
        }
    }

    public function save_message()
    {
        $param = Request::instance()->param();
        // print_r($_FILES['works_src']);
        if(!empty($param)){
            // $param['works_para'] = json_encode($param['works_para']);
            // $param['works_src'] = saveFile('pic_src');
            $param['update_time'] = time();
            Db::table("pp_message")->insert($param);
            $redata["comment_id"] =  Db::table("pp_message")->getLastInsID();
            $redata["update_time"] = $param['update_time'];
            $this->reJson("0",$redata,"成功插入");
        }else{
            $this->reJson("2",array(),"数据丢失了...");
        }
    }
}

