<?php

class Quotation_model extends Common_model {

    public $_table = tbl_quotation;
    public $_fields = "*";
    public $_where = array();
    public $_except_fields = array();
    public $_order = 'id DESC';
    public $_detatableorder = array('id'=>'DESC');

    public $column_order = array(null,'membername','sellermembername','q.quotationid', 'q.quotationdate','q.status','payableamount');
        
     public $column_search = array('m.name','((select name from '.tbl_member.' where id=sellermemberid))','q.quotationid','q.quotationdate','q.status','payableamount');

    function __construct() {
        parent::__construct();
    }
    function sendQuotationMailToBuyer($QuotationData,$file){
        /***************send email to buyer***************************/
        if(!empty($QuotationData)){
            $buyername = $QuotationData['quotationdetail']['buyername'];
            $buyeremail = $QuotationData['quotationdetail']['buyeremail'];
            
            if(!empty($buyeremail)){
                $mailto = $buyeremail;
                $from_mail = explode(",",COMPANY_EMAIL)[0];
                $from_name = COMPANY_NAME;

                $subject= array("{companyname}"=>COMPANY_NAME,"{quotationnumber}"=>$QuotationData['quotationdetail']['quotationid']);
                $totalamount = round($QuotationData['quotationdetail']['netamount']);

                $mailBodyArr = array(
                            "{logo}" => '<a href="'. DOMAIN_URL.'"><img src="' . MAIN_LOGO_IMAGE_URL. COMPANY_LOGO.'" alt="' . COMPANY_NAME . '" style="border: none; display: inline; font-size: 14px; font-weight: bold; height: auto; line-height: 100%; outline: none; text-decoration: none; text-transform: capitalize;"/></a>',
                            "{buyername}" => $buyername,
                            "{quotationnumber}" => $QuotationData['quotationdetail']['quotationid'],
                            "{quotationdate}" => $this->general_model->displaydate($QuotationData['quotationdetail']['quotationdate']),
                            "{amount}" => numberFormat(round($totalamount),2,','),
                            "{companyname}" => COMPANY_NAME,
                            "{companyemail}" => explode(",",COMPANY_EMAIL)[0]
                        );
                
                //Send mail with email format store in database
                $mailid=array_search("Quotation For Buyer",$this->Emailformattype);
                $emailSend = $this->Order->mail_attachment($file, QUOTATION_PATH, $mailto, $from_mail, $from_name, $from_mail, $subject, $mailBodyArr,$mailid);

                return $emailSend;
            }
        }
        return false;
    }
    function generatequotation($quotationid){
        
        $modifieddate = $this->general_model->getCurrentDateTime();
        if(is_null($this->session->userdata(base_url() . 'MEMBERID'))){
            $modifiedby = $this->session->userdata(base_url() . 'ADMINID');
        }else{
            $modifiedby = $this->session->userdata(base_url() . 'MEMBERID');
        }
        
        $companyname = $this->getCompanyName();
        $companyname = str_replace(" ", "", strtolower($companyname['businessname']));
        
        if ($quotationid) {
            $query =    $this->readdb->select("id")
                                 ->from($this->_table)
                                 ->where("id=".$quotationid." AND quotationfile!=''")
                                 ->get();
            $data = $query->result_array();
            
            if($query->num_rows() == 0){
                $updatedata = array(
                    'modifieddate'=>$modifieddate,
                    'modifiedby'=>$modifiedby,
                );
                $updatedata=array_map('trim',$updatedata);
                $this->writedb->set($updatedata);
                $this->writedb->set('quotationfile',"CONCAT('$companyname-', quotationid,'.pdf')",FALSE);
                $this->writedb->where("id=".$quotationid);
                $this->writedb->update($this->_table);
            }

            $PostData['quotationdata'] = $this->getQuotationDetails($quotationid);
            $this->_fields = '*';

            $this->load->model('Invoice_setting_model','Invoice_setting');
            $PostData['invoicesettingdata'] = $this->Invoice_setting->getShipperDetails();
            $PostData['type'] = 1;

            $header=$this->load->view(ADMINFOLDER . 'Quotationheader', $PostData,true);
            $html=$this->load->view(ADMINFOLDER . 'Quotationformatforpdf', $PostData,true);
            
            $this->load->library('m_pdf');
            //actually, you can pass mPDF parameter on this load() function
            $pdf = $this->m_pdf->load();

            // Set a simple Footer including the page number
            $pdf->setFooter('Side {PAGENO} 0f {nb}');

            //this the the PDF filename that user will get to download
            if(!is_dir(QUOTATION_PATH)){
                mkdir(QUOTATION_PATH);
            }
            $filename = $companyname."-".$PostData['quotationdata']['quotationdetail']['quotationid'].".pdf";
            $pdfFilePath =QUOTATION_PATH.$filename;

            $pdf->AddPage('', // L - landscape, P - portrait 
                        '', '', '', '',
                        5, // margin_left
                        5, // margin right
                       80, // margin top
                       15, // margin bottom
                        10, // margin header
                        10); // margin footer

            $this->load->model('Common_model');
            $stylesheet = $this->Common_model->curl_get_contents(ADMIN_CSS_URL.'bootstrap.min.css'); // external css
            $stylesheet = $this->Common_model->curl_get_contents(ADMIN_CSS_URL.'styles.css'); // external css
            $pdf->WriteHTML($stylesheet,1);
            $pdf->SetHTMLHeader($header,'',true);
            $pdf->WriteHTML($html,0);

            //offer it to user via browser download! (The PDF won't be saved on your server HDD)
            $pdf->Output($pdfFilePath, "F");

            return json_encode(array("error"=>"1","quotation"=>QUOTATION.$filename));
        } else {
            return json_encode(array("error"=>"0"));
        }
    }

    
    function orderdetail(){

        $this->readdb->select('DATE_FORMAT(createddate, "%d/%m/%Y %H:%i:%s") as date,amount,status');
        $this->readdb->from($this->_table);        
        $query=$this->readdb->get();
        return $query->result_array();
    }
    function recentquotation(){
        $MEMBERID = $this->session->userdata(base_url().'MEMBERID');
        $where = "1=1";
        if(!is_null($MEMBERID)){
            $where = "(((o.memberid IN (SELECT submemberid FROM ".tbl_membermapping." WHERE mainmemberid = ".$MEMBERID.") OR o.sellermemberid = ".$MEMBERID." ) AND (sellermemberid IN (SELECT mainmemberid FROM ".tbl_membermapping." WHERE submemberid = ".$MEMBERID.") OR o.memberid  IN (SELECT submemberid FROM ".tbl_membermapping." WHERE mainmemberid = ".$MEMBERID.") OR o.memberid=0) AND (o.addedby IN (SELECT submemberid FROM ".tbl_membermapping." WHERE mainmemberid = ".$MEMBERID.") OR o.addedby=".$MEMBERID.")) OR (o.sellermemberid IN (SELECT mainmemberid FROM ".tbl_membermapping." WHERE submemberid = ".$MEMBERID.") OR o.memberid=".$MEMBERID.") AND (o.memberid IN (SELECT submemberid FROM ".tbl_membermapping." WHERE mainmemberid = ".$MEMBERID.") OR o.sellermemberid  IN (SELECT mainmemberid FROM ".tbl_membermapping." WHERE submemberid = ".$MEMBERID.") OR o.sellermemberid=0) AND (o.addedby IN (SELECT mainmemberid FROM ".tbl_membermapping." WHERE submemberid = ".$MEMBERID.") OR o.addedby=".$MEMBERID.")) AND o.addedby!=0";
        }
        if (isset($this->viewData['mainmenuvisibility']['menuviewalldata']) && strpos($this->viewData['mainmenuvisibility']['menuviewalldata'], ',' . $this->session->userdata[base_url() . 'ADMINUSERTYPE'] . ',') === false) {
            $where .= " AND ((o.addedby=".$this->session->userdata(base_url().'ADMINID')." AND o.type=0) or o.salespersonid=".$this->session->userdata(base_url().'ADMINID').")";
        }
        $query = $this->readdb->select('o.id,o.quotationid,(quotationamount+taxamount)as totalamount, m.name as membername, m.membercode,IFNULL(m.id,"") as memberid,IFNULL(m.channelid,"0") as channelid')
                        ->from($this->_table." as o")
                        ->join(tbl_member." as m","m.id=o.memberid","left")
                        ->where($where)
                        ->limit(10)
                        ->order_by("o.id","desc")
                        ->get();
        return $query->result_array();
    }
    function getQuotationHistoryDetails($memberid,$type,$status,$counter)
	{   
        $limit=10;
        if($counter < 0){ $counter=0; }

        $this->readdb->select('s.allowmultiplememberwithsamechannel,c.multiplememberwithsamechannel, c.multiplememberchannel');
        $this->readdb->from(tbl_systemconfiguration." as s"); 
        $this->readdb->join(tbl_channel." as c","c.id=(SELECT channelid FROM ".tbl_member." WHERE id=".$memberid.")","INNER");
        $systemconfiguration = $this->readdb->get()->row_array();


        if($systemconfiguration['allowmultiplememberwithsamechannel']==1 && $systemconfiguration['multiplememberwithsamechannel']==1 && $systemconfiguration['multiplememberchannel']!=''){
            if($type==1){
                //Sales Quotation
                $where = '(q.sellermemberid='.$memberid.' OR FIND_IN_SET(seller.channelid, (SELECT c.multiplememberchannel FROM '.tbl_channel.' as c WHERE c.id=(SELECT channelid FROM '.tbl_member.' WHERE id='.$memberid.')))>0)'; 
            }else if($type==2){
                //Purchase Quotation
                $where = 'q.memberid = '.$memberid.' AND q.sellermemberid = (SELECT mainmemberid FROM '.tbl_membermapping.' WHERE submemberid='.$memberid.')';
            }
        }else{
            if($type==1){
            
                //$where = '(memberid IN (SELECT submemberid FROM '.tbl_membermapping.' WHERE mainmemberid = '.$memberid.') OR '.$memberid.'=0) AND sellermemberid='.$memberid;

                $where = '(q.memberid IN (SELECT submemberid FROM '.tbl_membermapping.' WHERE mainmemberid = '.$memberid.') OR q.sellermemberid='.$memberid.') AND (q.sellermemberid IN (SELECT mainmemberid FROM '.tbl_membermapping.' WHERE submemberid = '.$memberid.') OR q.memberid  IN (SELECT submemberid FROM '.tbl_membermapping.' WHERE mainmemberid = '.$memberid.') OR q.memberid=0) AND (q.addedby IN (SELECT submemberid FROM '.tbl_membermapping.' WHERE mainmemberid = '.$memberid.') OR q.addedby='.$memberid.') AND q.addedby!=0';

            }else if($type==2){
            
                $where = '(sellermemberid IN (SELECT mainmemberid FROM '.tbl_membermapping.' WHERE submemberid = '.$memberid.') OR memberid='.$memberid.') AND (memberid IN (SELECT submemberid FROM '.tbl_membermapping.' WHERE mainmemberid = '.$memberid.') OR sellermemberid  IN (SELECT mainmemberid FROM '.tbl_membermapping.' WHERE submemberid = '.$memberid.') OR sellermemberid=0) AND (q.addedby IN (SELECT mainmemberid FROM '.tbl_membermapping.' WHERE submemberid = '.$memberid.') OR q.addedby='.$memberid.') AND q.addedby!=0';
            }
        }
        if($status!=""){
            $where =($where." AND q.status=".$status." AND q.type=1");
        }else{
            $where =($where." AND q.type=1");
        }
       
        $query = $this->readdb->select("q.id,q.quotationid as quotationnumber,
                                    q.status,q.createddate,
                                    (select count(id) from ".tbl_quotationproducts." where quotationid=q.id)as itemcount,
                                    (select sum(finalprice) from ".tbl_quotationproducts." where quotationid=q.id)as orderammount,
                                    q.quotationamount,q.discountpercentage,
                                    q.discountamount,q.taxamount,q.globaldiscount,
                                    CAST((q.payableamount + IFNULL((SELECT SUM(amount) FROM ".tbl_extrachargemapping." WHERE referenceid=q.id AND type=1),0)) AS DECIMAL(14,2)) as payableamount,q.couponcodeamount,q.addquotationtype,
                                    q.resonforrejection,
                                    IFNULL(buyer.name, '') as buyername
                                
                                ")
                            
                        ->from($this->_table." as q")
                        ->join(tbl_member." as buyer","buyer.id=q.memberid","LEFT")
                        ->join(tbl_member." as seller","seller.id=q.sellermemberid","LEFT")
                        ->where($where)
                        ->group_by('q.quotationid')
                        ->order_by('q.id DESC')
                        ->limit($limit,$counter)
                        ->get();
        //echo $this->readdb->last_query(); exit;       
        if($query->num_rows() > 0) {
            return $query->result_array();
        } else {
			return array();
        }
    }
    function _get_datatables_query(){  

        $channelid = isset($_REQUEST['channelid'])?$_REQUEST['channelid']:0;
        $startdate = $this->general_model->convertdate($_REQUEST['startdate']);
        $enddate = $this->general_model->convertdate($_REQUEST['enddate']);
        $status = $_REQUEST['status'];
        $salespersonid = isset($_REQUEST['salespersonid'])?$_REQUEST['salespersonid']:0;

        $reportingto = $this->session->userdata(base_url().'REPORTINGTO');
        $memberid = $this->session->userdata(base_url().'MEMBERID');
        $salesmemberid = isset($_REQUEST['memberid'])?$_REQUEST['memberid']:0;
        $CHANNELID = $this->session->userdata(base_url().'CHANNELID');

        $this->readdb->select('q.id,q.quotationid,q.quotationdate,q.status,quotationamount,
                        (select sum(finalprice) from '.tbl_quotationproducts.' where quotationid = q.id ) as finalprice,q.createddate as date, q.memberid,m.channelid,m.name as membername, (q.payableamount + IFNULL((SELECT SUM(amount) FROM '.tbl_extrachargemapping.' WHERE referenceid=q.id AND type=1),0)) as netamount,q.addquotationtype,q.type,
                        m2.name as sellermembername,
                        m2.channelid as sellerchannelid,
                        m.membercode as membercode,
                        @primarynumber:=IF(m.isprimarywhatsappno=1,m.mobile,"") as primarynumber,
                        @secondarynumber:=IF(m.issecondarywhatsappno=1,m.secondarymobileno,"") as secondarynumber,
                        IF(@primarynumber="",IF(m.issecondarywhatsappno=1,CONCAT(m.secondarycountrycode,m.secondarymobileno),""),CONCAT(m.countrycode,@primarynumber)) as whatsappno,
                        m2.membercode as sellermembercode,
                        q.sellermemberid,(select name from '.tbl_member.' where id=q.addedby) as addedby,
                        (select membercode from '.tbl_member.' where id=q.addedby) as addedbycode,
                        IFNULL((select channelid from '.tbl_member.' where id=q.addedby),0) as addedbychannelid');
        
        $this->readdb->from($this->_table." as q");
        $this->readdb->join(tbl_member." as m","m.id=q.memberid","left");
        $this->readdb->join(tbl_member." as m2","m2.id=q.sellermemberid","left");
        $this->readdb->where("q.memberid!=0");
        
        $where = '';
        if(!is_null($memberid)) {
            if(ALLOWMULTIPLEMEMBERWITHSAMECHANNEL==1 && channel_multiplememberwithsamechannel==1 && channel_multiplememberchannel!=''){

                if(isset($_REQUEST['displaytype']) && $_REQUEST['displaytype']=='1'){

                    $where .= ' AND q.memberid='.$memberid.' AND q.sellermemberid IN(SELECT mainmemberid FROM '.tbl_membermapping.' WHERE submemberid='.$memberid.')';
                }else if(!is_null($memberid)) {
                    
                    $where .= ' AND (q.sellermemberid='.$memberid.' OR FIND_IN_SET(m2.channelid, (SELECT c.multiplememberchannel FROM '.tbl_channel.' as c WHERE c.id='.$CHANNELID.'))>0)';
                }
            }else{

                if(isset($_REQUEST['displaytype']) && $_REQUEST['displaytype']=='1'){
                    $where .= ' AND q.sellermemberid IN (SELECT mainmemberid FROM '.tbl_membermapping.' WHERE submemberid='.$memberid.') AND q.memberid='.$memberid.' AND q.addedby!=0';
                }else if(!is_null($memberid)) {
                    $where .= ' AND q.sellermemberid='.$memberid;
                }
            }
        }

        if($channelid != 0 || $status != -1 || $salesmemberid != 0 || $salespersonid != 0){
            if($channelid != 0){
                if(is_array($channelid)){
                    $channelid = implode(",",$channelid);
                }
                $where .= ' AND q.memberid IN (SELECT id FROM '.tbl_member.' WHERE channelid IN ('.$channelid.'))';
            }
            if($salesmemberid != 0){
                $where .= ' AND q.memberid='.$salesmemberid;
            }
            if($status != -1){
                $where .= ' AND q.status='.$status;
            }
            if(!empty($salespersonid)){
                $where .= ' AND q.salespersonid='.$salespersonid;
            }
			$this->readdb->where("(q.quotationdate BETWEEN '".$startdate."' AND '".$enddate."')".$where);
		}else{
			$this->readdb->where("(q.quotationdate BETWEEN '".$startdate."' AND '".$enddate."')".$where);
		}

        if (empty($salespersonid) && isset($this->viewData['submenuvisibility']['submenuviewalldata']) && strpos($this->viewData['submenuvisibility']['submenuviewalldata'], ',' . $this->session->userdata[base_url() . 'ADMINUSERTYPE'] . ',') === false) {
            $this->readdb->where("((q.addedby=".$this->session->userdata(base_url().'ADMINID')." AND q.type=0) or q.salespersonid=".$this->session->userdata(base_url().'ADMINID').")");
        }

        $this->readdb->group_by('q.quotationid');

        $i = 0;

        foreach ($this->column_search as $item) // loop column 
        {
            if($_POST['search']['value']) // if datatable send POST for search
            {
                
                if($i===0) // first loop
                {
                    $this->readdb->group_start(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
                    $this->readdb->like($item, $_POST['search']['value']);
                }
                else
                {
                    $this->readdb->or_like($item, $_POST['search']['value']);
                }

                if(count($this->column_search) - 1 == $i) //last loop
                    $this->readdb->group_end(); //close bracket
            }
            $i++;
        }
        
        if(isset($_POST['order'])) { // here order processing
            $this->readdb->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        }else if(isset($this->_detatableorder)) {
            $order = $this->_detatableorder;
            $this->readdb->order_by(key($order), $order[key($order)]);
        }
    }
    function get_datatables() {
        $this->_get_datatables_query();
        if($_POST['length'] != -1) {
            $this->readdb->limit($_POST['length'], $_POST['start']);
            $query = $this->readdb->get();
            //echo $this->readdb->last_query(); exit;
            return $query->result();
        }
    }
    function count_all() {
        $this->_get_datatables_query();
        return $this->readdb->count_all_results();
    }

    function count_filtered() {
        $this->_get_datatables_query();
        $query = $this->readdb->get();
        return $query->num_rows();
    }


    function CheckManageContent($contentid,$id=''){

        if (isset($id) && $id != '') {
            $query = $this->readdb->query("SELECT id FROM ".$this->_table." WHERE contentid =".$contentid." AND id <> '".$id."'");
        }else{
            $query = $this->readdb->query("SELECT id FROM ".$this->_table." WHERE contentid =".$contentid);
        }
       
        if($query->num_rows()   >=  1){
            return 0;
        }
        else{
            return 1;
        }
    }
    function getQuotationDetails($quotationid,$type=''){

        $whereorder = '';

        $MEMBERID = $this->session->userdata(base_url().'MEMBERID');
        if(!is_null($MEMBERID)){
            if($type == 'sales'){
                $whereorder = " AND q.sellermemberid=".$MEMBERID;
            }else if($type == 'purchase'){
                $whereorder = " AND q.memberid=".$MEMBERID;
            }
        }
        $transactiondata = array();

        $query = $this->readdb->select("q.id,q.quotationid,q.quotationdate,q.addressid,q.shippingaddressid,
                                    q.memberid,q.sellermemberid,IFNULL((SELECT channelid FROM ".tbl_member." WHERE id=q.sellermemberid),0) as sellerchannelid,
                                    m.gstno,q.remarks,q.status,
                                    q.createddate,

                                    ma.name as membername,
                                    CONCAT(ma.address,IF(ma.town!='',CONCAT(', ',ma.town),'')) as address,
                                    ct.name as cityname,ma.postalcode as postcode,
                                    ma.mobileno,ma.email,
                                    pr.name as provincename, 
                                    cn.name as countryname, 

                                    shipper.name as shippingmembername,
                                    CONCAT(shipper.address,
                                    IF(shipper.town!='',CONCAT(', ',shipper.town),'')) as shippingaddress,
                                    shipper.mobileno as shippingmobileno,
                                    shipper.email as shippingemail,
                                    IFNULL((SELECT name FROM ".tbl_city." WHERE id=shipper.cityid),'') as shippercityname,
                                    shipper.postalcode as shipperpostcode,

                                    IFNULL((SELECT name FROM ".tbl_province." WHERE id IN (SELECT stateid FROM ".tbl_city." WHERE id=shipper.cityid)),'') as shipperprovincename,
                                   
                                    IFNULL((SELECT name FROM ".tbl_country." WHERE 
                                        id IN (SELECT countryid FROM ".tbl_province." WHERE id IN (SELECT stateid FROM ".tbl_city." WHERE id=shipper.cityid))
                                        ),'') as shippercountryname,

                                    q.cashorbankid,

                                    IFNULL((SELECT name FROM ".tbl_cashorbank." WHERE id=q.cashorbankid),'') as bankname,
                                    IFNULL((SELECT branchname FROM ".tbl_cashorbank." WHERE id=q.cashorbankid),'') as branchname,
                                    IFNULL((SELECT accountno FROM ".tbl_cashorbank." WHERE id=q.cashorbankid),'') as bankaccountnumber,
                                    IFNULL((SELECT ifsccode FROM ".tbl_cashorbank." WHERE id=q.cashorbankid),'') as ifsccode,
                                    IFNULL((SELECT micrcode FROM ".tbl_cashorbank." WHERE id=q.cashorbankid),'') as micrcode,

                                    IF(pr.id=12 OR pr.name='gujarat',1,0) as igst,
                                    q.payableamount as payableamount,
                                    q.paymenttype,
                                    q.discountamount,q.globaldiscount,
                                    IF((SELECT count(qp.id) FROM ".tbl_quotationproducts." as qp WHERE qp.quotationid=q.id AND qp.discount>0)>0,1,0) as displaydiscountcolumn,
                                    q.gstprice
                                ")
                            ->from($this->_table." as q")
                            ->join(tbl_member." as m","m.id=q.memberid","LEFT") 
                            ->join(tbl_memberaddress." as ma","ma.id=q.addressid","LEFT")
                            ->join(tbl_memberaddress." as shipper","shipper.id=q.shippingaddressid","LEFT")
                            /*->join(tbl_invoice." as in","in.quotationid=po.quotationid","LEFT")*/
                            ->join(tbl_city." as ct","ct.id=ma.cityid","LEFT")
                            ->join(tbl_province." as pr","pr.id=ct.stateid","LEFT")
                            ->join(tbl_country." as cn","cn.id=pr.countryid","LEFT")
                            ->where("q.id=".$quotationid."".$whereorder)
                            ->get();
        $rowdata =  $query->row_array();
       
        if($query->num_rows() == 0){
            redirect('Pagenotfound');
        }

        $address = ucwords($rowdata['address']).",<br>".ucwords($rowdata['cityname'])." - ".$rowdata['postcode'].", ".ucwords($rowdata['provincename']).", ".ucwords($rowdata['countryname']).".";

        $shippingaddress = ucwords($rowdata['shippingaddress']).",<br>".ucwords($rowdata['shippercityname'])." - ".$rowdata['shipperpostcode'].", ".ucwords($rowdata['shipperprovincename']).", ".ucwords($rowdata['shippercountryname']).".";
        
        $transactiondata['transactiondetail'] = array("id"=>$rowdata['id'],
                                            "quotationid"=>$rowdata['quotationid'],
                                            "billingaddressid"=>$rowdata['addressid'],
                                            "shippingaddressid"=>$rowdata['shippingaddressid'],
                                            "quotationdate"=>$this->general_model->displaydate($rowdata['quotationdate']),
                                            "createddate"=>$this->general_model->displaydate($rowdata['createddate']),
                                            "membername"=>ucwords($rowdata['membername']),
                                            "memberid"=>$rowdata['memberid'],
                                            "sellermemberid"=>$rowdata['sellermemberid'],
                                            "sellerchannelid"=>$rowdata['sellerchannelid'],
                                            "mobileno"=>$rowdata['mobileno'],
                                            "email"=>$rowdata['email'],
                                            "gstno"=>$rowdata['gstno'],
                                            "cashorbankid"=>$rowdata['cashorbankid'],
                                            "bankname"=>$rowdata['bankname'],
                                            "branchname"=>$rowdata['branchname'],
                                            "bankaccountnumber"=>$rowdata['bankaccountnumber'],
                                            "ifsccode"=>$rowdata['ifsccode'],
                                            "micrcode"=>$rowdata['micrcode'],
                                            "payableamount"=>$rowdata['payableamount'],
                                            "status"=>$rowdata['status'],
                                            "address"=>$address,
                                            "billingaddress"=>$address,
                                            "igst"=>$rowdata['igst'],
                                            "paymenttype"=>$rowdata['paymenttype'],
                                            "discountamount"=>$rowdata['discountamount'],
                                            "globaldiscount"=>$rowdata['globaldiscount'],
                                            "displaydiscountcolumn"=>$rowdata['displaydiscountcolumn'],
                                            "shippingmembername"=>$rowdata['shippingmembername'],
                                            "shippingaddress"=>$shippingaddress,
                                            "shippingmobileno"=>$rowdata['shippingmobileno'],
                                            "shippingemail"=>$rowdata['shippingemail'],
                                            "remarks"=>$rowdata['remarks'],
                                            "gstprice"=>$rowdata['gstprice'],
                                            );

        $query = $this->readdb->select("CONCAT(o.name,' ',IFNULL((SELECT CONCAT('[',GROUP_CONCAT(variantvalue),']') FROM ".tbl_quotationvariant." WHERE quotationproductid=o.id),''),' | ',(SELECT name FROM ".tbl_productcategory." WHERE id=p.categoryid),IFNULL((SELECT CONCAT(' (',name,')') FROM ".tbl_brand." WHERE id=p.brandid),'')) as name,o.quantity,o.price,o.originalprice,o.tax,o.hsncode,o.discount,
        IFNULL((select filename from ".tbl_productimage." where productid=p.id limit 1),'') as productimage")
                            ->from(tbl_quotationproducts." as o")
                            ->join(tbl_product." as p","p.id=o.productid","LEFT")
                            ->where("o.quotationid=".$quotationid)
                            ->get();
        $transactiondata['transactionproduct'] =  $query->result_array();

        $query = $this->readdb->select("ecm.extrachargesname,ecm.taxamount,ecm.amount")
                            ->from(tbl_extrachargemapping." as ecm")
                            ->where("ecm.referenceid=".$quotationid." AND ecm.type=1")
                            ->get();
        $transactiondata['extracharges'] =  $query->result_array();

        return $transactiondata;
    }
   /*  function getShipperDetails(){
        $query = $this->readdb->select($this->_fields)
                            ->from(tbl_settings)

                            ->get();
        return $query->row_array();                 
    } */
    function getCompanyName(){
        $query = $this->readdb->select("businessname")
                            ->from(tbl_settings)
                            ->get();
        return $query->row_array();                 
    }

    function getQuotationDataByIdForOrder($id,$type=''){

        $whereorder = '';

        $MEMBERID = $this->session->userdata(base_url().'MEMBERID');
        if(!is_null($MEMBERID)){
            if($type == 'sales'){
                $whereorder = " AND q.sellermemberid=".$MEMBERID;
            }else if($type == 'purchase'){
                $whereorder = " AND q.memberid=".$MEMBERID;
            }
        }

        $quotationdetail['orderdetail'] = $quotationdetail['orderproduct'] = $quotationdetail['orderinstallment'] = array();
        
        $query = $this->readdb->select("q.id,q.memberid,q.sellermemberid,q.salespersonid,q.cashorbankid,q.addressid,q.shippingaddressid,q.quotationdate,q.remarks,q.quotationid as orderid,q.paymenttype,q.taxamount,q.quotationamount as amount,q.payableamount,q.discountamount,q.status,q.type,q.couponcodeamount,q.couponcode,q.globaldiscount,
        (SELECT edittaxrate FROM ".tbl_channel." WHERE id IN (SELECT channelid FROM ".tbl_member." WHERE id=q.memberid)) as memberedittaxrate")
                            ->from($this->_table." as q")
                            ->where("q.id=".$id."".$whereorder)
                            ->get();
        $rowdata =  $query->row_array();

        if($query->num_rows() == 0 || (!empty($rowdata) && $rowdata['status']!=1)){
            redirect('Pagenotfound');
        }
        
        $quotationdetail['orderdetail'] = array("id"=>$rowdata['id'],
                                            "memberid"=>$rowdata['memberid'],
                                            "sellermemberid"=>$rowdata['sellermemberid'],
                                            "addressid"=>$rowdata['addressid'],
                                            "shippingaddressid"=>$rowdata['shippingaddressid'],
                                            "orderdate"=>$rowdata['quotationdate'],
                                            "remarks"=>$rowdata['remarks'],
                                            "orderid"=>$rowdata['orderid'],
                                            "paymenttype"=>$rowdata['paymenttype'],
                                            "taxamount"=>$rowdata['taxamount'],
                                            "amount"=>$rowdata['amount'],
                                            "payableamount"=>$rowdata['payableamount'],
                                            "discountamount"=>$rowdata['discountamount'],
                                            "globaldiscount"=>$rowdata['globaldiscount'],
                                            "couponcode"=>$rowdata['couponcode'],
                                            "couponcodeamount"=>$rowdata['couponcodeamount'],
                                            "status"=>$rowdata['status'],
                                            "memberedittaxrate"=>$rowdata['memberedittaxrate'],
                                            "salespersonid"=>$rowdata['salespersonid'],
                                            "cashorbankid"=>$rowdata['cashorbankid'],
                                            );

        $query = $this->readdb->select("qp.id,qp.name,p.categoryid,qp.referencetype,qp.referenceid,'' as serialno,
                        qp.productid,
                        IF((qp.quantity-(IFNULL((SELECT SUM(op.quantity) FROM ".tbl_orderproducts." as op WHERE qp.quotationid IN (SELECT o.quotationid FROM ".tbl_orders." as o WHERE o.id=op.orderid)),0)))>0,(qp.quantity-(IFNULL((SELECT SUM(op.quantity) FROM ".tbl_orderproducts." as op WHERE qp.quotationid IN (SELECT o.quotationid FROM ".tbl_orders." as o WHERE o.id=op.orderid)),0))),1) as quantity,

                        qp.discount,qp.tax,qp.hsncode,
                        qp.finalprice,qp.price,qp.originalprice,
                        CAST((qp.price + (qp.price * qp.tax / 100)) AS DECIMAL(14,2)) as pricewithtax,
                        IF(p.isuniversal=0,(SELECT priceid FROM ".tbl_quotationvariant." WHERE quotationproductid=qp.id AND quotationid=qp.quotationid LIMIT 1),0) as priceid
                    ")
                            ->from(tbl_quotationproducts." as qp")
                            ->join(tbl_product." as p","p.id=qp.productid","LEFT")
                            ->where("qp.quotationid=".$id)
                            ->get();
        $quotationdetail['orderproduct'] =  $query->result_array();
        
        $query = $this->readdb->select("i.percentage,i.amount,i.date,
                            IF(i.paymentdate!='0000-00-00',i.paymentdate,'') as paymentdate,i.status")
                            ->from(tbl_installment." as i")
                            ->where("i.quotationid=".$id)
                            ->get();
        $quotationdetail['orderinstallment'] =  $query->result_array();
       
        return $quotationdetail;
    }

    function getQuotationDataById($id,$type=''){

        $whereorder = '';

        $MEMBERID = $this->session->userdata(base_url().'MEMBERID');
        if(!is_null($MEMBERID)){
            if($type == 'sales'){
                $whereorder = " AND q.sellermemberid=".$MEMBERID;
            }else if($type == 'purchase'){
                $whereorder = " AND q.memberid=".$MEMBERID;
            }
        }

        $quotationdetail['quotationdetail'] = $quotationdetail['quotationproduct'] = array();
        
        $query = $this->readdb->select("q.id,q.memberid,q.sellermemberid,q.addressid,q.shippingaddressid,q.quotationdate,q.remarks,q.salespersonid,
            q.quotationid,q.paymenttype,q.cashorbankid,q.taxamount,q.quotationamount,q.payableamount,q.discountamount,q.status,q.type,q.deliverypriority,q.globaldiscount,
            (SELECT edittaxrate FROM ".tbl_channel." WHERE id IN (SELECT channelid FROM ".tbl_member." WHERE id=q.memberid)) as memberedittaxrate,
            (q.payableamount + IFNULL((SELECT SUM(amount) FROM ".tbl_extrachargemapping." WHERE referenceid=q.id AND type=1),0)) as netamount,
            buyer.name as buyername,
            buyer.email as buyeremail,
            buyer.mobile as buyermobile,
            buyer.countrycode as buyercountrycode,
            buyer.secondarymobileno as buyersecondarymobileno,
            buyer.secondarycountrycode as buyersecondarycountrycode,
            buyer.isprimarywhatsappno,
            buyer.issecondarywhatsappno,

            IF(q.sellermemberid=0,u.name,seller.name) as sellername,
            IF(q.sellermemberid=0,u.email,seller.email) as selleremail,
        ")
                            ->from($this->_table." as q")
                            ->join(tbl_member." as buyer","buyer.id=q.memberid","LEFT") 
                            ->join(tbl_member." as seller","seller.id=q.sellermemberid","LEFT") 
                            ->join(tbl_user." as u","u.id=q.addedby AND q.sellermemberid=0","LEFT") 
                            ->where("q.id=".$id."".$whereorder)
                            ->get();
        $rowdata =  $query->row_array();
        
        if($query->num_rows() == 0 || (!empty($rowdata) && $rowdata['status']!=0 && $this->uri->segment(3)!="quotation-add")){
            redirect('Pagenotfound');
        }
        
        $quotationdetail['quotationdetail'] = array("id"=>$rowdata['id'],
                                            "memberid"=>$rowdata['memberid'],
                                            "buyername"=>$rowdata['buyername'],
                                            "buyeremail"=>$rowdata['buyeremail'],
                                            "sellername"=>$rowdata['sellername'],
                                            "selleremail"=>$rowdata['selleremail'],
                                            "buyercountrycode"=>$rowdata['buyercountrycode'],
                                            "buyermobile"=>$rowdata['buyermobile'],
                                            "buyersecondarycountrycode"=>$rowdata['buyersecondarycountrycode'],
                                            "buyersecondarymobileno"=>$rowdata['buyersecondarymobileno'],
                                            "isprimarywhatsappno"=>$rowdata['isprimarywhatsappno'],
                                            "issecondarywhatsappno"=>$rowdata['issecondarywhatsappno'],
                                            "sellermemberid"=>$rowdata['sellermemberid'],
                                            "addressid"=>$rowdata['addressid'],
                                            "shippingaddressid"=>$rowdata['shippingaddressid'],
                                            "netamount"=>$rowdata['netamount'],
                                            "remarks"=>$rowdata['remarks'],
                                            "cashorbankid"=>$rowdata['cashorbankid'],
                                            "quotationid"=>$rowdata['quotationid'],
                                            "quotationdate"=>$rowdata['quotationdate'],
                                            "paymenttype"=>$rowdata['paymenttype'],
                                            "taxamount"=>$rowdata['taxamount'],
                                            "quotationamount"=>$rowdata['quotationamount'],
                                            "payableamount"=>$rowdata['payableamount'],
                                            "discountamount"=>$rowdata['discountamount'],
                                            "globaldiscount"=>$rowdata['globaldiscount'],
                                            "deliverypriority"=>$rowdata['deliverypriority'],
                                            "status"=>$rowdata['status'],
                                            "salespersonid"=>$rowdata['salespersonid'],
                                            "memberedittaxrate"=>$rowdata['memberedittaxrate']
                                            );

        $query = $this->readdb->select("qp.id,qp.name,p.categoryid,qp.productid,
                            qp.quantity,qp.price,qp.discount,
                            qp.hsncode,qp.tax,qp.finalprice,qp.originalprice,
                            CAST((qp.price + (qp.price * qp.tax / 100)) AS DECIMAL(14,2)) as pricewithtax,
                            IF(p.isuniversal=0,(SELECT priceid FROM ".tbl_quotationvariant." WHERE quotationproductid=qp.id AND quotationid=qp.quotationid LIMIT 1),0) as priceid,

                            qp.referencetype,qp.referenceid,p.quantitytype,
                        
                            IF(qp.referencetype=0,
                                IFNULL((SELECT pricetype FROM ".tbl_productprices." WHERE id IN (SELECT productpricesid FROM ".tbl_productquantityprices." WHERE id=qp.referenceid) LIMIT 1),0),
                            
                                IF(qp.referencetype=1,
                                    IFNULL((SELECT pricetype FROM ".tbl_productbasicpricemapping." WHERE id IN (SELECT productbasicpricemappingid FROM ".tbl_productbasicquantityprice." WHERE id=qp.referenceid) LIMIT 1),0),
                                    IFNULL((SELECT pricetype FROM ".tbl_membervariantprices." WHERE id IN (SELECT membervariantpricesid FROM ".tbl_memberproductquantityprice." WHERE id=qp.referenceid) LIMIT 1),0)
                                )           
                            ) as pricetype,

                        ")
                    ->from(tbl_quotationproducts." as qp")
                    ->join(tbl_product." as p","p.id=qp.productid","LEFT")
                    ->where("qp.quotationid=".$id)
                    ->get();
        $quotationdetail['quotationproduct'] =  $query->result_array();
        //print_r($this->readdb->last_query());exit;
        $query = $this->readdb->select("i.percentage,i.amount,i.date,
                            IF(i.paymentdate!='0000-00-00',i.paymentdate,'') as paymentdate,i.status")
                            ->from(tbl_installment." as i")
                            ->where("i.quotationid=".$id)
                            ->get();
        $quotationdetail['quotationinstallment'] =  $query->result_array();
       
        return $quotationdetail;
    }
    function getQuotationStatusHistory($quotationid){

        $reportingto = $this->session->userdata(base_url().'REPORTINGTO');
        $memberid = $this->session->userdata(base_url().'MEMBERID');
       
        $query = $this->readdb->select("qs.id,qs.quotationid,qs.status,qs.modifieddate,qs.type,(IF(qs.type=0,(SELECT CONCAT(name,' (','".COMPANY_CODE."',')') FROM ".tbl_user." WHERE id=qs.modifiedby),(SELECT CONCAT(name,' (',membercode,')') FROM ".tbl_member." WHERE id=qs.modifiedby))) as name,qs.modifiedby,(IF(qs.type=1,(SELECT channelid FROM ".tbl_member." WHERE id=qs.modifiedby),0)) as channelid")
                        ->from(tbl_quotationstatuschange." as qs")
                        ->where("qs.quotationid=".$quotationid)
                        ->get();    
                        
        if($query->num_rows() > 0){
            return $query->result_array();   
        }else{
            return array();
        }
    }
    function getQuotationInstallmentDataByQuotationId($quotationid){

        $query = $this->readdb->select("id,percentage,amount,date,IF(paymentdate!='0000-00-00',paymentdate,'') as paymentdate,status")
                        ->from(tbl_installment)
                        ->where("quotationid=".$quotationid)
                        ->get();
                        
        if($query->num_rows() > 0){
            return $query->result_array();   
        }else{
            return array();
        }
    }
    function getExtraChargesDataByReferenceID($referenceid){

        $query = $this->readdb->select("ecm.id,ecm.type,ecm.referenceid,ecm.extrachargesid,ecm.extrachargesname,ecm.taxamount,ecm.amount,ecm.extrachargepercentage")
                        ->from(tbl_extrachargemapping." as ecm")
                        ->where("ecm.referenceid=".$referenceid." AND ecm.type=1")
                        ->get();
                        
        if($query->num_rows() > 0){
            return $query->result_array();   
        }else{
            return array();
        }
    }

    function getQuotationHistoryDetailsOnCRM($employeeid,$status,$counter)
	{   
        $limit=10;
        if($counter < 0){ $counter=0; }

        $query = $this->readdb->select("q.id,q.quotationid as quotationnumber,
                                    q.status,q.createddate,
                                    (select count(id) from ".tbl_quotationproducts." where quotationid=q.id)as itemcount,
                                    (select sum(finalprice) from ".tbl_quotationproducts." where quotationid=q.id)as orderammount,
                                    q.quotationamount,q.discountpercentage,
                                    q.discountamount,q.taxamount,q.globaldiscount,
                                    CAST((q.payableamount + IFNULL((SELECT SUM(amount) FROM ".tbl_extrachargemapping." WHERE referenceid=q.id AND type=1),0)) AS DECIMAL(14,2)) as payableamount,q.couponcodeamount,q.addquotationtype,
                                    q.resonforrejection,
                                    IFNULL(buyer.name, '') as buyername
                                
                                ")
                            
                        ->from($this->_table." as q")
                        ->join(tbl_member." as buyer","buyer.id=q.memberid","LEFT")
                        ->join(tbl_member." as seller","seller.id=q.sellermemberid","LEFT")
                        ->where("q.salespersonid=".$employeeid." AND q.salespersonid!=0 AND q.memberid!=0 AND (q.status='".$status."' OR '".$status."'='')")
                        ->group_by('q.quotationid')
                        ->order_by('q.id DESC')
                        ->limit($limit,$counter)
                        ->get();
        //echo $this->readdb->last_query(); exit;       
        if($query->num_rows() > 0) {
            return $query->result_array();
        } else {
			return array();
        }
    }
}  