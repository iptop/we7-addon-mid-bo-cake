<?php

/**

 * 中秋博饼模块

 *

 * 疯狂却不失细腻——厦门火池网络		www.weixiamen.cn

 */

defined('IN_IA') or exit('Access Denied');



class hc_newbbModuleSite extends WeModuleSite {



	public function doWebFormDisplay() {

		global $_W, $_GPC;

		$result = array('error' => 0, 'message' => '', 'content' => '');

		$result['content']['id'] = $GLOBALS['id'] = 'add-row-news-'.$_W['timestamp'];

		$result['content']['html'] = $this->template('item', TEMPLATE_FETCH);

		exit(json_encode($result));

	}

	

	public function doWebCreditlog(){

		global $_W,$_GPC;

		$rid = intval($_GPC['id']);

		$psize = 30;

		$pindex = max(1, intval($_GPC['page']));

		$total = pdo_fetchall( "  SELECT COUNT(*) FROM ".tablename('hc_newbb_dayscredit')." AS d LEFT JOIN ".tablename('hc_newbb_member')." AS m ON d.mid=m.id WHERE d.rid='".$rid."' GROUP BY d.openid " );

		$total = sizeof($total);

		$creditlog = pdo_fetchall(" SELECT SUM(d.credit) as credit, m.mobile, m.createtime, m.nickname FROM ".tablename('hc_newbb_dayscredit')." AS d LEFT JOIN ".tablename('hc_newbb_member')." AS m ON d.mid=m.id WHERE d.rid='".$rid."' GROUP BY d.openid ORDER BY credit  DESC  LIMIT ".($pindex - 1) * $psize.",{$psize}  ");

		$pager = pagination($total, $pindex, $psize);

		include $this->template('creditlog');

	}

	

	public function doWebCreditLogSearch(){

		global $_W,$_GPC;

		$rid = intval($_GPC['id']);

		$key = $_GPC['key'];

		if(empty($key)){

			message("请选择查询条件");

		}

		$keyword = trim($_GPC['keyword']);

		if(empty($keyword) && $keyword !=0){

			message("请输入查询条件");

		}

		if($key == '积分'){

			if(!is_numeric($keyword)){

				message('请输入数字');

			}

			$keyword = intval($keyword);

			$total = pdo_fetchall(" SELECT SUM(d.credit) as credit, m.mobile, m.createtime, m.nickname FROM ".tablename('hc_newbb_dayscredit')." AS d LEFT JOIN ".tablename('hc_newbb_member')." AS m ON d.mid=m.id WHERE d.rid='".$rid."' AND d.credit >= '".$keyword."'GROUP BY d.openid ORDER BY credit  DESC  ");

			$total = sizeof($total);

			$psize = 30;

			$pindex = max(1, intval($_GPC['page']));

			$creditlog = pdo_fetchall(" SELECT SUM(d.credit) as credit, m.mobile, m.createtime, m.nickname FROM ".tablename('hc_newbb_dayscredit')." AS d LEFT JOIN ".tablename('hc_newbb_member')." AS m ON d.mid=m.id WHERE d.rid='".$rid."' AND d.credit >= '".$keyword."'GROUP BY d.openid ORDER BY credit  DESC  LIMIT ".($pindex - 1) * $psize.",{$psize}   ");

			$pager = pagination($total, $pindex, $psize);

	

		}

		if($key == '昵称'){

			$creditlog = pdo_fetchall(" SELECT SUM(d.credit) as credit, m.mobile, m.createtime, m.nickname FROM ".tablename('hc_newbb_dayscredit')." AS d LEFT JOIN ".tablename('hc_newbb_member')." AS m ON d.mid=m.id WHERE d.rid='".$rid."' AND m.nickname like '%".$keyword."%' GROUP BY d.openid ORDER BY credit  DESC  ");

		}

		if($key == '手机号码'){

			$creditlog = pdo_fetchall(" SELECT SUM(d.credit) as credit, m.mobile, m.createtime, m.nickname FROM ".tablename('hc_newbb_dayscredit')." AS d LEFT JOIN ".tablename('hc_newbb_member')." AS m ON d.mid=m.id WHERE d.rid='".$rid."' AND m.mobile like '%".$keyword."%' GROUP BY d.openid ORDER BY credit  DESC  ");

		}

		include $this->template('creditlog');

	}

	

	//兑换请求管理

	public function doWeborder(){

		global $_W,$_GPC;

		$psize = 30;

		$pindex = max(1, intval($_GPC['page']));

		$total = pdo_fetchcolumn( " SELECT COUNT(id) FROM ".tablename('hc_newbb_exchange_order')." WHERE weid=".$_W['weid']." " );

		$order = pdo_fetchall( " SELECT o.*, g.name FROM ".tablename('hc_newbb_exchange_order')." AS o LEFT JOIN ".tablename('hc_newbb_goods')." AS g ON o.goodsid = g.id  WHERE o.weid='".$_W['weid']."' ORDER BY createtime DESC LIMIT ".($pindex - 1) * $psize.",{$psize} " );

		$pager = pagination($total, $pindex, $psize);



		include $this->template('order_index');

	}

	

	

	//兑换商品领取

	public function doWeborder_status(){

		global $_W,$_GPC;

		$orderid = $_GPC['orderid'];

		$status = $_GPC['status'];

		$order = pdo_fetch( " SELECT * FROM ".tablename('hc_newbb_exchange_order')." WHERE id=".$orderid." " );

		if(!empty($order)){

			$order['status'] = $status;

			pdo_update('hc_newbb_exchange_order',$order,array('id'=>$orderid));

		}

		$url = $_GPC['url'];

		message('修改成功',$url,'success');

		

	}

	

	public function doWeborderexcle(){

		global $_W,$_GPC;



		

		include $this->template('order_excle');

	}

	

	public function doWebordersearch(){

		global $_W,$_GPC;

		$key = $_GPC['key'];

		$keyword = $_GPC['keyword'];

		if(empty($key)){

			message("请选择查询条件");

		}

		if($key == "昵称"){

			$psize = 30;

			$pindex = max(1, intval($_GPC['page']));

			$total = pdo_fetchcolumn( " SELECT COUNT(id) FROM ".tablename('hc_newbb_exchange_order')." WHERE weid=".$_W['weid']." " );

			$order = pdo_fetchall( " SELECT o.*, g.name FROM ".tablename('hc_newbb_exchange_order')." AS o LEFT JOIN ".tablename('hc_newbb_goods')." AS g ON o.goodsid = g.id  WHERE o.weid='".$_W['weid']."' AND o.realname like '%".$keyword."%' ORDER BY createtime DESC LIMIT ".($pindex - 1) * $psize.",{$psize} " );

			$pager = pagination($total, $pindex, $psize);

		}

		if($key == "联系方式"){

			$psize = 30;

			$pindex = max(1, intval($_GPC['page']));

			$total = pdo_fetchcolumn( " SELECT COUNT(id) FROM ".tablename('hc_newbb_exchange_order')." WHERE weid=".$_W['weid']." " );

			$order = pdo_fetchall( " SELECT o.*, g.name FROM ".tablename('hc_newbb_exchange_order')." AS o LEFT JOIN ".tablename('hc_newbb_goods')." AS g ON o.goodsid = g.id  WHERE o.weid='".$_W['weid']."' AND o.mobile like '%".$keyword."%' ORDER BY createtime DESC LIMIT ".($pindex - 1) * $psize.",{$psize} " );

			$pager = pagination($total, $pindex, $psize);

		}

		include $this->template('order_index');

	}

	

	

	//excle导出功能

	

