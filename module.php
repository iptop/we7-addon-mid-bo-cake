<?php
/**
 * 中秋博饼模块
 *
 * 疯狂却不失细腻——厦门火池网络		www.weixiamen.cn
 */
defined('IN_IA') or exit('Access Denied');

class hc_tonganbbModule extends WeModule {
	public $tablename = 'hc_tonganbb_reply';

		public function fieldsFormDisplay($rid = 0) {
		global $_W;
		global $_GPC;
		load()->func('tpl');
		if (!empty($rid)) {
			$reply = pdo_fetch("SELECT * FROM ".tablename($this->tablename)." WHERE rid = :rid ORDER BY `id` DESC", array(':rid' => $rid));
			$yixiu = pdo_fetch(" SELECT * FROM ".tablename('hc_tonganbb_award')." WHERE `rid`=".$rid." AND `title`='一秀'  ");
			
			$erju = pdo_fetch(" SELECT * FROM ".tablename('hc_tonganbb_award')." WHERE `rid`=".$rid." AND `title`='二举'  ");
			$sanhong = pdo_fetch(" SELECT * FROM ".tablename('hc_tonganbb_award')." WHERE `rid`=".$rid." AND `title`='三红'  ");
			$sijing = pdo_fetch(" SELECT * FROM ".tablename('hc_tonganbb_award')." WHERE `rid`=".$rid." AND `title`='四进'  ");
			$duitang = pdo_fetch(" SELECT * FROM ".tablename('hc_tonganbb_award')." WHERE `rid`=".$rid." AND `title`='对堂'  ");
			$putongzhuangyuan = pdo_fetch(" SELECT * FROM ".tablename('hc_tonganbb_award')." WHERE `rid`=".$rid." AND `title`='普通状元'  ");
			$wuzi = pdo_fetch(" SELECT * FROM ".tablename('hc_tonganbb_award')." WHERE `rid`=".$rid." AND `title`='五子'  ");
			$wuhong = pdo_fetch(" SELECT * FROM ".tablename('hc_tonganbb_award')." WHERE `rid`=".$rid." AND `title`='五红'  ");
			$liupu = pdo_fetch(" SELECT * FROM ".tablename('hc_tonganbb_award')." WHERE `rid`=".$rid." AND `title`='六杯红'  ");
			$chajinghua = pdo_fetch(" SELECT * FROM ".tablename('hc_tonganbb_award')." WHERE `rid`=".$rid." AND `title`='状元插金花'  ");

				
		include $this->template('form');
		}
	}

	public function fieldsFormValidate($rid = 0) {
		return true;
	}

