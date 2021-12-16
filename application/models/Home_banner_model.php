<?php

class Home_banner_model extends Common_model {

	//put your code here
	public $_table = tbl_homebanner;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
	public $_order;

	function __construct() {
		parent::__construct();
	}
	 
	function getActiveHomebanner($memberid=0,$channelid=0){
		
		$this->load->model('Channel_model', 'Channel');
		$channeldata = $this->Channel->getMemberChannelData($memberid);
		$memberbasicsalesprice = (!empty($channeldata['memberbasicsalesprice']))?$channeldata['memberbasicsalesprice']:0;
		$memberspecificproduct = (!empty($channeldata['memberspecificproduct']))?$channeldata['memberspecificproduct']:0;
		$currentsellerid = (!empty($channeldata['currentsellerid']))?$channeldata['currentsellerid']:0;

		$this->readdb->select("productid as id,image");
		$this->readdb->from($this->_table." as h");
		$this->readdb->join(tbl_product." as p","p.id=h.productid","LEFT");
		$this->readdb->where("h.status=1");
		$this->readdb->where("((h.addedby=".$currentsellerid." AND FIND_IN_SET('".$channelid."', h.channelid)>0) OR (FIND_IN_SET('".$channelid."', h.channelid)>0 AND h.type=0))");
		if($memberid!=0 || $channelid!=0){
			$this->readdb->where("(IF(
									(SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."')>0,
									h.productid IN(SELECT mp.productid FROM ".tbl_memberproduct." as mp INNER JOIN ".tbl_membervariantprices." as mvp ON (mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid AND mvp.price>0) WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."' AND mvp.priceid IN (SELECT pp.id FROM ".tbl_productprices." as pp WHERE pp.id=mvp.priceid) GROUP BY mp.productid),
									
									IF(
										(".$currentsellerid."=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.memberid='".$currentsellerid."')=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."')=0),
										h.productid IN (SELECT productid FROM ".tbl_productbasicpricemapping." WHERE channelid = (SELECT channelid FROM member WHERE id='".$memberid."') AND salesprice >0 AND allowproduct = 1 AND productpriceid IN (SELECT id FROM ".tbl_productprices." WHERE productid=p.id) GROUP BY productid),
										h.productid IN(SELECT mp.productid FROM ".tbl_memberproduct." as mp INNER JOIN ".tbl_membervariantprices." as mvp ON mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid AND mvp.salesprice>0 AND mvp.productallow=1  WHERE mp.sellermemberid=(SELECT mainmemberid FROM ".tbl_membermapping." where submemberid='".$currentsellerid."') and mp.memberid=".$currentsellerid." AND mvp.priceid IN (SELECT pp.id FROM ".tbl_productprices." as pp WHERE pp.id=mvp.priceid and pp.productid=mp.productid) AND (SELECT c.memberbasicsalesprice FROM ".tbl_channel." as c WHERE c.id=(SELECT m.channelid FROM ".tbl_member." as m WHERE m.id=mp.memberid))=1 GROUP BY mp.productid)
									)
								) OR h.productid=0)");

			/*$where = "((h.addedby IN (SELECT mainmemberid FROM ".tbl_membermapping." WHERE submemberid=".$memberid.") AND h.status=1 AND (FIND_IN_SET('".$channelid."', h.channelid)>0)) OR (FIND_IN_SET('".$channelid."', h.channelid)>0 AND h.type=0 AND h.status=1)) 
			
			AND ((IF(
				(SELECT count(id) FROM ".tbl_memberproduct." WHERE memberid='".$memberid."')>0,
		
				h.productid IN ((SELECT mp.productid FROM ".tbl_memberproduct." as mp 
						INNER JOIN ".tbl_member." as m ON m.id=mp.memberid 
						WHERE mp.memberid='".$memberid."' AND 
						(SELECT count(mvp.productallow) FROM ".tbl_membervariantprices." as mvp WHERE mvp.salesprice>0 AND mvp.memberid=mp.memberid AND mvp.priceid IN (SELECT id FROM ".tbl_productprices." WHERE productid=mp.productid) AND mvp.productallow=1 LIMIT 1)>0 AND 
						(IFNULL((SELECT 1 FROM ".tbl_channel." as c WHERE c.id = `m`.`channelid` AND c.memberspecificproduct=1),0) = 1 OR 0=0))),
				
						h.productid IN ((SELECT mp.productid FROM ".tbl_memberproduct." as mp 
						INNER JOIN ".tbl_member." as m ON m.id=mp.memberid 
						WHERE mp.memberid='".$memberid."' AND 
						(SELECT count(mvp.productallow) FROM ".tbl_membervariantprices." as mvp WHERE mvp.salesprice>0 AND mvp.memberid=mp.memberid AND mvp.priceid IN (SELECT id FROM ".tbl_productprices." WHERE productid=mp.productid) AND mvp.productallow=1 LIMIT 1)>0 AND 
						(IFNULL((SELECT 1 FROM ".tbl_channel." as c WHERE c.id = `m`.`channelid` AND c.memberspecificproduct=1),0) = 1 OR 0=0)))
			)) OR h.productid=0) ";*/
		}
		
		$this->readdb->order_by("h.inorder","ASC");
		$query = $this->readdb->get();
							// echo $this->db->last_query(); exit;
		return $query->result_array();
	}
}