	public function doWebexcle() {

		global $_GPC,$_W;



		error_reporting(E_ALL);

		ini_set('display_errors', TRUE);

		ini_set('display_startup_errors', TRUE);

		if (PHP_SAPI == 'cli')

	die('This example should only be run from a Web Browser');

	

	require_once '../addons/hc_newbb/hc_public/Classes/PHPExcel.php';



	$rid=109;

	if(empty($rid)){

		message('抱歉，传递的参数错误！','', 'error');              

	}

	

	$order = pdo_fetchall( " SELECT o.*, g.name FROM ".tablename('hc_newbb_exchange_order')." AS o LEFT JOIN ".tablename('hc_newbb_goods')." AS g ON o.goodsid = g.id  WHERE o.weid='".$_W['weid']."' ORDER BY createtime DESC " );



	

			

 

// Create new PHPExcel object

$objPHPExcel = new PHPExcel();



// Set document properties

$objPHPExcel->getProperties()->setCreator("火池网络")

							 ->setLastModifiedBy("火池网络")

							 ->setTitle("Office 2007 XLSX Test Document")

							 ->setSubject("Office 2007 XLSX Test Document")

							 ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")

							 ->setKeywords("office 2007 openxml php")

							 ->setCategory("Test result file");





// Add some data

$objPHPExcel->setActiveSheetIndex(0)

            ->setCellValue('A1', '昵称')

            ->setCellValue('B1', '手机')

            ->setCellValue('C1', '地址')

            ->setCellValue('D1', '兑换商品')

            ->setCellValue('E1', '兑换数量')

            ->setCellValue('F1', '兑换时间');

$i=2;

$ii = 1;



foreach($order as $item){

$objPHPExcel->setActiveSheetIndex(0)			

            ->setCellValue('A'.$i, $item['realname'])

            ->setCellValue('B'.$i, $item['mobile'])

            ->setCellValue('C'.$i, $item['address'])

            ->setCellValue('D'.$i, $item['name'])

            ->setCellValue('E'.$i, $item['num'])

            ->setCellValue('F'.$i, date("Y-m-d H:i:s",$item['createtime']));

$i++;		

$ii++;

}					

$objPHPExcel->getActiveSheet()->getStyle('A1:H1')->getFont()->setBold(true);

$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(12); 

$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20); 

$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(12); 

$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(18); 



// Rename worksheet

$time=time();

$objPHPExcel->getActiveSheet()->setTitle('中秋博饼'.$time);





// Set active sheet index to the first sheet, so Excel opens this as the first sheet

$objPHPExcel->setActiveSheetIndex(0);





// Redirect output to a client’s web browser (Excel2007)

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

header('Content-Disposition: attachment;filename="兑换请求'.date("Y-m-d H:i:s",$time).'.xlsx"');

header('Cache-Control: max-age=0');



$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

$objWriter->save('php://output');



exit;



}

	

	

	//红包提现记录

	public function doWebHongbao_withdraw(){

		global $_W,$_GPC;



		$total = pdo_fetchcolumn(  " SELECT COUNT(`id`) FROM ".tablename('hc_newbb_hongbao_tixian_log')." WHERE uniacid='".$_W['uniacid']."' " );;

		$psize = 30;

		$pindex = max(1, intval($_GPC['page']));

		$list = pdo_fetchall( " SELECT * FROM ".tablename('hc_newbb_hongbao_tixian_log')." WHERE uniacid='".$_W['uniacid']."' ORDER BY id DESC LIMIT ".($pindex - 1) * $psize.",{$psize}  " );

		$total_money = pdo_fetchcolumn(" SELECT SUM(`money`) FROM ".tablename('hc_newbb_hongbao_tixian_log')." WHERE uniacid='".$_W['uniacid']."' AND status=1 ");

		$pager = pagination($total, $pindex, $psize);





		include $this->template('hongbao_withdraw');

	}





	//红包提现搜索

	public function doWebhongbao_withdraw_search(){

		global $_W,$_GPC;



		$key = $_GPC['key'];

		$keyword = $_GPC['keyword'];

		if(empty($key)){

			message('选择查询条件');

		}

		if(empty($keyword)){

			message('输入查询信息');

		}



		$psize = 30;

		$pindex = max(1, intval($_GPC['page']));

		if($key == 'nickname'){

			$total = pdo_fetchcolumn(  " SELECT COUNT(`id`) FROM ".tablename('hc_newbb_hongbao_tixian_log')." WHERE uniacid='".$_W['	uniacid']."' AND nickname like '%".$keyword."%' " );;



			$list = pdo_fetchall( " SELECT * FROM ".tablename('hc_newbb_hongbao_tixian_log')." WHERE uniacid='".$_W['uniacid']."' AND nickname like '%".$keyword."%' ORDER BY id DESC LIMIT ".($pindex - 1) * $psize.",{$psize}  " );



			$total_money = 0;

			foreach ($list as $key => $value) {

				$total_money = $value['money'] + $total_money;

			}



		}

		if($key == 'money'){

			$total = pdo_fetchcolumn(  " SELECT COUNT(`id`) FROM ".tablename('hc_newbb_hongbao_tixian_log')." WHERE uniacid='".$_W['	uniacid']."' AND money>='".$keyword."' " );;



			$list = pdo_fetchall( " SELECT * FROM ".tablename('hc_newbb_hongbao_tixian_log')." WHERE uniacid='".$_W['uniacid']."' AND money>='".$keyword."' ORDER BY id DESC LIMIT ".($pindex - 1) * $psize.",{$psize}  " );



			$total_money = pdo_fetchcolumn(" SELECT SUM(`money`) FROM ".tablename('hc_newbb_hongbao_tixian_log')." WHERE uniacid='".$_W['uniacid']."' AND money>='".$keyword."' AND status=1 ");



		}





		include $this->template('hongbao_withdraw');

	}

	

	//积分提现记录

	public function doWebwithdraw(){

		global $_W,$_GPC;



		$total = pdo_fetchcolumn(  " SELECT COUNT(`id`) FROM ".tablename('hc_newbb_tixian_log')." WHERE uniacid='".$_W['uniacid']."' " );;

		$psize = 30;

		$pindex = max(1, intval($_GPC['page']));

		$list = pdo_fetchall( " SELECT * FROM ".tablename('hc_newbb_tixian_log')." WHERE uniacid='".$_W['uniacid']."' ORDER BY id DESC LIMIT ".($pindex - 1) * $psize.",{$psize}  " );

		$total_money = pdo_fetchcolumn(" SELECT SUM(`money`) FROM ".tablename('hc_newbb_tixian_log')." WHERE uniacid='".$_W['uniacid']."' AND status=1 ");

		$pager = pagination($total, $pindex, $psize);





		include $this->template('withdraw');

	}



	//提现搜索

	public function doWebwithdraw_search(){

		global $_W,$_GPC;



		$key = $_GPC['key'];

		$keyword = $_GPC['keyword'];

		if(empty($key)){

			message('选择查询条件');

		}

		if(empty($keyword)){

			message('输入查询信息');

		}



		$psize = 30;

		$pindex = max(1, intval($_GPC['page']));

		if($key == 'nickname'){

			$total = pdo_fetchcolumn(  " SELECT COUNT(`id`) FROM ".tablename('hc_newbb_tixian_log')." WHERE uniacid='".$_W['	uniacid']."' AND nickname like '%".$keyword."%' " );;



			$list = pdo_fetchall( " SELECT * FROM ".tablename('hc_newbb_tixian_log')." WHERE uniacid='".$_W['uniacid']."' AND nickname like '%".$keyword."%' ORDER BY id DESC LIMIT ".($pindex - 1) * $psize.",{$psize}  " );



			$total_money = 0;

			foreach ($list as $key => $value) {

				$total_money = $value['money'] + $total_money;

			}



		}

		if($key == 'money'){

			$total = pdo_fetchcolumn(  " SELECT COUNT(`id`) FROM ".tablename('hc_newbb_tixian_log')." WHERE uniacid='".$_W['	uniacid']."' AND money>='".$keyword."' " );;



			$list = pdo_fetchall( " SELECT * FROM ".tablename('hc_newbb_tixian_log')." WHERE uniacid='".$_W['uniacid']."' AND money>='".$keyword."' ORDER BY id DESC LIMIT ".($pindex - 1) * $psize.",{$psize}  " );



			$total_money = pdo_fetchcolumn(" SELECT SUM(`money`) FROM ".tablename('hc_newbb_tixian_log')." WHERE uniacid='".$_W['uniacid']."' AND money>='".$keyword."' AND status=1 ");



		}





		include $this->template('withdraw');

	}

	

	//排行榜查看

	public function doWebrankview(){

		global $_W,$_GPC;

		//$today = pdo_fetchall(" SELECT rid, nickname, SUM(credit) AS credit FROM ".tablename('hc_newbb_user')." WHERE  weid='".$_W['uniacid']."' GROUP BY mid ORDER BY credit DESC  ");



		$total = pdo_fetchall( " SELECT u.rid, u.nickname,m.username,m.mobile, SUM(u.credit) AS credit, m.createtime, m.openid FROM ".tablename('hc_newbb_user')." AS u LEFT JOIN ".tablename('hc_newbb_member')." AS m ON u.mid=m.id WHERE  u.weid='".$_W['uniacid']."' GROUP BY u.mid ORDER BY credit DESC  " );

		$total = sizeof($total);

		$psize = 30;

		$pindex = max(1, intval($_GPC['page']));

		$today = pdo_fetchall(" SELECT u.rid, u.nickname,m.username,m.mobile, SUM(u.credit) AS credit, m.createtime, m.openid FROM ".tablename('hc_newbb_user')." AS u LEFT JOIN ".tablename('hc_newbb_member')." AS m ON u.mid=m.id WHERE  u.weid='".$_W['uniacid']."' GROUP BY u.mid ORDER BY credit DESC LIMIT ".($pindex - 1) * $psize.",{$psize}  ");

		$pager = pagination($total, $pindex, $psize);

		include $this->template('rankview_index');

	}
	//日积分排行榜查看

	public function doWebday_rank_view(){

		global $_W,$_GPC;

		$day_time = $_GPC['day_time'];
		if(empty($day_time)){
			$start_time = $this->get_start_time(TIMESTAMP);
		}else{
			$start_time = strtotime($day_time);
		}
		$end_time = $start_time + 3600*24; 


		$today = pdo_fetchcolumn(" SELECT COUNT('u.id')  FROM ".tablename('hc_newbb_user')." AS u LEFT JOIN ".tablename('hc_newbb_member')." AS m ON u.mid=m.id WHERE  u.weid='".$_W['uniacid']."' AND u.createtime>='".$start_time."' AND u.createtime<'".$end_time."' GROUP BY u.mid   ");

		$total = sizeof($total);

		$psize = 30;

		$pindex = max(1, intval($_GPC['page']));

		$today = pdo_fetchall(" SELECT u.rid, u.nickname,m.username,m.mobile, SUM(u.credit) AS credit, m.createtime, m.openid FROM ".tablename('hc_newbb_user')." AS u LEFT JOIN ".tablename('hc_newbb_member')." AS m ON u.mid=m.id WHERE  u.weid='".$_W['uniacid']."' AND u.createtime>='".$start_time."' AND u.createtime<'".$end_time."' GROUP BY u.mid ORDER BY credit DESC LIMIT ".($pindex - 1) * $psize.",{$psize}  ");





		$pager = pagination($total, $pindex, $psize);

		include $this->template('rankview_index');

	}

	

	//总积分排行榜查询

	public function doWebrankviewsearch(){

		global $_W,$_GPC;

		$key = $_GPC['key'];

		$keyword = $_GPC['keyword'];

		if($key == '昵称'){

			$today = pdo_fetchall(" SELECT u.rid, u.nickname, SUM(u.credit) AS credit, m.createtime, m.openid FROM ".tablename('hc_newbb_user')." AS u LEFT JOIN ".tablename('hc_newbb_member')." AS m ON u.mid=m.id WHERE  u.weid='".$_W['uniacid']."' AND u.nickname like '%".$keyword."%' GROUP BY u.mid ORDER BY credit DESC  ");

		}

		if($key == '积分前n位'){

			$keyword = intval($keyword);

			if(!is_numeric($keyword)){

				message("n必须为正整数");

			}

			if($keyword <=0){

				message("n必须为正整数");

			}

			$today = pdo_fetchall(" SELECT u.rid, u.nickname, SUM(u.credit) AS credit, m.createtime, m.openid FROM ".tablename('hc_newbb_user')." AS u LEFT JOIN ".tablename('hc_newbb_member')." AS m ON u.mid=m.id WHERE  u.weid='".$_W['uniacid']."'  GROUP BY u.mid ORDER BY credit DESC LIMIT ".$keyword." ");

			

		}

		include $this->template('rankview_index');

	}

	

	//排名导出

	public function doWebranksearch(){

		global $_W,$_GPC;



		include $this->template('rank_search');

	}

	

	//排名excle

	public function doWebrankexcle(){

		global $_W,$_GPC;

	

		$key = $_GPC['key'];

		$keyword = $_GPC['keyword'];

		if(empty($keyword)){

			message("请输入查询信息");

		}

		if($key == '昵称'){

			$today = pdo_fetchall(" SELECT u.rid, u.nickname,m.username,m.mobile, SUM(u.credit) AS credit, m.createtime, m.openid FROM ".tablename('hc_newbb_user')." AS u LEFT JOIN ".tablename('hc_newbb_member')." AS m ON u.mid=m.id WHERE  u.weid='".$_W['uniacid']."' AND u.nickname like '%".$keyword."%' GROUP BY u.mid ORDER BY credit DESC  ");

		}

		if($key == '积分前n位'){

			$keyword = intval($keyword);

			if(!is_numeric($keyword)){

				message("n必须为正整数");

			}

			if($keyword <=0){

				message("n必须为正整数");

			}

			$today = pdo_fetchall(" SELECT u.rid, u.nickname,m.username,m.mobile, SUM(u.credit) AS credit, m.createtime, m.openid FROM ".tablename('hc_newbb_user')." AS u LEFT JOIN ".tablename('hc_newbb_member')." AS m ON u.mid=m.id WHERE  u.weid='".$_W['uniacid']."'  GROUP BY u.mid ORDER BY credit DESC LIMIT ".$keyword." ");

			

		}

		

				error_reporting(E_ALL);

		ini_set('display_errors', TRUE);

		ini_set('display_startup_errors', TRUE);

		if (PHP_SAPI == 'cli')

	die('This example should only be run from a Web Browser');

	

	require_once '../addons/hc_newbb/hc_public/Classes/PHPExcel.php';



	$rid=109;

	if(empty($rid)){

		message('抱歉，传递的参数错误！','', 'error');              

	}

	

	$order = $today;



	

			

 

// Create new PHPExcel object

$objPHPExcel = new PHPExcel();



// Set document properties

$objPHPExcel->getProperties()->setCreator("火池网络")

							 ->setLastModifiedBy("火池网络")

							 ->setTitle("Office 2007 XLSX Test Document")

							 ->setSubject("Office 2007 XLSX Test Document")

							 ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")

							 ->setKeywords("office 2007 openxml php")

							 ->setCategory("Test result file");





// Add some data

$objPHPExcel->setActiveSheetIndex(0)

            ->setCellValue('A1', '昵称')

            ->setCellValue('B1', '手机')

            ->setCellValue('C1', '姓名')

            ->setCellValue('D1', '积分')

            ->setCellValue('E1', '注册时间')

            ->setCellValue('F1', '排名');

$i=2;

$ii = 1;



foreach($order as $item){

$objPHPExcel->setActiveSheetIndex(0)			

            ->setCellValue('A'.$i, $item['nickname'])

            ->setCellValue('B'.$i, $item['mobile'])

            ->setCellValue('C'.$i, $item['username'])

            ->setCellValue('D'.$i, $item['credit'])

            ->setCellValue('E'.$i, date("Y-m-d H:i:s",$item['createtime']))

            ->setCellValue('F'.$i, $ii);

$i++;		

$ii++;

}					

$objPHPExcel->getActiveSheet()->getStyle('A1:H1')->getFont()->setBold(true);

$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(12); 

$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20); 

// Rename worksheet

$time=time();

$objPHPExcel->getActiveSheet()->setTitle('中秋博饼'.$time);





// Set active sheet index to the first sheet, so Excel opens this as the first sheet

$objPHPExcel->setActiveSheetIndex(0);





// Redirect output to a client’s web browser (Excel2007)

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

header('Content-Disposition: attachment;filename="积分排名'.date("Y-m-d H:i",$time).'.xlsx"');

header('Cache-Control: max-age=0');



$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

$objWriter->save('php://output');



