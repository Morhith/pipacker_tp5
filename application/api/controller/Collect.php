<?php
namespace app\api\controller;

use think\Controller;
use think\Request;
use think\Db;
use app\api\controller\baseControll;

class Collect extends baseControll
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        //
        //
        $param = Request::instance()->param();
        if(!empty($param)){
            $collect_list = Db::table("pp_collect")
                            ->where($param)
                            ->join("pp_user","pp_user.user_id = pp_collect.user_id")
                            ->limit(10)
                            ->select();
            $this->reJson("0",$collect_list);
        }else{
            $this->reJson("1",array(),"数据丢失了...");
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
        //
        $param = Request::instance()->param();
        // print_r($_FILES['works_src']);
        if(!empty($param)){
            // $param['works_para'] = json_encode($param['works_para']);
            // $param['works_src'] = saveFile('pic_src');
            //$param['update_time'] = time();
            Db::table("pp_collect")->insert($param);
            $redata["comment_id"] =  Db::table("pp_collect")->getLastInsID();
            //$redata["update_time"] = $param['update_time'];
            $this->reJson("0",$redata,"成功插入");
        }else{
            $this->reJson("2",array(),"数据丢失了...");
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
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        //
		$param = Request::instance()->param();
		if(!empty($param)){
		unset($param["id"]);
		if(Db::table("pp_collect")->where($param)->delete()){
			$this->reJson("0",array(),"取消收藏了");
		}else{
			$this->reJson("2",array(),"服务器处理失败了");
		}
		}else{
		$this->rejson("1",array(),"数据丢失了...");
		}
    }
	public function getAuthor_collect(){
		$param = Request::instance()->param();
		unset($param["action"]);
		$id = $param['user_id'];
		session_start();
		if(!empty($_SESSION["user_info"])){
            $user_id = $_SESSION["user_info"]["user_id"];
        }else{
            $user_id = null;
        }
		if(!empty($param)){
			$collect_list = Db::table("pp_collect")
                            ->where("pp_collect.user_id = $id")
                            ->join("pp_works","pp_works.works_id = pp_collect.works_id")
							->field("pp_works.works_src,pp_collect.user_id,pp_collect.works_id")
                            //->limit(10)
                            ->select();
			$follower_list = Db::table("pp_follwer")
                            ->where(array("pp_follwer.user_id" => $user_id,"pp_follwer.follwer_user"=>$id))
							->field("pp_follwer.user_id")
							->find();
			if(!empty($follower_list)){
				$follwer_val = 0;
			}else{
				$follwer_val = 1;
			}
			$user_info = Db::table("pp_user")
						->where("pp_user.user_id = $id")
						->field("pp_user.user_id,pp_user.user_name,pp_user.user_photo")
						->find();
            $this->reJson("0",array("collect_list"=>$collect_list,"user_info"=>$user_info,"follwer_val"=>$follwer_val)); 
		}else{
			$this->reJson("0",array(),"数据丢失了...");
		}
	}
}