	public function fieldsFormSubmit($rid = 0) {
	load()->func('file');
		global $_GPC, $_W;
		$actime = $_GPC['actime'];
		if(intval($_GPC['prace_times']) < intval($_GPC['sharecount'])){
			message("转发奖励次数不能大于每天最大奖励次数");
		}
		$insert = array(
			'rid' => $rid,
			'weid' => $_W['uniacid'],
			'picture' => $_GPC['picture'],
			'description' => $_GPC['description'],
			'card_id' => $_GPC['card_id'],
			'registimg' => $_GPC['registimg'],
			'periodlottery' => intval($_GPC['periodlottery']),
			'maxlottery' => intval($_GPC['maxlottery']),
			'rule' => htmlspecialchars_decode($_GPC['rule']),
			'sharecount' => intval($_GPC['sharecount']),
			'misscredit' => intval($_GPC['misscredit']),
			'prace_times' => intval($_GPC['prace_times']),
			'start_time' => strtotime($_GPC['start_time']),
			'end_time' => strtotime($_GPC['end_time']),
			'title' => $_GPC['title'],
			'indexPicture' =>$_GPC['indexPicture'],
			'headpic' => $_GPC['headpic'],
			'headurl' => $_GPC['headurl'],
			'baoxiangname' => $_GPC['baoxiangname'],
			'zhuanfaimg' => $_GPC['zhuanfaimg'],
			'zhuanfatitle' => $_GPC['zhuanfatitle'],
			'zhuanfacontent' => $_GPC['zhuanfacontent'],
			'baoxiangsize' => $_GPC['baoxiangsize'],
			'baoxiangimg' => $_GPC['baoxiangimg'],
			'baoxiangorder' => $_GPC['baoxiangorder'],
			'panzi' => $_GPC['panzi'],
			'guanzhuUrl' => $_GPC['guanzhuUrl'],
			'fansstatus' => intval($_GPC['fansstatus']),
			
		);
		$id = intval($_GPC['id']);
		if (empty($id)) {
			pdo_insert($this->tablename, $insert);
		} else {
			if (!empty($_GPC['picture'])) {
			//	file_delete($_GPC['picture-old']);
			} else {
				unset($insert['picture']);
			}
			pdo_update($this->tablename, $insert, array('id' => $id));
		}
		
			$yixiu = array(
				'probalilty' => $_GPC['yixiuPro'],
				'total' => $_GPC['yixiuNum'],
				'credit' => $_GPC['yixiucredit'],
				'card_id' => $_GPC['yixiucard'],
				'title' => '一秀',
				'weid' => $_W['uniacid'],
				'rid' => $rid,
				'description' => $_GPC['yixiuDes']
			);
			
			$erju = array(
				'probalilty' => $_GPC['erjuPro'],
				'total' => $_GPC['erjuNum'],
				'credit' => $_GPC['erjucredit'],
				'card_id' => $_GPC['erjucard'],
				'title' => '二举',
				'weid' => $_W['uniacid'],
				'rid' => $rid,
				'description' => $_GPC['erjuDes']
			);
			
			$sanhong = array(
				'probalilty' => $_GPC['sanhongPro'],
				'total' => $_GPC['sanhongNum'],
				'credit' => $_GPC['sanhongcredit'],
				'card_id' => $_GPC['sanhongcard'],
				'title' => '三红',
				'weid' => $_W['uniacid'],
				'rid' => $rid,
				'description' => $_GPC['sanhongDes']
			);
			
			$sijing = array(
				'probalilty' => $_GPC['sijingPro'],
				'total' => $_GPC['sijingNum'],
				'credit' => $_GPC['sijingcredit'],
				'card_id' => $_GPC['sijingcard'],
				'title' => '四进',
				'weid' => $_W['uniacid'],
				'rid' => $rid,
				'description' => $_GPC['sijingDes']
			);
			
			$duitang = array(
				'probalilty' => $_GPC['duitangPro'],
				'total' => $_GPC['duitangNum'],
				'credit' => $_GPC['duitangcredit'],
				'card_id' => $_GPC['duitangcard'],
				'title' =>'对堂',
				'weid' => $_W['uniacid'],
				'rid' => $rid,
				'description' => $_GPC['duitangDes']
			);
			
			$putongzhuangyuan = array(
				'probalilty' => $_GPC['putongzhuangyuanPro'],
				'total' => $_GPC['putongzhuangyuanNum'],
				'credit' => $_GPC['putongzhuangyuancredit'],
				'card_id' => $_GPC['putongzhuangyuancard'],
				'title' => '普通状元',
				'weid' => $_W['uniacid'],
				'rid' => $rid,
				'description' => $_GPC['putongzhuangyuanDes']
			);
			
			$wuzi = array(
				'probalilty' => $_GPC['wuziPro'],
				'total' => $_GPC['wuziNum'],
				'credit' => $_GPC['wuzicredit'],
				'card_id' => $_GPC['wuzicard'],
				'title' => '五子',
				'weid' => $_W['uniacid'],
				'rid' => $rid,
				'description' => $_GPC['wuziDes']
			);
			
			$wuhong = array(
				'probalilty' => $_GPC['wuhongPro'],
				'total' => $_GPC['wuhongNum'],
				'credit' => $_GPC['wuhongcredit'],
				'card_id' => $_GPC['wuhongcard'],
				'title' => '五红',
				'weid' => $_W['uniacid'],
				'rid' => $rid,
				'description' => $_GPC['wuhongDes']
			);
			
			$liupu = array(
				'probalilty' => $_GPC['liupuPro'],
				'total' => $_GPC['liupuNum'],
				'credit' => $_GPC['liupucredit'],
				'card_id' => $_GPC['liupucard'],
				'title' => '六杯红',
				'weid' => $_W['uniacid'],
				'rid' => $rid,
				'description' => $_GPC['liupuDes']
			);
			
			$chajinghua = array(
				'probalilty' => $_GPC['chajinghuaPro'],
				'total' => $_GPC['chajinghuaNum'],
				'credit' => $_GPC['chajinghuacredit'],
				'card_id' => $_GPC['chajinghuacard'],
				'title' => '状元插金花',
				'weid' => $_W['uniacid'],
				'rid' => $rid,
				'description' => $_GPC['chajinghuaDes']
			);

			$yixiutemp = pdo_fetch(" SELECT * FROM ".tablename('hc_tonganbb_award')." WHERE `rid`=".$rid."   ");
			if($yixiutemp == null && $_GPC['yixiuPro']!=null){
				pdo_insert('hc_tonganbb_award',$yixiu);
				pdo_insert('hc_tonganbb_award',$erju);
				pdo_insert('hc_tonganbb_award',$sanhong);
				pdo_insert('hc_tonganbb_award',$sijing);
				pdo_insert('hc_tonganbb_award',$duitang);
				pdo_insert('hc_tonganbb_award',$putongzhuangyuan);
				pdo_insert('hc_tonganbb_award',$wuzi);
				pdo_insert('hc_tonganbb_award',$wuhong);
				pdo_insert('hc_tonganbb_award',$liupu);
				pdo_insert('hc_tonganbb_award',$chajinghua);
			
			}
			else{
				pdo_update('hc_tonganbb_award',$yixiu,array('rid'=>$rid, 'title'=>'一秀'));
				pdo_update('hc_tonganbb_award',$erju,array('rid'=>$rid, 'title'=>'二举'));
				pdo_update('hc_tonganbb_award',$sanhong,array('rid'=>$rid, 'title'=>'三红'));
				pdo_update('hc_tonganbb_award',$sijing,array('rid'=>$rid, 'title'=>'四进'));
				pdo_update('hc_tonganbb_award',$duitang,array('rid'=>$rid, 'title'=>'对堂'));
				pdo_update('hc_tonganbb_award',$putongzhuangyuan,array('rid'=>$rid, 'title'=>'普通状元'));
				pdo_update('hc_tonganbb_award',$wuzi,array('rid'=>$rid, 'title'=>'五子'));
				pdo_update('hc_tonganbb_award',$wuhong,array('rid'=>$rid, 'title'=>'五红'));
				pdo_update('hc_tonganbb_award',$liupu,array('rid'=>$rid, 'title'=>'六杯红'));
				pdo_update('hc_tonganbb_award',$chajinghua,array('rid'=>$rid, 'title'=>'状元插金花'));
			}
		
		
	}

	public function ruleDeleted($rid = 0) {
		global $_W;
			load()->func('file');
		$replies = pdo_fetchall("SELECT id, picture FROM ".tablename($this->tablename)." WHERE rid = '$rid'");
		$deleteid = array();
		
		if (!empty($replies)) {
			foreach ($replies as $index => $row) {
				file_delete($row['picture']);
				$deleteid[] = $row['id'];
			}
		}
		pdo_delete($this->tablename, "id IN ('".implode("','", $deleteid)."')");
		
		return true;
	}
	
	
	
		public function settingsDisplay($settings) {
		global $_GPC, $_W;
		load()->func('tpl');

		if(checksubmit()) {
			$cfg = array();
			$cfg['headpic'] = $_GPC['headpic'];
			$cfg['rule_des'] = htmlspecialchars_decode($_GPC['rule_des']);



			if($this->saveSettings($cfg)) {
				message('保存成功', 'refresh');
			}
		}		

		include $this->template('setting');
	}
	
	
	
	
	
	
	
	
}