exit;



		

		

		

	}

	

	//积分兑换管理

	public function doWebexchange(){

		global $_W,$_GPC;

		$goods = pdo_fetchall( " SELECT * FROM ".tablename('hc_newbb_goods')." WHERE weid='".$_W['weid']."' " );

		include $this->template('exchange_index');

	}

	

		

	//积分兑换管理

	public function doWebexchangeadd(){

		global $_W,$_GPC;

		load()->func('tpl');

		$id = intval($_GPC['id']);

		if(!empty($id)){

			$servicer = pdo_fetch( " SELECT * FROM ".tablename('hc_newbb_goods')." WHERE id='".$id."' " );

		}

		include $this->template('exchange_add');

	}

	

	public function doWebexchangedelete(){

		global $_W,$_GPC;

		$id = intval($_GPC['id']);

		if(!empty($id)){

			pdo_delete('hc_newbb_goods',array('id'=>$id));

			message('删除成功',$this->createWebUrl('exchange'),'success');

		}else{

			message('要删除的数据不存在');

		}

	}

	

	//商品数据保存

	public function doWebgoodssave(){

		global $_W,$_GPC;

		$id = intval($_GPC['id']);

		$actime = $_GPC['actime'];

		$insert = array(

			'weid' => $_W['uniacid'],

			'name' => $_GPC['name'],

			'num' => $_GPC['num'],

			'price' => $_GPC['price'],

			'jifen' => $_GPC['jifen'],

			'img' => $_GPC['img'],

			'limit' => $_GPC['limit'],

			'start_time' => strtotime($_GPC['start_time']),

			'end_time' => strtotime($_GPC['end_time']),

			'content' => htmlspecialchars_decode($_GPC['content']),

			'order' => $_GPC['order'],

		);

		if(empty($id)){

			pdo_insert('hc_newbb_goods',$insert);

			message('保存成功',$this->createWebUrl('exchange'),'success');

		}else{

			$insert['id'] = $id;

			pdo_update('hc_newbb_goods',$insert,array('id'=>$insert['id']));

			message('保存成功',$this->createWebUrl('exchange'),'success');

		}

		

	}

	

	public function doMobileindex(){

		global $_W,$_GPC;

		$rid = intval($_GPC['id']);

		$fromuser = $_W['openid'];

		if(empty($rid)){

			message('游戏不存在');

		}

		$weibb = pdo_fetch( " SELECT * FROM ".tablename('hc_newbb_reply')." WHERE rid=".$rid." " );

		//检测浏览器

		$this->checkBowser();

		//为转发者增加次数

		if($_GPC['share'] == 'share'){

			$shareid = intval($_GPC['shareid']);

			if(!empty($shareid)){

				$this->addLotteryNum($shareid,$weibb,$rid);

			}

		}

		//检测用户是否已关注公众号

		$this->checkFollow($rid);

		//第一次玩，插入member表

		//$member = pdo_fetch( " SELECT * FROM ".tablename('hc_newbb_member')." WHERE openid='".$fromuser."' AND rid=".$rid." " );

		$member = pdo_fetch( " SELECT * FROM ".tablename('hc_newbb_member')." WHERE openid='".$fromuser."' AND weid=".$_W['uniacid']." " );
		
		if(empty($member)){

			$insert = array(

				'weid' => $_W['uniacid'],

				'openid' => $_W['openid'],

				//'rid' => $rid,

				'createtime' => TIMESTAMP,

			);

			$info = $this->getMemberInfo();

			$insert['nickname'] = $info['nickname'];

			$insert['headimg'] = $info['headimgurl'];

			$flag = pdo_insert('hc_newbb_member',$insert);

			if($flag == false){

				message('非法访问，请重新发送消息进入游戏');

			}

		$member = pdo_fetch( " SELECT * FROM ".tablename('hc_newbb_member')." WHERE openid='".$fromuser."' AND rid=".$rid." " );

		}



		//如果用户的昵称时空，再获取一次

		if(empty($member['nickname'])){

			$info = $this->getMemberInfo();

			$member['nickname'] = $info['nickname'];

			$member['headimg'] = $info['headimgurl'];

			pdo_update('hc_newbb_member',$member,array('id'=>$member['id']));

		}



		include $this->template('index');

	}

	



	//规则说明界面

	public function doMobilerule_des(){

		global $_W,$_GPC;

		$setting = $this->module['config'];

		include $this->template('rule_des');

	}

	

	

	//包厢选择页面

	public function doMobileselect(){

		global $_W,$_GPC;

		$rid = intval($_GPC['rid']);

		if(empty($rid)){

			$rid = intval($_GPC['id']);

		}

		$weibb = pdo_fetch(" SELECT * FROM ".tablename('hc_newbb_reply')." WHERE rid='".$rid."' ");

		$this->checkBowser();

		$this->checkFollow($rid);

		$reply1 = pdo_fetchall( " SELECT * FROM ".tablename('hc_newbb_reply')." WHERE weid=".$_W['uniacid']." AND baoxiangsize=2 ORDER BY baoxiangorder DESC " );

		$reply0 = pdo_fetchall( " SELECT * FROM ".tablename('hc_newbb_reply')." WHERE weid=".$_W['uniacid']." AND baoxiangsize=3 ORDER BY baoxiangorder DESC " );

		$img = $this->module['config']['headpic'];

		//今日未博

		$todayStart = strtotime(date('Y-m-d'));

		$openid = $_W['openid'];

		$start = pdo_fetchall( " SELECT * FROM ".tablename('hc_newbb_dayscredit')." WHERE openid='".$openid."' AND weid=".$_W['uniacid']." AND createtime='".$todayStart."'  " );

		$check_start = array();

		foreach($start as $n=>$a){

			$check_start[$a['rid']] = 4;

		}

		//访问量，参与人数，博饼次数
		$setting = $this->module['config'];
		$setting['pv'] = $setting['pv'] + 1;
		$this->saveSettings($setting);

		$member_total = pdo_fetchcolumn( " SELECT COUNT(`id`) FROM ".tablename('hc_newbb_member')." WHERE weid='".$_W['uniacid']."' " );

		$bobing_total = pdo_fetchcolumn( " SELECT COUNT(`id`) FROM ".tablename('hc_newbb_user')." WHERE weid='".$_W['uniacid']."' " );


		include $this->template('baoxiang');

	}

	

	//修改资料

	public function doMobileregist(){

		global $_GPC,$_W;

		$rid = intval($_GPC['id']);

		$this->checkBowser();

		$this->checkFollow($rid);

		$weibb = pdo_fetch( " SELECT * FROM ".tablename('hc_newbb_reply')." WHERE rid='".$rid."' " );

		//$member = pdo_fetch( " SELECT * FROM ".tablename('hc_newbb_member')." WHERE openid='".$_W['openid']."' AND rid=".$rid." " );

		$member = pdo_fetch( " SELECT * FROM ".tablename('hc_newbb_member')." WHERE openid='".$_W['openid']."' AND weid=".$_W['uniacid']." " );

		if($_GPC['op'] == 'regist'){

			$nickname = trim($_GPC['nickname']);

			$mobile = trim($_GPC['mobile']);

			if(empty($mobile)){

				message('请填写手机号码');

			}

			$chars = "/^((\(\d{2,3}\))|(\d{3}\-))?1(3|5|8|9)\d{9}$/";

			$flag = preg_match($chars, $mobile);

			if($flag == false){

				message("请填写正确的手机格式");

			}

			if(empty($member)){

				$insert = array(

					'weid' => $_W['uniacid'],

					'openid' => $_W['openid'],

					'nickname' => $nickname,

					'mobile' => $mobile,

					//'rid' => $rid,

				);

				pdo_insert('hc_newbb_member',$insert);

			}else{

				$member['nickname'] = $nickname;

				$member['mobile'] = $mobile;

				pdo_update('hc_newbb_member',$member,array('id'=>$member['id']));

			}

			message('保存成功',$this->createMobileUrl('Lottery',array('id'=>$rid)),'success');

		}

		

		include $this->template('register');

	}



	//红包提现

	public function doMobilehongbao_withdraw(){

		global $_W,$_GPC;

		$setting = $this->module['config'];

		$openid = $_W['openid'];

		if($setting['is_hongbao'] != 1){

			message('错误的链接地址');

		}



		if(empty($openid)){

			message('请从公众号回复关键字进入');

		}



		$money_total = pdo_fetch( " SELECT * FROM ".tablename('hc_newbb_hongbao_total')." WHERE openid='".$_W['openid']."'  AND uniacid='".$_W['uniacid']."' " );

		

		if(checksubmit('submit')){

			$money = intval($_GPC['credit2']); 

			$money = abs($money);

			if(!is_int($money) || $money <0){

				message("请输入正整数");

			}



			//查看是否有足够积分

			if($money_total['money'] < $money){

				message('账户金额不足');

				exit;

			}

			//查看是否超过最大金额

			$setting = $this->module['config'];

			if($money < $setting['hongbao_min']){

				message("小于最低提现金额!");

				exit;

			}

			

			$member = pdo_fetch( " SELECT nickname,id FROM ".tablename('hc_newbb_member')." WHERE openid='".$_W['openid']."' AND weid='".$_W['uniacid']."' " );

			



			$insert = array(

				'uniacid' => $_W['uniacid'],

				'openid' => $_W['openid'],

				'money' => $money,

				'createtime' => TIMESTAMP,

				'status' => 0,

				'nickname' => $member['nickname'],

				'mid' => $member['id'],

			);

			pdo_insert('hc_newbb_hongbao_tixian_log',$insert);

			$tixian_id = pdo_insertid();



			$money_total['money'] = $money_total['money'] - $money;

			pdo_update('hc_newbb_hongbao_total',$money_total,array('id'=>$money_total['id']));



			$callback = $this->get_red_bag($tixian_id,1);

			if($callback['return_code'] == false){

				pdo_update('hc_newbb_hongbao_tixian_log',array('false_return'=>$callback['return_msg']),array('id'=>$tixian_id));

				message('提现失败，请稍后再试!');

			}		



			if($callback['return_code'] == 'SUCCESS'){

				if($callback['return_msg'] != '发送成功'){

					pdo_update('hc_newbb_hongbao_tixian_log',array('status'=>1),array('id'=>$tixian_id));

					message('提现成功，请及时查看公众号领取红包',$this->createMobileUrl('select'),'success');

				}else{

					pdo_update('hc_newbb_hongbao_tixian_log',array('false_return'=>$callback['return_msg']),array('id'=>$tixian_id));

					message('提现失败，请稍后再试');

				}

			}



		}









		include $this->template('hongbao_withdraw');

	}



	//积分提现

	public function doMobiletixian(){

		global $_W,$_GPC;

		$setting = $this->module['config'];

		$openid = $_W['openid'];

		if($setting['is_tixian'] != 1 || $setting['tixian_rate'] == 0){

			message('错误的链接地址');

		}



		if(empty($openid)){

			message('请从公众号回复关键字进入');

		}

		$uid = pdo_fetchcolumn( " SELECT * FROM ".tablename('mc_mapping_fans')." WHERE openid='".$openid."' AND uniacid='".$_W['uniacid']."' " );

		$fans = pdo_fetch( " SELECT * FROM ".tablename('mc_members')." WHERE uid='".$uid."' " );

		if(empty($fans)){

			message('请从公众号回复关键字进入！');

		}



		$withdraw = pdo_fetchall( " SELECT * FROM ".tablename('hc_newbb_tixian_log')." WHERE openid='".$openid."' AND uniacid='".$_W['uniacid']."' AND status=1   " );



		if(checksubmit('submit')){

			$money = intval($_GPC['credit2']); 

			$money = abs($money);

			if(!is_int($money) || $money <0){

				message("请输入正整数");

			}

			$credit = $money * intval($setting['tixian_rate']);



			//查看是否有足够积分

			if($fans['credit1'] < $credit){

				message('积分余额不足');

				exit;

			}

			$nickname = pdo_fetchcolumn( " SELECT nickname FROM ".tablename('hc_newbb_member')." WHERE openid='".$_W['openid']."' AND weid='".$_W['uniacid']."' " );

			



			$insert = array(

				'uniacid' => $_W['uniacid'],

				'openid' => $_W['openid'],

				'money' => $money,

				'credit' => $credit,

				'createtime' => TIMESTAMP,

				'status' => 0,

				'nickname' => $nickname,

			);

			pdo_insert('hc_newbb_tixian_log',$insert);

			$tixian_id = pdo_insertid();



			//修改积分

			$uid = $fans['uid'];

			if(!empty($uid)){

				mc_credit_update($uid,'credit1',-$credit,array(1=>'积分提现'));

			}



			//$callback = $this->give_withdraw_money($tixian_id);

			$callback = $this->get_red_bag($tixian_id);

			if($callback['return_code'] == false){

				pdo_update('hc_newbb_tixian_log',array('false_return'=>$callback['return_msg']),array('id'=>$tixian_id));

				message('提现失败，请稍后再试!');

			}		



			if($callback['return_code'] == 'SUCCESS'){

				if($callback['return_msg'] == '发放成功'){

					pdo_update('hc_newbb_tixian_log',array('status'=>1),array('id'=>$tixian_id));

					message('提现成功，可以查看微信支付公众号或钱包',$this->createMobileUrl('select'),'success');

				}else{

					pdo_update('hc_newbb_tixian_log',array('false_return'=>$callback['return_msg']),array('id'=>$tixian_id));

					message('提现失败，请稍后再试');

				}

			}



		}





		include $this->template('withdraw');

	}



    /*登记手机号码*/
    public function doMobileRegPhone(){
        global $_W,$_GPC;
        $rid = intval($_GPC['id']);
        $fromuser = $_W['openid'];
        $phone =$_POST['phone'];
        $name=$_POST['name'];

        $member = pdo_fetch( " SELECT * FROM ".tablename('hc_newbb_member')." WHERE openid='".$fromuser."' AND weid=".$_W['uniacid']." " );
        $member['mobile']=$phone;
        $member['username']=$name;
        pdo_update('hc_newbb_member',$member,array('openid'=>$member['openid']));
        $this->doMobileLottery();

    }

public function doMobiletest(){
	include $this->template('webbb-reg');
			exit;
}
	

	public function doMobileLottery() {

		global $_GPC, $_W;

		$title = '摇色子抽奖';

		$id = intval($_GPC['id']);

		$rid = intval($_GPC['id']);

		$fromuser = $_W['openid'];

		$setting = $this->module['config'];
		$setting['pv'] = $setting['pv'] + 1;
		$this->saveSettings($setting);

		//检测浏览器

		$this->checkBowser();

		$weibb = pdo_fetch("SELECT * FROM ".tablename('hc_newbb_reply')." WHERE rid = '$id' LIMIT 1");

		$weibb['gettitle'] = '中秋博饼' ;

		if (empty($weibb)) {

			message('非法访问，请重新发送消息进入摇色子页面！');

		}

		

		if(TIMESTAMP <= $weibb['start_time']){

			message('活动尚未开始','','error');

		}if(TIMESTAMP >= $weibb['end_time']){

			message('活动已经结束','','error');

		}



		

		//检测是否关注

		$this->checkFollow($rid);

		//第一次玩，插入member表

		$member = pdo_fetch( " SELECT * FROM ".tablename('hc_newbb_member')." WHERE openid='".$fromuser."' AND weid=".$_W['uniacid']." " );

		if(empty($member)){

			$insert = array(

				'weid' => $_W['uniacid'],

				'openid' => $_W['openid'],

			//	'rid' => $rid,

				'createtime' => TIMESTAMP,

			);

			$info = $this->getMemberInfo();

			$insert['nickname'] = $info['nickname'];

			$insert['headimg'] = $info['headimgurl'];

			$flag = pdo_insert('hc_newbb_member',$insert);

			if($flag == false){

				message('非法访问，请重新发送消息进入游戏');

			}

			$member = pdo_fetch( " SELECT * FROM ".tablename('hc_newbb_member')." WHERE openid='".$fromuser."' AND weid=".$_W['uniacid']." " );

		}

		//同安定制
		//如果没有手机号，则到注册页面
		if(empty($member['mobile']) || empty($member['username'])){

			include $this->template('webbb-reg');
			exit;
		}



		//检测是否时倒计时包厢，倒计时包厢时间有没有到

		if($weibb['baoxiang_type'] == 1){

			$start_cycle = $weibb['start_cycle'] * 3600;

			$end_cycle = $weibb['end_cycle'] *3600;

			$among_time = TIMESTAMP - $weibb['start_time'];

			$remainder_time = $among_time % $start_cycle;

			if($remainder_time >= $end_cycle){

				message("游戏尚未开始，请稍后再试");

				exit;

			}

		}



		//当前剩余次数和朋友加油次数

		$weibb['description']=str_replace("\n","",$weibb['description']);

		$weibb['description']=str_replace("\r","",$weibb['description']);

		$callback = $this->getLotteryNum($id,$fromuser);

		$arr_times['today_has'] = $callback['lotteryNum'];

		$shareTotal = $callback['sharecount'];

		if($callback == "used" ){

			$arr_times['today_has'] = 0;

			$shareTotal = 0;

		}

		if($callback == "error" ){

			message("必须先注册才能进行游戏",$weibb['guanzhuUrl'],'success');

		}

		

		//排行榜

		//$zong = pdo_fetchall(" SELECT rid,baoxiangname, SUM(credit) AS credit FROM ".tablename('hc_newbb_user')."  WHERE weid='".$_W['uniacid']."'  GROUP BY rid ORDER BY credit DESC LIMIT 15  ");

		$now = time();  

		$beginTime = date('Y-m-d 00:00:00', $now);  

		$tStart = strtotime($beginTime);

		$tEnd = strtotime($beginTime) +86400;

		$yEnd = strtotime($beginTime);

		$yStart = strtotime($beginTime) -86400;

		//$today = pdo_fetchall(" SELECT rid, nickname, SUM(credit) AS credit FROM ".tablename('hc_newbb_user')." WHERE createtime>".$tStart." AND createtime<=".$tEnd." AND rid='".$_GPC['id']."' AND weid='".$_W['uniacid']."' GROUP BY mid ORDER BY credit DESC LIMIT 15  ");


		//$yesterday = pdo_fetchall(" SELECT rid, nickname, SUM(credit) AS credit FROM ".tablename('hc_newbb_user')." WHERE createtime>".$yStart." AND createtime<=".$yEnd." AND rid='".$_GPC['id']."' AND weid='".$_W['uniacid']."' GROUP BY mid ORDER BY credit DESC LIMIT 15  ");



		//获得每个结果对应的积分;

			/*

			$yixiu = pdo_fetch(" SELECT * FROM ".tablename('hc_newbb_award')." WHERE `rid`=".$rid." AND `title`='一秀'  ");

			$erju = pdo_fetch(" SELECT * FROM ".tablename('hc_newbb_award')." WHERE `rid`=".$rid." AND `title`='二举'  ");

			$sanhong = pdo_fetch(" SELECT * FROM ".tablename('hc_newbb_award')." WHERE `rid`=".$rid." AND `title`='三红'  ");

			$sijing = pdo_fetch(" SELECT * FROM ".tablename('hc_newbb_award')." WHERE `rid`=".$rid." AND `title`='四进'  ");

			$duitang = pdo_fetch(" SELECT * FROM ".tablename('hc_newbb_award')." WHERE `rid`=".$rid." AND `title`='对堂'  ");

			$putongzhuangyuan = pdo_fetch(" SELECT * FROM ".tablename('hc_newbb_award')." WHERE `rid`=".$rid." AND `title`='普通状元'  ");

			$wuzi = pdo_fetch(" SELECT * FROM ".tablename('hc_newbb_award')." WHERE `rid`=".$rid." AND `title`='五子'  ");

			$wuhong = pdo_fetch(" SELECT * FROM ".tablename('hc_newbb_award')." WHERE `rid`=".$rid." AND `title`='五红'  ");

			$liupu = pdo_fetch(" SELECT * FROM ".tablename('hc_newbb_award')." WHERE `rid`=".$rid." AND `title`='六杯红'  ");

			$chajinghua = pdo_fetch(" SELECT * FROM ".tablename('hc_newbb_award')." WHERE `rid`=".$rid." AND `title`='状元插金花'  ");

			*/

			$award_all = pdo_fetchall( " SELECT * FROM ".tablename('hc_newbb_award')." WHERE `rid`=".$rid." " );

			foreach ($award_all as $key => $value) {

				if($value['title'] == '一秀')			$yixiu = $award_all[$key];

				if($value['title'] == '二举')			$erju = $award_all[$key];

				if($value['title'] == '三红')			$sanhong = $award_all[$key];

				if($value['title'] == '四进')			$sijing = $award_all[$key];

				if($value['title'] == '对堂')			$duitang = $award_all[$key];

				if($value['title'] == '普通状元')		$putongzhuangyuan = $award_all[$key];

				if($value['title'] == '五子')			$wuzi = $award_all[$key];

				if($value['title'] == '五红')			$wuhong = $award_all[$key];

				if($value['title'] == '六杯红')			$liupu = $award_all[$key];

				if($value['title'] == '状元插金花')		$chajinghua = $award_all[$key];

			}



/*        if('onk2ewRaqP3kq56fd9b3dHouToOQ'==$fromuser){
            include $this->template('webbbaudio');
            die();
        }*/



		include $this->template('webbbaudio');

	}

	

	//红包的包厢

	public function doMobilehongbao_lottery() {

		global $_GPC, $_W;

		$title = '摇色子抽奖';

		$id = intval($_GPC['id']);

		$rid = intval($_GPC['id']);

		$fromuser = $_W['openid'];

		

		//检测浏览器

		$this->checkBowser();

		$weibb = pdo_fetch("SELECT * FROM ".tablename('hc_newbb_reply')." WHERE rid = '$id' LIMIT 1");

		$weibb['gettitle'] = '中秋博饼' ;

		if (empty($weibb)) {

			message('非法访问，请重新发送消息进入摇色子页面！');

		}

		

		if(TIMESTAMP <= $weibb['start_time']){

			message('活动尚未开始','','error');

		}if(TIMESTAMP >= $weibb['end_time']){

			message('活动已经结束','','error');

		}



		//检测是否关注

		$this->checkFollow($rid);

		//第一次玩，插入member表

		$member = pdo_fetch( " SELECT * FROM ".tablename('hc_newbb_member')." WHERE openid='".$fromuser."' AND weid=".$_W['uniacid']." " );

		if(empty($member)){

			$insert = array(

				'weid' => $_W['uniacid'],

				'openid' => $_W['openid'],

			//	'rid' => $rid,

				'createtime' => TIMESTAMP,

			);

			$info = $this->getMemberInfo();

			$insert['nickname'] = $info['nickname'];

			$insert['headimg'] = $info['headimgurl'];

			$flag = pdo_insert('hc_newbb_member',$insert);

			if($flag == false){

				message('非法访问，请重新发送消息进入游戏');

			}

			$member = pdo_fetch( " SELECT * FROM ".tablename('hc_newbb_member')." WHERE openid='".$fromuser."' AND weid=".$_W['uniacid']." " );

		}



		//检测是否时倒计时包厢，倒计时包厢时间有没有到

		if($weibb['baoxiang_type'] == 1){

			$start_cycle = $weibb['start_cycle'] * 3600;

			$end_cycle = $weibb['end_cycle'] *3600;

			$among_time = TIMESTAMP - $weibb['start_time'];

			$remainder_time = $among_time % $start_cycle;

			if($remainder_time >= $end_cycle){

				message("游戏尚未开始，请稍后再试");

				exit;

			}

		}



		//当前剩余次数和朋友加油次数

		$weibb['description']=str_replace("\n","",$weibb['description']);

		$weibb['description']=str_replace("\r","",$weibb['description']);

		

		//$callback = $this->getLotteryNum($id,$fromuser);

		$callback = $this->get_hongbao_loggery_num($id,$fromuser);

		$arr_times['today_has'] = $callback['lotteryNum'];

		$shareTotal = $callback['sharecount'];

		if($callback == "used" ){

			$arr_times['today_has'] = 0;

			$shareTotal = 0;

		}

		if($callback == "error" ){

			message("必须先注册才能进行游戏",$weibb['guanzhuUrl'],'success');

		}



		//排行榜

		$now = time();  

		$beginTime = date('Y-m-d 00:00:00', $now);  

		$tStart = strtotime($beginTime);

		$tEnd = strtotime($beginTime) +86400;

		$yEnd = strtotime($beginTime);

		$yStart = strtotime($beginTime) -86400;





		$today_time = $this->get_start_time(TIMESTAMP);

		$yesterday_time = $today_time - 86400;

		$today = pdo_fetchall( " SELECT rid, nickname, SUM(`money`) AS total_money FROM ".tablename('hc_newbb_hongbao_log')." WHERE createday='".$today_time."' AND uniacid='".$_W['uniacid']."' AND rid='".$_GPC['id']."' GROUP BY mid ORDER BY total_money DESC LIMIT 10  " );

		$zong = pdo_fetchall(" SELECT rid, nickname, SUM(`money`) AS total_money FROM ".tablename('hc_newbb_hongbao_log')." WHERE  uniacid='".$_W['uniacid']."' AND rid='".$_GPC['id']."' GROUP BY mid ORDER BY total_money DESC LIMIT 10  " );



		$award_all = pdo_fetchall( " SELECT * FROM ".tablename('hc_newbb_award')." WHERE `rid`=".$rid." " );

			foreach ($award_all as $key => $value) {

				if($value['title'] == '一秀')			$yixiu = $award_all[$key];

				if($value['title'] == '二举')			$erju = $award_all[$key];

				if($value['title'] == '三红')			$sanhong = $award_all[$key];

				if($value['title'] == '四进')			$sijing = $award_all[$key];

				if($value['title'] == '对堂')			$duitang = $award_all[$key];

				if($value['title'] == '普通状元')		$putongzhuangyuan = $award_all[$key];

				if($value['title'] == '五子')			$wuzi = $award_all[$key];

				if($value['title'] == '五红')			$wuhong = $award_all[$key];

				if($value['title'] == '六杯红')			$liupu = $award_all[$key];

				if($value['title'] == '状元插金花')		$chajinghua = $award_all[$key];

			}

		

		

		include $this->template('hongbao_webb');

	}







	//初始化游戏

	public function doMobileinitgame(){

		global $_GPC, $_W;

		$fromuser = $_W['openid'];

		WeUtility::logging("初始化游戏", $fromuser);

		$ret=array(

			"id" => "0",

			"score_each" => "0",

			"roll" => "0",

			"times" => 0,

			'friendtimes'=>0,

			"status" => "200"

		);

		if (empty($fromuser)) {

			

			echo json_encode($ret);

			exit;

		}

		$id = intval($_GPC['id']);

		if (!$id) {

			exit('非法参数2！');

		}

		$webbb = pdo_fetch("SELECT * FROM ".tablename('hc_newbb_reply')." WHERE rid = '$id' LIMIT 1");



		if (empty($webbb)) {

			exit('非法参数2！');

		}

		WeUtility::logging("ret", $ret);

		if($webbb['is_hongbao'] == 1){

			$callback = $this->get_hongbao_loggery_num($id,$fromuser);

		}else{

			$callback = $this->getLotteryNum($id,$fromuser);

		}

				

		$arr_times['today_has'] = $callback['lotteryNum'];

		$shareTotal = $callback['sharecount'];

		if($callback == "used" ){

			$arr_times['today_has'] = 0;

			$shareTotal = 0;

		}

		

		

		echo '{"id":"8884","user_id":"6443","score":"0","times":"'.$arr_times['today_has'].'","time":"20140814","add_flag":"1","friendtimes":"'.$shareTotal.'","status":"200"}';

		exit;

	}

	

	

	//ajax轻松，获得博饼结果

	public function doMobileGetAward() {
$timea = time();

		global $_GPC, $_W;
		$fromuser = $_W['openid'];
		$this->checkBowser();

		

		if (empty($fromuser)) {

			exit('非法参数1！');

		}

		$id = intval($_GPC['id']);

		if (!$id) {

			exit('非法参数2！');

		}

		$weibb = pdo_fetch("SELECT * FROM ".tablename('hc_newbb_reply')." WHERE rid = '$id' LIMIT 1");

		

		if (empty($weibb)) {

			exit('非法参数2！');

		}



		//检查时间

		if($weibb['start_time'] > TIMESTAMP){

			$ret = array(

				'status' => "206",

			);

			echo json_encode($ret);

			exit;

		}

		if($weibb['end_time'] < TIMESTAMP){

			$ret = array(

				'status' => "204",

			);

			echo json_encode($ret);

			exit;

		}



		//检测是否时倒计时包厢，倒计时包厢时间有没有到

		if($weibb['baoxiang_type'] == 1){

			$start_cycle = $weibb['start_cycle'] * 3600;

			$end_cycle = $weibb['end_cycle'] *3600;

			$among_time = TIMESTAMP - $weibb['start_time'];

			$remainder_time = $among_time % $start_cycle;

			if($remainder_time >= $end_cycle){

				$ret = array(

				'status' => "207",

				);

				echo json_encode($ret);

				exit;

			}

		}





		//检查次数

		$user=array();

		$user['name']='ss';

		$user['num']=0;

		$user['usercont']=0;

		$callback = $this->getLotteryNum($id,$fromuser);

		if($callback == "error"){

			echo json_encode(array('user'=>$user,'level'=>1,'errmessage'=>'获取会员id失败'));

			exit;

		}

		if($callback == "used"){

			echo json_encode(array('user'=>$user,'level'=>1,'errmessage'=>'今日次数已用光'));

			exit;

		}

		$user['num']=$callback['lotteryNum'];



		$level=array();

		

		//$awardList = pdo_fetchall(" SELECT * FROM ims_hc_newbb_award WHERE `rid`=".$_GPC['id']." and total>0");

		$awardList = pdo_fetchall(" SELECT * FROM ims_hc_newbb_award WHERE `rid`=".$_GPC['id']." ");

			$result = -1;

			$totalProbaility = 0;

			$totalProbaliltyArray = array();

			foreach($awardList as $k=>$list){

					//获取总概率

				$totalProbaility =$totalProbaility + $list['probalilty'];

				$totalProbaliltyArray[$k]['probalilty'] = $list['probalilty'];

				$totalProbaliltyArray[$k]['title'] = $list['title'];

				$totalProbaliltyArray[$k]['credit'] = $list['credit'];

				$totalProbaliltyArray[$k]['id'] = $list['id'];

			}

			

			//如果总概率小于100，添加一个不中奖的概率使之等于100

			if($totalProbaility <100){

					

				$a = sizeof($totalProbaliltyArray);

				$totalProbaliltyArray[$a+1]['probalilty'] = 100-$totalProbaility;

				$totalProbaliltyArray[$a+1]['title'] = '未中奖';

				$totalProbaliltyArray[$a+1]['credit'] = 0;

				$totalProbaility = 100;

			}

			//概率*10000去除小数点

			$totalProbaility = $totalProbaility*10000;

			

			foreach ($totalProbaliltyArray as $key => $probaility) { 

					//mt_srand((double) microtime()*1000000);

					//在1和10000之间取随机数，如果随机数小于等于当前循环概率数组中的概率，这跳出并中奖，否则总概率减去未中奖概率再次循环

					//比如中奖概率分别为10,20,30,40，要中第二个奖的概率是20/100 按照算法为第一次不中奖，第二次中奖 90/100*20*90 = 20/100

					$randNum = mt_rand(1, $totalProbaility); 

					if ($randNum <= $probaility['probalilty']*10000) { 

						$huode = $probaility['title']; 

						$credit = $probaility['credit']; 

						break; 

					} else { 

						$totalProbaility -= $probaility['probalilty']*10000; 

					} 

			} 

				//  unset ($proArr); 

			if($huode == null){

				$huode =  "未中奖";

			}

		$res = $this->getDiceNum($huode);

		$level['a']=$res['a'];

		$level['b']=$res['b'];

		$level['c']=$res['c'];

		$level['d']=$res['d'];

		$level['e']=$res['e'];

		$level['f']=$res['f'];

		$level['title']='weibb';

		$level['key']= $level['a'] + $level['b'] + $level['c'] + $level['d'] + $level['e'] + $level['f'] ;



		$userdate = array(

			'rid'=>$id,

			'title'=>$huode,

			'credit'=>intval($credit),

			'from_user'=>$fromuser,

			'iszhuangyuan'=>0,

			'createtime'=>TIMESTAMP,

		);

		//这里是插入中奖名单

		$userdate['iszhuangyuan'] = 0;

		if($huode == '一秀') {

			

			$userdate['titleid'] = 1;

		}

		if($huode == '二举') {

			

			$userdate['titleid'] = 2;



		}

		if($huode == '三红') {

			

			$userdate['titleid'] = 3;



		}

		if($huode == '四进') {

				

			$userdate['titleid'] = 4;



		}

		if($huode == '对堂') {

			

			$userdate['titleid'] = 5;



		}if($huode == '普通状元') {

			$userdate['iszhuangyuan'] = 1;

			$userdate['titleid'] = 6;



		}

		if($huode == '五红'){

			$userdate['iszhuangyuan'] = 2;

			$userdate['titleid'] = 8;



		}

		if($huode == '五子'){

			$userdate['iszhuangyuan'] = 3;

			$userdate['titleid'] = 7;



		}

		if($huode == '六杯红'){

				$userdate['iszhuangyuan'] = 4;

				$userdate['titleid'] = 9;



		}

		if($huode == '状元插金花'){

				$userdate['iszhuangyuan'] = 5;

				$userdate['titleid'] = 10;



		}

		if($huode == '未中奖'){

				$userdate['iszhuangyuan'] = -1;

				$userdate['titleid'] = 0;



		}

		$rid = $_GPC['id'];

		$baoxiangname = $weibb['baoxiangname'];

		//$member = pdo_fetch( " SELECT * FROM ".tablename('hc_newbb_member')." WHERE openid='".$_W['openid']."' AND rid='".$rid."' " );

		$member = pdo_fetch( " SELECT * FROM ".tablename('hc_newbb_member')." WHERE openid='".$_W['openid']."' AND weid='".$_W['uniacid']."' " );

		$userdate['baoxiangname'] = $baoxiangname;

		$userdate['mid'] = $member['id'];

		$userdate['nickname'] = $member['nickname'];

		$userdate['headimg'] = $member['headimg'];

		$userdate['weid'] = $_W['uniacid'];

		pdo_insert('hc_newbb_user',$userdate);

		//积分插入每天积分记录表

		$todayStart = strtotime(date('Y-m-d'));

		$todayCredit = pdo_fetch( " SELECT * FROM ".tablename('hc_newbb_dayscredit')."  WHERE openid='".$fromuser."' AND createtime='".$todayStart."'  AND rid='".$rid."' " );

		if(empty($todayCredit)){

			//$mid = pdo_fetchcolumn( " SELECT id FROM ".tablename('hc_newbb_member')." WHERE openid='".$fromuser."' AND rid='".$rid."' " );

			$mid = pdo_fetchcolumn( " SELECT id FROM ".tablename('hc_newbb_member')." WHERE openid='".$fromuser."' AND weid='".$_W['uniacid']."' " );

			$insert = array(

				'weid' => $_W['uniacid'],

				'openid'=> $fromuser,

				'createtime' => $todayStart,

				'headimg' => $member['headimg'],

				'nickname' => $member['nickname'],

				'mid' => $mid,

				'credit' => intval($credit),

				'rid' => $rid

			);

			pdo_insert('hc_newbb_dayscredit',$insert);

			

		}else{

			$todayCredit['credit'] = intval($todayCredit['credit']) + intval($credit);

			pdo_update('hc_newbb_dayscredit',$todayCredit,array('id'=>$todayCredit['id']));

		}

		//积分增加记录

		$uid = $_W['member']['uid'];

		if(!empty($uid)){

			mc_credit_update($uid,'credit1',$credit,array(1=>'博饼 '.$huode));

		}

		if($huode !='未中奖'){
			$credit_log = array(
				'uniacid' => $_W['uniacid'],
				'rid' => $rid,
				'openid' => $fromuser,
				'mid' => $member['id'],
				'award' => $huode,
				'credit' => $credit,
				'createtime' => TIMESTAMP,
			);
			pdo_insert('hc_newbb_credit_log',$credit_log);
			
		}

		//中奖奖品数量减一

		/*

		if($huode != '未中奖'){

			pdo_query(" update  ".tablename('hc_newbb_award')." SET total=total-1  WHERE `rid`=".$rid." AND `title`='".$huode."' AND total>0 ");

		

		}

		*/

		$user['num']=$callback['lotteryNum'] - 1;

		if($user['num'] <=0 ){

			$user['num'] = 0;

		}

		$user['usercont'] = $callback['sharecount'];

		$user['isshare'] = $callback['isshare'];

		if($callback['isshare'] == 2){

			//系统的次数用完即减去朋友送的

				pdo_query("UPDATE  ".tablename('hc_newbb_share')." SET count=count-1 WHERE from_user = '{$fromuser}' AND rid=".$id);

				$user['usercont']=$user['usercont']-1;

				if($user['usercont'] <=0 || $user['usercont']==null ){

					$user['usercont'] = 0;

				}

		}

		

		$ret=array(

			"id" => "0",

			"score_each" => "0",

			"roll" => "0",

			"times" => 0,

			"friendtimes"=>0,

			"status" => "200"

		);

		$ret['score_each']=$huode;

		$ssssss=array($level['a'],$level['b'],$level['c'],$level['d'],$level['e'],$level['f']);

		shuffle($ssssss);

		$ssssss=implode($ssssss);

		$ret['roll']=$ssssss;

		$ret['friendtimes']=$user['usercont'];

		$ret['times']=$user['num'];

	


		//echo json_encode(array('user'=>$user,'level'=>$level,'huode'=>$huode,'status'=>'200','errmessage'=>''));

		echo json_encode($ret);

		exit;

	}

	





	//ajax轻松，获得博饼结果

	public function doMobileget_hongbao_award() {

		global $_GPC, $_W;

		$fromuser = $_W['openid'];

		$this->checkBowser();

		

		if (empty($fromuser)) {

			exit('非法参数1！');

		}

		$id = intval($_GPC['id']);

		$rid = $id;

		if (!$id) {

			exit('非法参数2！');

		}

		$weibb = pdo_fetch("SELECT * FROM ".tablename('hc_newbb_reply')." WHERE rid = '$id' LIMIT 1");

		

		if (empty($weibb)) {

			exit('非法参数2！');

		}



		//检查时间

		if($weibb['start_time'] > TIMESTAMP){

			$ret = array(

				'status' => "206",

			);

			echo json_encode($ret);

			exit;

		}

		if($weibb['end_time'] < TIMESTAMP){

			$ret = array(

				'status' => "204",

			);

			echo json_encode($ret);

			exit;

		}



		//检测是否时倒计时包厢，倒计时包厢时间有没有到

		if($weibb['baoxiang_type'] == 1){

			$start_cycle = $weibb['start_cycle'] * 3600;

			$end_cycle = $weibb['end_cycle'] *3600;

			$among_time = TIMESTAMP - $weibb['start_time'];

			$remainder_time = $among_time % $start_cycle;

			if($remainder_time >= $end_cycle){

				$ret = array(

				'status' => "207",

				);

				echo json_encode($ret);

				exit;

			}

		}



		$member = pdo_fetch( " SELECT * FROM ".tablename('hc_newbb_member')." WHERE openid='".$_W['openid']."' AND weid='".$_W['uniacid']."' " );



		//检查次数

		$user=array();

		$user['name']='ss';

		$user['num']=0;

		$user['usercont']=0;

		//$callback = $this->getLotteryNum($id,$fromuser);

		$callback = $this->get_hongbao_loggery_num($id,$fromuser);

		if($callback == "error"){

			echo json_encode(array('user'=>$user,'level'=>1,'errmessage'=>'获取会员id失败'));

			exit;

		}

		if($callback == "used"){

			echo json_encode(array('user'=>$user,'level'=>1,'errmessage'=>'今日次数已用光'));

			exit;

		}

		$user['num']=$callback['lotteryNum'];



		$level=array();

		

		//$awardList = pdo_fetchall(" SELECT * FROM ims_hc_newbb_award WHERE `rid`=".$_GPC['id']." and total>0");

		$awardList = pdo_fetchall(" SELECT * FROM ims_hc_newbb_award WHERE `rid`=".$_GPC['id']." ");

			$result = -1;

			$totalProbaility = 0;

			$totalProbaliltyArray = array();

			foreach($awardList as $k=>$list){

					//获取总概率

				$totalProbaility =$totalProbaility + $list['probalilty'];

				$totalProbaliltyArray[$k]['probalilty'] = $list['probalilty'];

				$totalProbaliltyArray[$k]['title'] = $list['title'];

				$totalProbaliltyArray[$k]['credit'] = $list['credit'];

				$totalProbaliltyArray[$k]['id'] = $list['id'];

				$totalProbaliltyArray[$k]['money'] = $list['money'];

			}

			

			//如果总概率小于100，添加一个不中奖的概率使之等于100

			if($totalProbaility <100){

					

				$a = sizeof($totalProbaliltyArray);

				$totalProbaliltyArray[$a+1]['probalilty'] = 100-$totalProbaility;

				$totalProbaliltyArray[$a+1]['title'] = '未中奖';

				$totalProbaliltyArray[$a+1]['credit'] = 0;

				$totalProbaility = 100;

			}

			//概率*10000去除小数点

			$totalProbaility = $totalProbaility*10000;

			

			foreach ($totalProbaliltyArray as $key => $probaility) { 

					//mt_srand((double) microtime()*1000000);

					//在1和10000之间取随机数，如果随机数小于等于当前循环概率数组中的概率，这跳出并中奖，否则总概率减去未中奖概率再次循环

					//比如中奖概率分别为10,20,30,40，要中第二个奖的概率是20/100 按照算法为第一次不中奖，第二次中奖 90/100*20*90 = 20/100

					$randNum = mt_rand(1, $totalProbaility); 

					if ($randNum <= $probaility['probalilty']*10000) { 

						$huode = $probaility['title']; 

						$credit = $probaility['credit']; 

						$money = $probaility['money']; 

						break; 

					} else { 

						$totalProbaility -= $probaility['probalilty']*10000; 

					} 

			} 

				//  unset ($proArr); 

			if($huode == null){

				$huode =  "未中奖";

			}

		$res = $this->getDiceNum($huode);

		$level['a']=$res['a'];

		$level['b']=$res['b'];

		$level['c']=$res['c'];

		$level['d']=$res['d'];

		$level['e']=$res['e'];

		$level['f']=$res['f'];

		$level['title']='weibb';

		$level['key']= $level['a'] + $level['b'] + $level['c'] + $level['d'] + $level['e'] + $level['f'] ;

		

		$createday = $this->get_start_time(TIMESTAMP);

		$userdate = array(

			'rid'=>$id,

			'credit'=>intval($credit),

			'uniacid' => $_W['uniacid'],

			'openid'=>$fromuser,

			'money'=>$money,

			'createtime'=>TIMESTAMP,

			'nickname'=>$member['nickname'],

			'mid'=>$member['id'],

			'createday' => $createday,

		);



		$baoxiangname = $weibb['baoxiangname'];

		pdo_insert('hc_newbb_hongbao_log',$userdate);

		//插入到总金额表

		$total_money = pdo_fetch( " SELECT * FROM ".tablename('hc_newbb_hongbao_total')." WHERE mid='".$member['id']."' AND uniacid='".$_W['uniacid']."' " );

		if(empty($total_money)){

			$insert_money = array(

				'uniacid' => $_W['uniacid'],

				'openid' => $_W['openid'],

				'createtime' => TIMESTAMP,

				'money' => $money,

				'nickname' => $member['nickname'],

				'mid' => $member['id'],

			);

			pdo_insert("hc_newbb_hongbao_total",$insert_money);

		}else{

			$total_money['money'] = $money + $total_money['money'];

			pdo_update('hc_newbb_hongbao_total',$total_money,array('id'=>$total_money['id']));

		}



		$user['num']=$callback['lotteryNum'] - 1;

		if($user['num'] <=0 ){

			$user['num'] = 0;

		}

		$user['usercont'] = $callback['sharecount'];

		$user['isshare'] = $callback['isshare'];

		if($callback['isshare'] == 2){

			//系统的次数用完即减去朋友送的

				pdo_query("UPDATE  ".tablename('hc_newbb_share')." SET count=count-1 WHERE from_user = '{$fromuser}' AND rid=".$id);

				$user['usercont']=$user['usercont']-1;

				if($user['usercont'] <=0 || $user['usercont']==null ){

					$user['usercont'] = 0;

				}

		}

		

		$ret=array(

			"id" => "0",

			"score_each" => "0",

			"roll" => "0",

			"times" => 0,

			"friendtimes"=>0,

			"status" => "200"

		);

		$ret['score_each']=$huode;

		$ssssss=array($level['a'],$level['b'],$level['c'],$level['d'],$level['e'],$level['f']);

		shuffle($ssssss);

		$ssssss=implode($ssssss);

		$ret['roll']=$ssssss;

		$ret['friendtimes']=$user['usercont'];

		$ret['times']=$user['num'];

		

		//echo json_encode(array('user'=>$user,'level'=>$level,'huode'=>$huode,'status'=>'200','errmessage'=>''));

		echo json_encode($ret);

		exit;

	}

	











	//查看我的积分

	public function doMobilemyCredit(){

		global $_W,$_GPC;

		$rid = intval($_GPC['id']);

		$this->checkBowser();

		$mycredit = pdo_fetchall(" SELECT * FROM ".tablename('hc_newbb_dayscredit')." WHERE openid='".$_W['openid']."' AND rid='".$rid."' ");

		include $this->template('myaward');

	}

	//积分排行

	public function doMobilecreditRank(){

		global $_W,$_GPC;

		$rid = intval($_GPC['id']);

		$this->checkBowser();

		$mycredit = pdo_fetchall(" SELECT rid, from_user, nickname,headimg, SUM(credit) AS credit FROM ".tablename('hc_newbb_user')." WHERE  weid='".$_W['uniacid']."' GROUP BY from_user ORDER BY credit DESC LIMIT 100  ");

	

		$credit_rank_img = $this->module['config']['credit_rank_img'];



		$zong_credit = pdo_fetchcolumn( " SELECT SUM(credit) FROM ".tablename('hc_newbb_user')." WHERE weid='".$_W['uniacid']."' AND from_user = '".$_W['openid']."' " );

		

		foreach ($mycredit as $key => $value) {

			if($value['from_user'] == $_W['openid']) $rank = $key + 1;

		}



		//include $this->template('creditRank');

		include $this->template('zong_rank');

	}



	public function doMobileweekrank(){

		global $_W,$_GPC;

		$week = $this->get_week_time(TIMESTAMP);

		$start_time = strtotime($week['start_time']);

		$end_time =  strtotime($week['end_time']);

		$mycredit = pdo_fetchall(" SELECT rid,from_user, nickname,headimg, SUM(credit) AS credit FROM ".tablename('hc_newbb_user')." WHERE  weid='".$_W['uniacid']."' AND createtime > '".$start_time."' AND createtime <= '".$end_time."' GROUP BY from_user ORDER BY credit DESC LIMIT 100  ");

		

		foreach ($mycredit as $key => $value) {

			if($value['from_user'] == $_W['openid']) $rank = $key + 1;

		}



		$zong_credit = pdo_fetchcolumn( " SELECT SUM(credit) FROM ".tablename('hc_newbb_user')." WHERE weid='".$_W['uniacid']."' AND from_user = '".$_W['openid']."' " );





		include $this->template('zong_rank');

	}

	

	//日积分排行

	public function doMobiledayrank(){

		global $_W,$_GPC;

		$rid = intval($_GPC['id']);
		$day_time = $_GPC['day_time'];

		$this->checkBowser();

		//开始时间和结束时间

		$start_time = strtotime(date('Y-m-d'));
		if(!empty($day_time)){
			$start_time = strtotime($day_time);
		}

		$keyword = date('Y-m-d');

		$end_time = $start_time + 3600*24; 

		$mycredit = pdo_fetchall(" SELECT rid,from_user, nickname,headimg, SUM(credit) AS credit FROM ".tablename('hc_newbb_user')." WHERE  weid='".$_W['uniacid']."' AND createtime>='".$start_time."' AND createtime<'".$end_time."' GROUP BY from_user ORDER BY credit DESC LIMIT 100  ");

		$day_rank_img = $this->module['config']['day_rank_img'];



		$zong_credit = pdo_fetchcolumn( " SELECT SUM(credit) FROM ".tablename('hc_newbb_user')." WHERE weid='".$_W['uniacid']."' AND from_user = '".$_W['openid']."' " );



		foreach ($mycredit as $key => $value) {

			if($value['from_user'] == $_W['openid']) $rank = $key + 1;

		}



		//include $this->template('dayrank');

		include $this->template('zong_rank');

	}

	

//我的积分记录
	public function doMobilemy_credit_log(){
		global $_W,$_GPC;
		$rid = intval($_GPC['rid']);
		$this->checkFollow($rid);
		

		$start_time = $_GPC['day_time'];
		if(empty($start_time)){
			$start_time = TIMESTAMP;
		}else{
			$start_time = strtotime($start_time);
		}
		$start_time = $this->get_start_time($start_time);
		$end_time = $start_time + 86400;

		$zong_credit = pdo_fetchcolumn( " SELECT SUM(credit) FROM ".tablename('hc_newbb_user')." WHERE weid='".$_W['uniacid']."' AND from_user = '".$_W['openid']."' " );

		$credit_log = pdo_fetchall( " SELECT * FROM ".tablename('hc_newbb_credit_log')." WHERE openid='".$_W['openid']."' AND uniacid='".$_W['uniacid']."' AND createtime >='".$start_time."' AND createtime<'".$end_time."' ORDER BY `createtime` DESC " );

		include $this->template('my_credit_log');

	}
	public function doMobileSearch_rank(){

		global $_W,$_GPC;

		$rid = intval($_GPC['id']);

		$this->checkBowser();

		$keyword = $_GPC['keyword'];

		if(empty($keyword)){

			message("请输入查询时间");

		}

		//开始时间和结束时间

		$start_time = strtotime($keyword);

		$end_time = $start_time + 3600*24; 

		$mycredit = pdo_fetchall(" SELECT rid, nickname, SUM(credit) AS credit FROM ".tablename('hc_newbb_user')." WHERE  weid='".$_W['uniacid']."' AND createtime>='".$start_time."' AND createtime<'".$end_time."' GROUP BY from_user ORDER BY credit DESC   ");



	



		include $this->template('dayrank');



	}

		//兑换记录

	public function doMobilemygoods(){

		global $_W,$_GPC;

		$this->checkBowser();

		$rid = intval($_GPC['id']);

		$openid = $_W['openid'];

		if(empty($openid)){

			message('无法获取openid，请发送关键字重试');

		}

		$uid = pdo_fetchcolumn( " SELECT uid FROM ".tablename('mc_mapping_fans')." WHERE openid='".$openid."' AND uniacid='".$_W['uniacid']."' " );

		if(empty($uid)){

			message('无法获取uid，请发送关键字重试');

		}

		$fans = pdo_fetch( " SELECT * FROM ".tablename('mc_members')." WHERE uid='".$uid."' " );

		$credit = intval($fans['credit1']);

		$webbb_award_list = pdo_fetchall( " SELECT o.createtime, g.* FROM ".tablename('hc_newbb_exchange_order')." AS o LEFT JOIN ".tablename('hc_newbb_goods')." AS g ON o.goodsid=g.id WHERE o.openid='".$openid."' AND o.weid=".$_W['uniacid']." ORDER BY o.createtime DESC  " );

		

		include $this->template('mygoods');

	}

	

	//奖品兑换页面

	public function doMobileexchange(){

		global $_W,$_GPC;

		$rid = intval($_GPC['id']);

		$this->checkBowser();

		//$this->checkFollow($rid);

		$openid = $_W['openid'];

		if(empty($openid)){

			message('无法获取openid，请发送关键字重试');

		}

		$uid = pdo_fetchcolumn( " SELECT uid FROM ".tablename('mc_mapping_fans')." WHERE openid='".$openid."' AND uniacid='".$_W['uniacid']."' " );

		if(empty($uid)){

			message('无法获取uid，请发送关键字重试');

		}

		$fans = pdo_fetch( " SELECT * FROM ".tablename('mc_members')." WHERE uid='".$uid."' " );

		$credit = intval($fans['credit1']);

		$goods = pdo_fetchall( " SELECT * FROM ".tablename('hc_newbb_goods')." WHERE weid='".$_W['uniacid']."' ORDER BY `order` DESC " );

		

		include $this->template('exchange_index');

	}

	//兑换商品

	public function doMobileexchangegoods(){

		global $_W,$_GPC;

		$this->checkBowser();

		$openid = $_W['openid'];

		if(empty($openid)){

			message("无法获取openid，请发送关键字重试");

		}

		$goodsid = intval($_GPC['goodsid']);

		if(empty($goodsid)){

			message("商品信息丢失，请返回重试");

		}

		$goods = pdo_fetch( " SELECT * FROM ".tablename('hc_newbb_goods')." WHERE id=".$goodsid." " );

		$now = TIMESTAMP;

		if($now < $goods['start_time']){

			message("商品兑换时间未到，请耐心等待");

		}

		if($now > $goods['end_time']){

			message("商品兑换时间已结束，请下次再来");

		}

		

		

		include $this->template('exchange_goods');

	}

	

	//获取商品

	public function doMobilegetgoods(){

		global $_W,$_GPC;

		$this->checkBowser();

		$openid = $_W['openid'];

		if(empty($openid)){

			message("无法获取openid，请发送关键字重试");

		}

		$uid = pdo_fetchcolumn( " SELECT uid FROM ".tablename('mc_mapping_fans')." WHERE openid='".$openid."' AND uniacid='".$_W['uniacid']."' " );

		$mid = pdo_fetchcolumn( " SELECT id FROM ".tablename('hc_newbb_member')." WHERE openid='".$openid."' AND weid='".$_W['uniacid']."' " );

		if(empty($uid)){

			message('无法获取uid，请发送关键字重试');

		}

		if(empty($uid)){

			message('无法获取mid，请发送关键字重试');

		}

		$fans = pdo_fetch( " SELECT * FROM ".tablename('mc_members')." WHERE uid='".$uid."' " );

			$goodsid = intval($_GPC['goodsid']);

			$realname = $_GPC['realname'];

			$mobile = $_GPC['mobile'];

			$address = $_GPC['address'];

			$num = intval($_GPC['num']);

			if($num <= 0){			

				message("兑换数量错误");

			}

			if(empty($goodsid) || empty($realname) || empty($mobile) || empty($address) || empty($num) ){

				message("请完善相关信息");

			}

			$goods = pdo_fetch( " SELECT * FROM ".tablename('hc_newbb_goods')." WHERE id=".$goodsid." " );

			$now = TIMESTAMP;

			if($now < $goods['start_time']){

				message("商品兑换时间未到，请耐心等待");

			}

			if($now > $goods['end_time']){

				message("商品兑换时间已结束，请下次再来");

			}

			if($num > $goods['num']){

				message("商品剩余数量不足，请兑换其他商品");

			}

			$total = pdo_fetchcolumn(" SELECT SUM(num) FROM ".tablename('hc_newbb_exchange_order')." WHERE openid='".$openid."' AND goodsid='".$goods['id']."' ");			

			$total = intval($total);

			$total = $total + $num;

			if($total > $goods['limit']){

				message("已超过商品最大购买数量，无法继续购买该产品");

			}

			$goodsjifen = intval($goods['jifen']) * $num;

			$credit = intval($fans['credit1']);

			if($credit < $goodsjifen){

				message("您的积分不足，无法兑换改商品");

			}else{

				$insert = array(

					'weid' => $_W['weid'],

					'openid' => $_W['openid'],

					'createtime' => TIMESTAMP,

					'mid' => $mid,

					'realname' => $realname,

					'mobile' => $mobile,

					'address' => $address,

					'num' => $num,

					'goodsid' => $goods['id'],

					'status' => 3,

					

				);

				$temp = pdo_insert('hc_newbb_exchange_order',$insert);

				if($temp == false){

					message('保存失败，请发送关键字重试');

				}

				$goods['num'] = $goods['num'] - $num;

				pdo_update('hc_newbb_goods',$goods,array('id'=>$goods['id']));

				$fans['credit1'] = $credit - $goodsjifen;

				pdo_update('mc_members',$fans,array('uid'=>$fans['uid']));

				message('商品兑换成功，请耐心等待',$this->createMobileUrl('exchange'),'success');

			}

			

		

		

	}

	

	

	//函数部分

	

	

	//检测用户是否已关注公众号

	public function checkFollow($rid){

		global $_W,$_GPC;

		$openid = $_W['openid'];

		$weibb = pdo_fetch( " SELECT * FROM ".tablename('hc_newbb_reply')." WHERE rid='".$rid."' " );


		//检测服务号是否开启免关注

		$setting = $this->module['config'];

		if($setting['is_guanzhu'] == 1){

			if($_W['account']['level'] == 4){

				if(!empty($_W['openid'])){

					$info = $this->getMemberInfo();
					if($info['subscribe'] == 0){
						$is_subscribe == false;
					}
				}

				//如果openid为空或者没有关注，就是用高级接口获取信息
				if(empty($_W['openid']) || $is_subscribe == false){


					$cookie_name = $this->get_cookie_name();

					if(!isset($_COOKIE[$cookie_name])){

						//高级接口获取信息

						$appid = $_W['account']['key'];

						$secret = $_W['account']['secret'];

						//是否为高级号

						$serverapp = $_W['account']['level'];	

						

						//返回链接

						$fanhuiurl = $_W['siteroot']."app/".$this->createMobileUrl('select',array(),true);

						//借用的

						$url = $_W['siteroot']."app/".$this->createMobileUrl('Oauth2', array('fanhuiurl'=>$fanhuiurl),true);

						//返回链接



						$oauth2_code = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".$appid."&redirect_uri=".urlencode($url)."&response_type=code&scope=snsapi_userinfo&state=0#wechat_redirect";

						

						//exit($oauth2_code);

						header("location:$oauth2_code");



					}

					//从cookie中获得openid

					$openid = $_COOKIE[$cookie_name];

					$_W['openid'] = $openid;



				}
				

					return;



			}

		}

		

		if(empty($openid)){

			$checkfollow = 4;

			$url = $weibb['guanzhuUrl'];

			header("location:$url");exit;

			$url = $this->createMobileUrl('header_url',array('rid'=>$rid));

			header($url);exit;

			$content = "请到公众号发送关键字进行游戏";

			include $this->template('message');

			exit;

			

		}



		$fans = pdo_fetch( " SELECT * FROM ".tablename('mc_mapping_fans')." WHERE openid='".$openid."' AND uniacid=".$_W['uniacid']." " );



			





		if(empty($fans) || $fans['follow'] == 0){

			$checkfollow = 4;

			$url = $weibb['guanzhuUrl'];

			header("location:$url");exit;

			$url = $this->createMobileUrl('header_url',array('rid'=>$rid));

			$content = "请到公众号发送关键字进行游戏";

			include $this->template('message');

			exit;

		}

		

		//为已关注没有uid的用户添加uid



		  $openid = $_W['openid'];

          $uid = $_W['member']['uid'];

          if(!empty($openid) && empty($uid)){

               $default_groupid = pdo_fetchcolumn('SELECT groupid FROM ' .tablename('mc_groups') . ' WHERE uniacid = :uniacid AND isdefault = 1', array(':uniacid' => $_W['uniacid']));

                    $row = array(

                         'uniacid' => $_W['uniacid'],

                         'nickname'=>$info['nickname'],

                         'avatar'=>$info['headimgurl'],

                         'realname'=>$info['nickname'],

                         'groupid' => $default_groupid,

                         'email'=>random(32).'@we7.cc',

                         'salt'=>random(8),

                         'createtime'=>time()

                    );

                    pdo_insert('mc_members', $row);

                    $user['uid'] = pdo_insertid();

                    $fan = mc_fansinfo($_W['openid']);

                    //pdo_update('mc_mapping_fans', array('uid'=>$uid), array('openid'=>$_W['openid'], 'uniacid'=>$_W['uniacid']));

                    pdo_update('mc_mapping_fans', array('uid'=>$user['uid']), array('fanid'=>$fan['fanid']));

                    _mc_login($user);

          }

	

	}



	public function doMobileheader_url(){

		global $_W,$_GPC;

		$rid = intval($_GPC['rid']);

		$weibb = pdo_fetch( " SELECT * FROM ".tablename('hc_newbb_reply')." WHERE rid='".$rid."' " );

		$url = $weibb['guanzhuUrl'];

		header("location:$url");

		exit;

	}



//获得cookie_name

	public function get_cookie_name(){

		global $_W;

		$cookie_name = $_W['account']['account']."huochi_bobing4";

		return $cookie_name;		

	}



	//检测用户浏览器

	public function checkBowser(){

		$useragent = addslashes($_SERVER['HTTP_USER_AGENT']);

		if(strpos($useragent, 'MicroMessenger') === false && strpos($useragent, 'Windows Phone') === false ){

		//	echo " 404";

		//	exit;

		}

	}

	

	//获取用户当前剩余次数和加油次数

	public function getLotteryNum($rid,$openid){

		global $_W,$_GPC;

		$mid = pdo_fetchcolumn( " SELECT id FROM ".tablename('hc_newbb_member')." WHERE openid='".$openid."' AND weid='".$_W['uniacid']."' " );

		if(empty($mid)){

			$callback = "error";

			return $callback;

		}else{

			//$daystotal 系统每天赠送次数,$sharecount 朋友赠送次数, $usedtotal 用户今日已使用次数

			//用户剩余次数 = 系统每天赠送次数 + 朋友赠送次数 - 用户今日已使用次数

			$daystotal = pdo_fetchcolumn( " SELECT maxlottery FROM ".tablename('hc_newbb_reply')." WHERE rid=".$rid." " );

			$daystotal = intval($daystotal);

			$sharecount = pdo_fetchcolumn( " SELECT count FROM ".tablename('hc_newbb_share')." WHERE mid='".$mid."' AND rid='".$rid."' " );

			$sharecount = intval($sharecount);

			$sql="SELECT COUNT(*) FROM ".tablename('hc_newbb_user')." WHERE createtime > '".strtotime(date('Y-m-d'))."' AND from_user = '$openid' and rid=".$rid;

			$usedtotal = intval(pdo_fetchcolumn($sql));

				//当用户每天赠送次数用完后，剩余次数=加油次数

				if($usedtotal >= $daystotal){

				$callback['isshare'] = 2;//=2说明赠送次数已用完，使用的是加油次数

				//如果分享次数小于等于0则摇奖次数用完

				if($sharecount <= 0 ){

						$callback = "used";

						return $callback;

					}

					else{

					$callback['lotteryNum'] = $sharecount;

					$callback['sharecount'] = $sharecount;

					return $callback;

				}

			}else{

				//用户每天赠送次数用完前，剩余次数=赠送次数-已使用次数+加油次数

				$lotteryNum = $daystotal + $sharecount - $usedtotal;

				//$lotteryNum = $daystotal  - $usedtotal;

				$callback['lotteryNum'] = $lotteryNum;

				$callback['sharecount'] = $sharecount;

				return $callback;

			}

			

		}

		

	}



	//获取红包包厢的用户当前剩余次数和加油次数

	public function get_hongbao_loggery_num($rid,$openid){

		global $_W,$_GPC;

		$mid = pdo_fetchcolumn( " SELECT id FROM ".tablename('hc_newbb_member')." WHERE openid='".$openid."' AND weid='".$_W['uniacid']."' " );

		if(empty($mid)){

			$callback = "error";

			return $callback;

		}else{

			//$daystotal 系统每天赠送次数,$sharecount 朋友赠送次数, $usedtotal 用户今日已使用次数

			//用户剩余次数 = 系统每天赠送次数 + 朋友赠送次数 - 用户今日已使用次数

			$daystotal = pdo_fetchcolumn( " SELECT maxlottery FROM ".tablename('hc_newbb_reply')." WHERE rid=".$rid." " );

			$daystotal = intval($daystotal);

			$sharecount = pdo_fetchcolumn( " SELECT count FROM ".tablename('hc_newbb_share')." WHERE mid='".$mid."' AND rid='".$rid."' " );

			$sharecount = intval($sharecount);

			$sql="SELECT COUNT(*) FROM ".tablename('hc_newbb_hongbao_log')." WHERE createtime > '".strtotime(date('Y-m-d'))."' AND openid = '$openid' and rid=".$rid;

			$usedtotal = intval(pdo_fetchcolumn($sql));

				//当用户每天赠送次数用完后，剩余次数=加油次数

				if($usedtotal >= $daystotal){

				$callback['isshare'] = 2;//=2说明赠送次数已用完，使用的是加油次数

				//如果分享次数小于等于0则摇奖次数用完

				if($sharecount <= 0 ){

						$callback = "used";

						return $callback;

					}

					else{

					$callback['lotteryNum'] = $sharecount;

					$callback['sharecount'] = $sharecount;

					return $callback;

				}

			}else{

				//用户每天赠送次数用完前，剩余次数=赠送次数-已使用次数+加油次数

				$lotteryNum = $daystotal + $sharecount - $usedtotal;

				//$lotteryNum = $daystotal  - $usedtotal;

				$callback['lotteryNum'] = $lotteryNum;

				$callback['sharecount'] = $sharecount;

				return $callback;

			}

			

		}

		

	}

	

	//为转发者增加加油次数

	public function addLotteryNum($shareid,$weibb,$rid){

		global $_W;

		//检测member表中是否存在这条数据

		$member = pdo_fetch( " SELECT id,openid FROM ".tablename('hc_newbb_member')." WHERE id='".$shareid."' " );

		$mid = $member['id'];

		$openid = $member['openid'];

		$mid = intval($mid);

		$share = pdo_fetch( " SELECT * FROM ".tablename('hc_newbb_share')." WHERE mid='".$mid."' AND rid=".$rid." " );

				//如果不存在则插入

				if(empty($share)){

					$insert = array(

						'count' => 0,

						'sharetotal' => 0,

						'from_user' => $openid,

						'createtime' => TIMESTAMP,

						'rid' => $rid,

						'mid' => $mid,

					);

					pdo_insert('hc_newbb_share',$insert);

					$share = pdo_fetch( " SELECT * FROM ".tablename('hc_newbb_share')." WHERE mid='".$mid."' AND rid=".$rid." " );

				}

		if(!empty($mid)){

			//用cookie作为判断依据，cookie不存在说明为首次被转发者，为被转发者增加奖励次数

			$cookies = $_W['uniacid']."hc_".$rid."_baoxiangbb130".$mid;

			if(!isset($_COOKIE[$cookies])){



				$shareCount = intval($share['count']);					//当前剩余奖励次数

				$prace_times = intval($weibb['prace_times']);			//系统规定最大奖励次数

				$weibbSharecount = intval($weibb['sharecount']);		//系统规定转发一次奖励次数

				$shareTotal = intval($share['sharetotal']);				//用户总共获得的奖励次数

				if($shareTotal <= $prace_times){

					/*

					if($shareTotal + $weibbSharecount <= $prace_times){

						$shareTotal = $shareTotal + 1;

						$shareCount = $shareCount + $weibbSharecount;

					}else{

						$shareTotal = $prace_times;

						$lastcount = $prace_times - $shareTotal;

						$shareCount = $shareCount + $lastcount;

					}

					*/

						$shareTotal = $shareTotal + 1;

						$shareCount = $shareCount + $weibbSharecount;



					



					$share['count'] = $shareCount;

					$share['sharetotal'] = $shareTotal;

					setcookie($cookies,1,time()+86400);

					pdo_update('hc_newbb_share',$share,array('id'=>$share['id']));

				}

			}

		}

		return null;

	}

	

	//根据获奖结果随机显示骰子点数

	public function getDiceNum($huode){

		//根据摇出的奖项显示骰子

			if($huode == '未中奖'){

				$rand = mt_rand(1,3);

				if($rand == 1){

					$level['a']=5;

					$level['b']=1;

					$level['c']=2;

					$level['d']=1;

					$level['e']=6;

					$level['f']=2;

					

				}

				if($rand == 2){

					$level['a']=3;

					$level['b']=2;

					$level['c']=5;

					$level['d']=1;

					$level['e']=5;

					$level['f']=3;

				

				}

				if($rand == 3){

					$level['a']=3;

					$level['b']=2;

					$level['c']=5;

					$level['d']=6;

					$level['e']=6;

					$level['f']=3;

				

				}

			

			}

			//有一个4

			if($huode == '一秀'){

				$rand = mt_rand(1,3);

				if($rand == 1)

				{

					$level['a']=3;

					$level['b']=4;

					$level['c']=5;

					$level['d']=1;

					$level['e']=5;

					$level['f']=6;

					

				}

				if($rand == 2){

					$level['a']=6;

					$level['b']=4;

					$level['c']=5;

					$level['d']=1;

					$level['e']=2;

					$level['f']=6;

					

				}

				if($rand == 3){

					$level['a']=3;

					$level['b']=4;

					$level['c']=5;

					$level['d']=3;

					$level['e']=1;

					$level['f']=6;

					

				}

			

			}

			//有两个4

			if($huode == '二举'){

				$rand = mt_rand(1,3);

				if($rand == 1){

					$level['a']=4;

					$level['b']=4;

					$level['c']=2;

					$level['d']=2;

					$level['e']=5;

					$level['f']=6;

					

				}if($rand == 2){

					$level['a']=4;

					$level['b']=2;

					$level['c']=2;

					$level['d']=4;

					$level['e']=1;

					$level['f']=6;

					

				}if($rand == 3){

					$level['a']=4;

					$level['b']=1;

					$level['c']=2;

					$level['d']=2;

					$level['e']=3;

					$level['f']=4;

					

				}

			

			

			

			}

			//有三个4

			if($huode == '三红'){

				$rand = mt_rand(1,3);

				if($rand == 1){

					$level['a']=4;

					$level['b']=4;

					$level['c']=4;

					$level['d']=3;

					$level['e']=5;

					$level['f']=1;

					

				}if($rand == 2){

					$level['a']=4;

					$level['b']=4;

					$level['c']=3;

					$level['d']=4;

					$level['e']=6;

					$level['f']=1;

					

				}if($rand == 3){

					$level['a']=4;

					$level['b']=4;

					$level['c']=3;

					$level['d']=3;

					$level['e']=2;

					$level['f']=4;

					

				}

			

		

			}

			//除四个4外，有四个点数相同

			if($huode == '四进'){

				$rand = mt_rand(1,3);

				if($rand == 1){

					$level['a']=2;

					$level['b']=2;

					$level['c']=2;

					$level['d']=2;

					$level['e']=5;

					$level['f']=6;

					

				}if($rand == 2){

					$level['a']=1;

					$level['b']=1;

					$level['c']=1;

					$level['d']=1;

					$level['e']=2;

					$level['f']=6;

					

				}if($rand == 3){

					$level['a']=5;

					$level['b']=5;

					$level['c']=5;

					$level['d']=5;

					$level['e']=3;

					$level['f']=6;

					

				}

			

			}

			//六个骰子为123456

			if($huode == '对堂'){

				$level['a']=1;

				$level['b']=2;

				$level['c']=3;

				$level['d']=4;

				$level['e']=5;

				$level['f']=6;

				

			}

			//六个骰子有四个4，另外两个除4和同时为1外

			if($huode == '普通状元'){

				$rand = mt_rand(1,3);

				if($rand == 1){

					$level['a']=4;

					$level['b']=4;

					$level['c']=4;

					$level['d']=4;

					$level['e']=3;

					$level['f']=5;

					

				}

				if($rand == 2){

					$level['a']=4;

					$level['b']=4;

					$level['c']=4;

					$level['d']=2;

					$level['e']=4;

					$level['f']=6;

					

				}

				if($rand == 3){

					$level['a']=4;

					$level['b']=4;

					$level['c']=3;

					$level['d']=2;

					$level['e']=4;

					$level['f']=4;

					

				}

			}



			//六个骰子有5个相同(除4点外)

			if($huode == '五子'){

				$rand = mt_rand(1,3);

				if($rand == 1){

					$level['a']=1;

					$level['b']=1;

					$level['c']=1;

					$level['d']=1;

					$level['e']=1;

					$level['f']=2;

					

				}if($rand == 2){

					$level['a']=5;

					$level['b']=5;

					$level['c']=5;

					$level['d']=5;

					$level['e']=5;

					$level['f']=6;

					

				}if($rand == 3){

					$level['a']=3;

					$level['b']=3;

					$level['c']=3;

					$level['d']=3;

					$level['e']=3;

					$level['f']=5;

					

				}

			}

			//六个骰子有5粒为4

			if($huode == '五红'){	

				$rand = mt_rand(1,3);

				if($rand == 1){

					$level['a']=4;

					$level['b']=4;

					$level['c']=4;

					$level['d']=4;

					$level['e']=4;

					$level['f']=5;

					

				}	

				if($rand == 2){

					$level['a']=4;

					$level['b']=4;

					$level['c']=4;

					$level['d']=4;

					$level['e']=4;

					$level['f']=2;

					

				}	

				if($rand == 3){

					$level['a']=4;

					$level['b']=4;

					$level['c']=4;

					$level['d']=4;

					$level['e']=4;

					$level['f']=3;

					

				}

			

			

			

			

			}

			//六个骰子都是1

			if($huode == '六杯红'){

				$level['a']=1;

				$level['b']=1;

				$level['c']=1;

				$level['d']=1;

				$level['e']=1;

				$level['f']=1;

				

			}

		

			//六个骰子四个为4两个为1

			if($huode == '状元插金花'){

				$level['a']=4;

				$level['b']=4;

				$level['c']=4;

				$level['d']=4;

				$level['e']=1;

				$level['f']=1;

				

			}	

		return $level;

	}

	

	//获取已关注公众号人员信息

	public function getMemberInfo(){

     global $_W,$_GPC;

		load()->classs('weixin.account');

        $accObj= WeixinAccount::create($_W['uniacid']);

        $access_token = $accObj->fetch_token();

        $ACCESS_TOKEN = $access_token;

        $OPENID = $_W['openid'];

        $url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token={$ACCESS_TOKEN}&openid={$OPENID}&lang=zh_CN";

        $json = ihttp_get($url);

        $userInfo = @json_decode($json['content'], true);

		//同步更新到fans表

		$uid = pdo_fetchcolumn( " SELECT uid FROM ".tablename('mc_mapping_fans')." WHERE openid='".$_W['openid']."' AND uniacid='".$_W['uniacid']."'  " );

		if(!empty($uid)){

			$fans = pdo_fetch( " SELECT * FROM ".tablename('mc_members')." WHERE uid='".$uid."' " );

			if(!empty($fans)){

				$fans['nickname'] = $userInfo['nickname'];

				$fans['avatar'] = $userInfo['headimgurl'];

				

				$flag = pdo_update('mc_members',$fans,array('uid'=>$uid));

			}

		}

		return $userInfo;



	}

	

	//根据openid发送卡券

	

     public function sendWxCard($cardid,$sendopenid,$sendopenid2){

     global $_W,$_GPC;

	 $appid = $_W['account']['key'];

     $appSecret = $_W['account']['secret'];

      load()->func('communication');

     if ($data->expire_time < time()) {    

       $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$appid."&secret=".$appSecret."";

      $res = json_decode($this->httpGet($url));

      $tokens = $res->access_token;

          if(empty($tokens))

          {

          return;

          }

         

          //$postarr = '{"touser":["'.$sendopenid.'","'.$sendopenid2.'"],"wxcard":{"card_id":"'.$cardid.'"},"msgtype":"wxcard"}';

          $postarr = '{"card_id":"'.$cardid.'"}';

          $res = ihttp_post('https://api.weixin.qq.com/card/mpnews/gethtml?access_token='.$tokens,$postarr);

     }



	}

	

	private function httpGet($url) {

    $curl = curl_init();

    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    curl_setopt($curl, CURLOPT_TIMEOUT, 500);

    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);

    curl_setopt($curl, CURLOPT_URL, $url);



    $res = curl_exec($curl);

    curl_close($curl);



    return $res;

  }

  //随机字符串

  private function createNonceStr($length = 16) {

    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";

    $str = "";

    for ($i = 0; $i < $length; $i++) {

      $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);

    }

    return $str;

  }

  

  

  //jssdk添加卡券

  //获取api_ticket

  public function getCardTicket($card_id,$openid){

     global $_W,$_GPC;

	 //获取access_token

	 $data = pdo_fetch( " SELECT * FROM ".tablename('hc_newbb_card_ticket')." WHERE weid='".$_W['uniacid']."' " );

	 $appid = $_W['account']['key'];

     $appSecret = $_W['account']['secret'];

     load()->func('communication');

	 //检测ticket是否过期

     if ($data['createtime'] < time()) {    

       $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$appid."&secret=".$appSecret."";

      $res = json_decode($this->httpGet($url));

      $tokens = $res->access_token;

          if(empty($tokens))

          {

          return;

          }

         

        $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=".$tokens."&type=wx_card";

		$res = json_decode($this->httpGet($url));

		$now = TIMESTAMP;

		$now = intval($now) + 7200;

		$ticket = $res->ticket;

		$insert = array(

			'weid' => $_W['uniacid'],

			'createtime' => $now,

			'ticket' => $ticket,

		);

		if(empty($data)){

			pdo_insert('hc_newbb_card_ticket',$insert);

		}else{

			pdo_update('hc_newbb_card_ticket',$insert,array('id'=>$data['id']));

		}

		

     }else{

		$ticket = $data['ticket'];

	 }

	 

	 //获得ticket后将参数拼成字符串进行sha1加密

		$now = time();

		$timestamp = $now;

		$nonceStr = $this->createNonceStr();

		$card_id = $card_id;

		$openid = $openid;

		$string = "card_id=$card_id&jsapi_ticket=$ticket&noncestr=$nonceStr$openid=$openid&timestamp=$timestamp";



		$arr = array($card_id,$ticket,$nonceStr,$openid,$timestamp);//组装参数

        asort($arr, SORT_STRING);

		$sortString = "";

		 foreach($arr as $temp){

			$sortString = $sortString.$temp;

		 }

		$signature = sha1($sortString);

	 $cardArry = array(

		'code' =>"",

		'openid' => $openid,

		'timestamp' => $now,

		'signature' => $signature,

		'cardId' => $card_id,

		'ticket' => $ticket,

		'nonceStr' => $nonceStr,

	 );

	return $cardArry;

		

  

  }





  	//积分提现

	public function give_withdraw_money($tixian_id,$is_hongbao=0){

		global $_W,$_GPC;

		if($is_hongbao == 1){

			$tixian =pdo_fetch("SELECT * FROM ".tablename('hc_newbb_hongbao_tixian_log')." WHERE id='".$tixian_id."' ");

		}else{

			$tixian =pdo_fetch("SELECT * FROM ".tablename('hc_newbb_tixian_log')." WHERE id='".$tixian_id."' ");

		}

		if(empty($tixian)){

			$call_back = array(

				'return_code' => false,

				'return_msg' => '提现记录不存在',

			);

			return $call_back;

		}



		$setting = $this->module['config'];

		//商户号

		$mchid = $setting['mchid'];

		//商户订单号

		$partner_trade_no = date('ymdhisymd').$nonce_num;

		//用户openid

		$openid = $tixian['openid'];

		//是否校验用户姓名

		$check_name = "NO_CHECK";

		//金额

		

		$amount = $tixian['money'] *100 ;

		//付款描述

		$desc = '积分提现';

		//调用接口机器ip

		$spbill_create_ip = $_SERVER['REMOTE_ADDR'];

		//随机字符串

		$nonce_str = $this->createNonceStr();

		//公众号appid

		$mch_appid = $_W['account']['key'];

		$mch_appid = $mch_appid;

		//商户平台秘钥

		$key = $setting['mchkey'];

		if(!empty($mchid) && !empty($partner_trade_no) && !empty($openid) && !empty($check_name) && !empty($amount) && !empty($desc) && !empty($spbill_create_ip) && !empty($nonce_str) && !empty($mch_appid) &&!empty($key) ){

			return $this->send_red_bag($mchid, $partner_trade_no, $openid, $check_name, $amount, $desc, $spbill_create_ip, $nonce_str, $mch_appid, $key);

		}else{

			$call_back = array(

				'return_code' => false,

				'return_msg' => '发送红包失败',

			);

			return $callback;

		}

		

	}









	//红包函数

	function get_red_bag($tixian_id,$is_hongbao=0){

		global $_W;



		if($is_hongbao == 1){

			$tixian =pdo_fetch("SELECT * FROM ".tablename('hc_newbb_hongbao_tixian_log')." WHERE id='".$tixian_id."' ");

		}else{

			$tixian =pdo_fetch("SELECT * FROM ".tablename('hc_newbb_tixian_log')." WHERE id='".$tixian_id."' ");

		}

		if(empty($tixian)){

			$call_back = array(

				'return_code' => false,

				'return_msg' => '提现记录不存在',

			);

			return $call_back;

		}

		$setting = $this->module['config'];



		load()->func('communication');



		//公众号appid

		$wxappid = $_W['account']['key'];

		//商户平台秘钥

		$key = $setting['mchkey'];

		$mch_id = $setting['mchid'];



		//随机字符串

		$nonce_str = $this->createNonceStr();

		//随机数字

		$nonce_num = $this->createNoNum();

		//订单号

		$mch_billno = $mch_id.date('ymd').$nonce_num;

		//公众号appid



		//红包发送者名称

		$send_name = $setting['hongbao_name'];

		//红包发送数量

		$total_num = 1;

		//发起活动ip地址

		$client_ip = $_SERVER['REMOTE_ADDR'];

		//接受者openid

		$re_openid = $tixian['openid'];

		//活动名称

		$act_name = '中秋博饼';

		//备注

		$remark = "中秋红包";

		//发送金额

		$total_amount = $tixian['money'] * 100;

		//祝福语

		$wishing = $setting['hongbao_wishing'];



		$stringA = "act_name=$act_name&client_ip=$client_ip&mch_billno=$mch_billno&mch_id=$mch_id&nonce_str=$nonce_str&re_openid=$re_openid&remark=$remark&send_name=$send_name&total_amount=$total_amount&total_num=$total_num&wishing=$wishing&wxappid=$wxappid&key=$key";

		$stringSignTemp = md5($stringA);

		$signvalue = strtoupper($stringSignTemp);

		$postarr = "

			<xml>

				<sign><![CDATA[$signvalue]]></sign>

				<mch_billno><![CDATA[$mch_billno]]></mch_billno>

				<mch_id><![CDATA[$mch_id]]></mch_id>

				<wxappid><![CDATA[$wxappid]]></wxappid>

				<send_name><![CDATA[$send_name]]></send_name>

				<re_openid><![CDATA[$re_openid]]></re_openid>

				<total_amount><![CDATA[$total_amount]]></total_amount>

				<total_num><![CDATA[$total_num]]></total_num>

				<wishing><![CDATA[$wishing]]></wishing>

				<client_ip><![CDATA[$client_ip]]></client_ip>

				<act_name><![CDATA[$act_name]]></act_name>

				<remark><![CDATA[$remark]]></remark>

				<nonce_str><![CDATA[$nonce_str]]></nonce_str>

			</xml>

		";

		//$res = ihttp_post('https://api.mch.weixin.qq.com/mmpaymkttransfers/sendredpack',$postarr);

		$res = $this->curl_post_ssl('https://api.mch.weixin.qq.com/mmpaymkttransfers/sendredpack',$postarr);

		//var_dump($postarr);exit;

		$token = @json_decode($res['content'], true);

		$string = $res;

			$xml = simplexml_load_string($string);

			//$return_msg = $xml->return_msg;//这里返回的依然是个SimpleXMLElement对象

			$return_msg = (string) $xml->return_msg;//在做数据比较时，注意要先强制转换

			$return_code = (string)$xml->return_code;

			$callback = array(

				'return_msg' => $return_msg,

				'return_code' => $return_code,

				'mch_billno' => $mch_billno,

			);

			return $callback;

	}













	



	//零钱函数

	public function send_red_bag($mchid, $partner_trade_no, $openid, $check_name, $amount, $desc, $spbill_create_ip, $nonce_str, $mch_appid, $key){

    global $_W,$_GPC;

	load()->func('communication');

	

	$stringA = "amount=$amount&check_name=$check_name&desc=$desc&mch_appid=$mch_appid&mchid=$mchid&nonce_str=$nonce_str&openid=$openid&partner_trade_no=$partner_trade_no&spbill_create_ip=$spbill_create_ip&key=$key";

	

	$stringSignTemp = md5($stringA);

	$signvalue = strtoupper($stringSignTemp);

	

	$postarr = "

				<xml>

					<amount>$amount</amount>

					<check_name>$check_name</check_name>

					<desc>$desc</desc>

					<mch_appid>$mch_appid</mch_appid>

					<mchid>$mchid</mchid>

					<nonce_str>$nonce_str</nonce_str>

					<openid>$openid</openid>

					<partner_trade_no>$partner_trade_no</partner_trade_no>

					<spbill_create_ip>$spbill_create_ip</spbill_create_ip>

					<sign>$signvalue</sign>



				</xml>

				";



	$res = $this->curl_post_ssl('https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers',$postarr);



	$string = $res;

	$xml = simplexml_load_string($string);

	//$return_msg = $xml->return_msg;//这里返回的依然是个SimpleXMLElement对象

	$return_msg = (string) $xml->return_msg;//在做数据比较时，注意要先强制转换

	$return_code = (string)$xml->return_code;

	$callback = array(

		'return_msg' => $return_msg,

		'return_code' => $return_code,

		'mch_billno' => $mch_billno,

	);

	return $callback;

  

  

  

  

  

  }





 

  	function curl_post_ssl($url, $vars, $second=30,$aHeader=array())

	{

		global $_W;

		load()->func('communication');



		$ch = curl_init();

		//超时时间

		curl_setopt($ch,CURLOPT_TIMEOUT,$second);

		curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);

		//这里设置代理，如果有的话

		//curl_setopt($ch,CURLOPT_PROXY, '10.206.30.98');

		//curl_setopt($ch,CURLOPT_PROXYPORT, 8080);

		curl_setopt($ch,CURLOPT_URL,$url);

		curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);

		curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);

		//以下两种方式需选择一种

		$url = $_SERVER['DOCUMENT_ROOT'].'/payment/'.$_W['uniacid'];

		curl_setopt($ch,CURLOPT_SSLCERT,$url.'/apiclient_cert.pem');

 		curl_setopt($ch,CURLOPT_SSLKEY,$url.'/apiclient_key.pem');

 		curl_setopt($ch,CURLOPT_CAINFO,$url.'/rootca.pem');

		

		//第二种方式，两个文件合成一个.pem文件

		//curl_setopt($ch,CURLOPT_SSLCERT,getcwd().'/all.pem');

	 

		if( count($aHeader) >= 1 ){

			curl_setopt($ch, CURLOPT_HTTPHEADER, $aHeader);

		}

	 

		curl_setopt($ch,CURLOPT_POST, 1);

		curl_setopt($ch,CURLOPT_POSTFIELDS,$vars);

		$data = curl_exec($ch);



		if($data){

			curl_close($ch);

			return $data;

		}

		else { 

			$error = curl_errno($ch);

			//echo "call faild, errorCode:$error\n"; 

			curl_close($ch);

			return false;

		}

	}

	





	//返回某日的开始时间

	public function get_start_time($timestamp){

		

		$start_time = date( 'Y-m-d 00:00:00', $timestamp );

		$start_time = strtotime($start_time);

		return $start_time;

	}

	

//随机数字

function createNoNum($length = 10) {

	$chars = "0123456789";

	$str = "";

	for ($i = 0; $i < $length; $i++) {

	  $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);

	}

	return $str;

}



    public function get_week_time($timestamp){

        //当前日期 

        $sdefaultDate = date("Y-m-d",$timestamp); 

        //$first =1 表示每周星期一为开始日期 0表示每周日为开始日期 

        $first=0; 

        //获取当前周的第几天 周日是 0 周一到周六是 1 - 6 

        $w=date('w',strtotime($sdefaultDate)); 

        //获取本周开始日期，如果$w是0，则表示周日，减去 6 天 

        $week_start=date('Y-m-d',strtotime("$sdefaultDate -".($w ? $w - $first : 6).' days')); 

        //本周结束日期 

        $week_end=date('Y-m-d',strtotime("$week_start +6 days"));

        $week_time['start_time'] = $week_start;

        $week_time['end_time'] = $week_end;

        return $week_time;

    }







//高级接口部分

    public function doMobileOauth2(){

        global $_W,$_GPC;



        //高级接口

        $weid = $_W['uniacid'];//当前公众号ID

        load()->func('communication');



        if (isset($_GPC['code'])){

            $appid = $_W['account']['key'];       

            $secret = $_W['account']['secret'];

            $serverapp = $_W['account']['level'];

            $state = $_GPC['state'];

            //1为关注用户, 0为未关注用户

            //查询活动时间

            $code = $_GPC['code'];

            $oauth2_code = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=".$appid."&secret=".$secret."&code=".$code."&grant_type=authorization_code";

            $content = ihttp_get($oauth2_code);

            $token = @json_decode($content['content'], true);



            if(empty($token) || !is_array($token) || empty($token['access_token']) || empty($token['openid'])) {

                echo '<h1>获取微信公众号授权'.$code.'失败[无法取得token以及openid], 请稍后重试！ 公众平台返回原始数据为: <br />' . $content['meta'].'<h1>';

                exit;

            }

            $from_user = $token['openid'];

            //再次查询是否为关注用户

            $profile = pdo_fetch( " SELECT * FROM ".tablename('mc_mapping_fans')." WHERE openid='".$from_user."' AND uniacid=".$_W['uniacid']." " );

            //$profile  = fans_search($from_user, array('follow'));   



            //关注用户直接获取信息   

            if ($profile['follow']==1){

                $state = 1;

            }

            /*else{

                //未关注用户跳转到授权页

                $url = $_W['siteroot']."app/".$this->createMobileUrl('Oauth2', array(), true);

                $oauth2_code = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".$appid."&redirect_uri=".urlencode($url)."&response_type=code&scope=snsapi_userinfo&state=0#wechat_redirect";               

                header("location:$oauth2_code");

            }*/

            //未关注用户和关注用户取全局access_token值的方式不一样

            if ($state==1){

                $oauth2_url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$appid."&secret=".$secret."";

                $content = ihttp_get($oauth2_url);

                $token_all = @json_decode($content['content'], true);

                if(empty($token_all) || !is_array($token_all) || empty($token_all['access_token'])) {

                    echo '<h1>获取微信公众号授权失败[无法取得access_token], 请稍后重试！ 公众平台返回原始数据为: <br />' . $content['meta'].'<h1>';

                    exit;

                }

                $access_token = $token_all['access_token'];

                $oauth2_url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$access_token."&openid=".$from_user."&lang=zh_CN";

            }else{

                $access_token = $token['access_token'];

                $oauth2_url = "https://api.weixin.qq.com/sns/userinfo?access_token=".$access_token."&openid=".$from_user."&lang=zh_CN";

            }



            //使用全局ACCESS_TOKEN获取OpenID的详细信息           

            $content = ihttp_get($oauth2_url);

            $info = @json_decode($content['content'], true);

            if(empty($info) || !is_array($info) || empty($info['openid'])  || empty($info['nickname']) ) {

                echo '<h1>获取微信公众号授权失败[无法取得info], 请稍后重试！<h1>';

                exit;

            }



            //写入到member表中

            $member = pdo_fetch( " SELECT * FROM ".tablename('hc_newbb_member')." WHERE openid='".$info['openid']."' AND weid='".$_W['uniacid']."'  " );

        	$insert = array(

        		'weid' => $_W['uniacid'],

        		'openid' => $info['openid'],

        		'createtime' => TIMESTAMP,

        		'nickname' => $info['nickname'],

        		'headimg' => $info['headimgurl'],

        		'type' => 1,

        	);

            if(empty($member)){
            	pdo_insert('hc_newbb_member',$insert);
            }else{
            	$insert['id'] = $member['id'];
            	pdo_update('hc_newbb_member',$insert,array('id'=>$member['id']));
            }





        $return_url = $_GPC['fanhuiurl'];

		

		$cookie_name = $this->get_cookie_name();

		setcookie($cookie_name,$info['openid'],time()+86400*30);



        header("location:$return_url");   





        }else{

            echo '<h1>网页授权域名设置出错!</h1>';

            exit;       

        }



    }











	

}

