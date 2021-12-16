<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Channel extends Admin_Controller {

	public $viewData = array();
	function __construct(){
		parent::__construct();
		$this->viewData = $this->getAdminSettings('submenu','Channel');
		$this->load->model('Channel_model','Channel');
	}
	public function index() {
		$this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
		$this->viewData['title'] = "Channel";
		$this->viewData['module'] = "channel/Channel";
		
		if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(4,'Channel','View channel.');
        }
		$this->admin_headerlib->add_javascript("channel","pages/channel.js");
		$this->load->view(ADMINFOLDER.'template',$this->viewData);
		
	}
	public function listing() {
		
		$list = $this->Channel->get_datatables();
		$data = array();
		$counter = $_POST['start'];
		foreach ($list as $Channel) {
			$row = array();
			$row['DT_RowId'] = $Channel->id;
			$row[] = ++$counter;
			$row[] = $Channel->name;
			$row[] = "<span class='pull-right'>".$Channel->priority."</span>";
			$row[] = '<div style="background: '.$Channel->color.';" class="statusescolor"></div>';
			$Action='';

            if(strpos($this->viewData['submenuvisibility']['submenuedit'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){
            	$Action .= '<a class="'.edit_class.'" href="'.ADMIN_URL.'channel/channel-edit/'.$Channel->id.'" title='.edit_title.'>'.edit_text.'</a>';
			}

			if(strpos($this->viewData['submenuvisibility']['submenudelete'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){                
                $Action.='<a class="'.delete_class.'" href="javascript:void(0)" title="'.delete_title.'" onclick=deleterow('.$Channel->id.',"'.ADMIN_URL.'channel/check-channel-use","channel","'.ADMIN_URL.'channel/delete-mul-channel","channeltable") >'.delete_text.'</a>';
            }

            if(strpos($this->viewData['submenuvisibility']['submenuedit'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){
                if($Channel->status==1){
                    $Action .= '<span id="span'.$Channel->id.'"><a href="javascript:void(0)" onclick="enabledisable(0,'.$Channel->id.',\''.ADMIN_URL.'channel/channel-enable-disable\',\''.disable_title.'\',\''.disable_class.'\',\''.enable_class.'\',\''.disable_title.'\',\''.enable_title.'\',\''.disable_text.'\',\''.enable_text.'\')" class="'.disable_class.'" title="'.disable_title.'">'.stripslashes(disable_text).'</a></span>';
                }
                else{
                    $Action .='<span id="span'.$Channel->id.'"><a href="javascript:void(0)" onclick="enabledisable(1,'.$Channel->id.',\''.ADMIN_URL.'channel/channel-enable-disable\',\''.enable_title.'\',\''.disable_class.'\',\''.enable_class.'\',\''.disable_title.'\',\''.enable_title.'\',\''.disable_text.'\',\''.enable_text.'\')" class="'.enable_class.'" title="'.enable_title.'">'.stripslashes(enable_text).'</a></span>';
                }
            }
			
			$row[] = $Action;
			$row[] =  '<span style="display: none;">'.$Channel->priority.'</span><div class="checkbox">
                  <input id="deletecheck'.$Channel->id.'" onchange="singlecheck(this.id)" type="checkbox" value="'.$Channel->id.'" name="deletecheck'.$Channel->id.'" class="checkradios">
                  <label for="deletecheck'.$Channel->id.'"></label>
                </div>';
			$data[] = $row;
		}
		$output = array(
						"draw" => $_POST['draw'],
						"recordsTotal" => $this->Channel->count_all(),
						"recordsFiltered" => $this->Channel->count_filtered(),
						"data" => $data,
				);
		echo json_encode($output);
	}
	
	public function channel_add() {
		$this->checkAdminAccessModule('submenu','add',$this->viewData['submenuvisibility']);
		$this->viewData['title'] = "Add Channel";
		$this->viewData['module'] = "channel/Add_channel";

		$this->viewData['multiplememberchannel'] = $this->Channel->getLevelForRegister();
		$this->viewData['allowedchannelregistrationdata'] = $this->Channel->getChannelList();
		//$this->viewData['firstlevel'] = 0;
		$this->viewData['channelcount'] = $this->Channel->CountRecords();

		$this->admin_headerlib->add_javascript("bootstrap-toggle.min","bootstrap-toggle.min.js");
		$this->admin_headerlib->add_stylesheet("bootstrap-toggle.min","bootstrap-toggle.min.css");
		$this->admin_headerlib->add_javascript_plugins("minicolorjs","minicolor/jquery.minicolors.min.js");
		$this->admin_headerlib->add_plugin("minicolorcss","minicolor/jquery.minicolors.css");
		$this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
		$this->admin_headerlib->add_javascript("add_channel","pages/add_channel.js");
		$this->load->view(ADMINFOLDER.'template',$this->viewData);
	}

	public function channel_edit($id) {
		$this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
		$this->viewData['title'] = "Edit Channel";
		$this->viewData['module'] = "channel/Add_channel";
		$this->viewData['action'] = "1";//Edit

		//Get Channel data by id
		$this->viewData['channeldata'] = $this->Channel->getChannelDataByID($id);
		
		/* $channel = $this->Channel->getChannelIDByFirstLevel();
		if(!empty($channel)){
			$this->viewData['firstlevel'] = $channel['id']; 
		}else{
			$this->viewData['firstlevel'] = 0;
		} */
		
		$this->viewData['multiplememberchannel'] = $this->Channel->getLevelForRegister();
		$this->viewData['allowedchannelregistrationdata'] = $this->Channel->getChannelList();

		$this->admin_headerlib->add_javascript("bootstrap-toggle.min","bootstrap-toggle.min.js");
		$this->admin_headerlib->add_stylesheet("bootstrap-toggle.min","bootstrap-toggle.min.css");
		
		$this->admin_headerlib->add_javascript_plugins("minicolorjs","minicolor/jquery.minicolors.min.js");
		$this->admin_headerlib->add_plugin("minicolorcss","minicolor/jquery.minicolors.css");
		$this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
		$this->admin_headerlib->add_javascript("add_channel","pages/add_channel.js");
		$this->load->view(ADMINFOLDER.'template',$this->viewData);

	}
	public function add_channel(){
		
		$PostData = $this->input->post();	
        $createddate = $this->general_model->getCurrentDateTime();
		$addedby = $this->session->userdata(base_url().'ADMINID');
		$name = $PostData['name'];
		$priority = $PostData['priority'];
		$advancepaymentcod = $PostData['advancepaymentcod'];
		$advancepaymentpriority = $PostData['advancepaymentpriority'];
		$minimumorderamount = $PostData['minimumorderamount'];

		$this->Channel->_where = "name='".trim($name)."' OR priority=".$priority;
		$Count = $this->Channel->CountRecords();

		if($Count==0){

			$status = $PostData['status'];
			$color = $PostData['color'];
			//$memberselection = (!empty($PostData['memberselection']))?implode(",",$PostData['memberselection']):'';
			$multiplememberchannel = (!empty($PostData['multiplememberchannel']))?implode(",",$PostData['multiplememberchannel']):'';
			$allowedchannelmemberregistration = (!empty($PostData['allowedchannelmemberregistration']))?implode(",",$PostData['allowedchannelmemberregistration']):'';
			
			$quotation = (isset($PostData['quotation']))?1:0;
			$partialpayment = (isset($PostData['partialpayment']))?1:0;
			$identityproof = (isset($PostData['identityproof']))?1:0;
			$memberspecificproduct = (isset($PostData['memberspecificproduct']))?1:0;
			$discount = (isset($PostData['discount']))?1:0;
			$discountcoupon = (isset($PostData['discountcoupon']))?1:0;
			$rating = (isset($PostData['rating']))?1:0;
			$debitlimit = (isset($PostData['debitlimit']))?1:0;
			$discountpriority = (isset($PostData['discountpriority']))?1:0;
			$website = (isset($PostData['website']))?1:0;
			$mobileapplication = (isset($PostData['mobileapplication']))?1:0;
			$showupperdirectory = (isset($PostData['showupperdirectory']))?1:0;
			$multiplememberwithsamechannel = (isset($PostData['multiplememberwithsamechannel']))?1:0;
			$memberbasicsalesprice = (isset($PostData['memberbasicsalesprice']))?1:0;
			$incrementdecrementprice = (isset($PostData['incrementdecrementprice']))?1:0;
			$addorderwithoutstock = (isset($PostData['addorderwithoutstock']))?1:0;
			$edittaxrate = (isset($PostData['edittaxrate']))?1:0;
			$addmemberforrapp = (isset($PostData['addmemberforrapp']))?1:0;
			$automaticgenerateinvoice = (isset($PostData['automaticgenerateinvoice']))?1:0;
			$productlisting = (isset($PostData['productlisting']))?1:0;
			$crm = (isset($PostData['crm ']))?1:0;
			$websitetype = (isset($PostData['websitetype']))?1:0;

			$referandearn = (isset($PostData['referandearn']))?1:0;
			$rewardforrefferedby = $PostData['rewardforrefferedby'];
			$rewardfornewregister = $PostData['rewardfornewregister'];

			$conversationrate = $PostData['conversationrate'];
			$minimumpointsonredeem = $PostData['minimumpointsonredeem'];
			$minimumpointsonredeemfororder = $PostData['minimumpointsonredeemfororder'];
			$mimimumpurchaseorderamountforredeem = $PostData['mimimumpurchaseorderamountforredeem'];
			
			/* Color Setting */
			$themecolor = $PostData['themecolor'];
			$fontcolor = $PostData['fontcolor'];
			$sidebarbgcolor = $PostData['sidebarbgcolor'];
			$sidebarmenuactivecolor = $PostData['sidebarmenuactivecolor'];
			$sidebarsubmenubgcolor = $PostData['sidebarsubmenubgcolor'];
			$sidebarsubmenuactivecolor = $PostData['sidebarsubmenuactivecolor'];
			$footerbgcolor = $PostData['footerbgcolor'];
			$linkcolor=$PostData['linkcolor'];
			$tableheadercolor=$PostData['tableheadercolor'];
			
			/* Discount Setting */
			$discountonbill = $PostData['discountonbill'];
			$gstondiscount = $PostData['gstondiscount'];
			$discountonbilltype = $PostData['discountonbilltype'];
			$discountonbillminamount = $PostData['discountonbillminamount'];
			$startdate = ($PostData['startdate']!='')?$this->general_model->convertdate($PostData['startdate']):'';
			$enddate = ($PostData['enddate']!='')?$this->general_model->convertdate($PostData['enddate']):'';
			if($PostData['discountonbilltype']==0){
				$discountval = $PostData['amount'];
			}else{
				$discountval = $PostData['percentageval'];
			}
			if($discountonbill==0){ 
				$discountval = $discountonbillminamount = 0; 
				$startdate = $enddate = "";
			}

			$offermodule = (isset($PostData['offermodule']))?1:0;
			
			if(REWARDSPOINTS==1){

				$firstlevel = 0;
				$channel = $this->Channel->getChannelIDByFirstLevel();
				if(!empty($channel)){
					$firstlevel = $channel['id']; 
				}
				
				//if($firstlevel != $channelid){
					$productwisepointsforseller = (isset($PostData['productwisepointsforseller']))?1:0;
					$sellerpointsforoverallproduct = (isset($PostData['sellerpointsforoverallproduct']))?$PostData['sellerpointsforoverallproduct']:'';
					$sellerpointsforsalesorder = (isset($PostData['sellerpointsforsalesorder']))?$PostData['sellerpointsforsalesorder']:'';
				/* }else{
					$productwisepointsforseller = 0;
					$sellerpointsforoverallproduct = '';
					$sellerpointsforsalesorder = '';
				} */

				$productwisepoints = (isset($PostData['productwisepoints']))?1:0;
				$productwisepointsmultiplywithqty = (isset($PostData['productwisepointsmultiplywithqty']))?1:0;
				$productwisepointsforbuyer = (isset($PostData['productwisepointsforbuyer']))?1:0;
				
				$overallproductpoints = (isset($PostData['overallproductpoints']))?1:0;
				$buyerpointsforoverallproduct = $PostData['buyerpointsforoverallproduct'];
				$mimimumorderqtyforoverallproduct = $PostData['mimimumorderqtyforoverallproduct'];
				
				$pointsonsalesorder = (isset($PostData['pointsonsalesorder']))?1:0;
				$buyerpointsforsalesorder = $PostData['buyerpointsforsalesorder'];
				$mimimumorderamountforsalesorder = $PostData['mimimumorderamountforsalesorder'];

				$samechannelreferrermemberpointonoff = (isset($PostData['samechannelreferrermemberpointonoff']))?1:0;
				$samechannelreferrermemberpoint = $PostData['samechannelreferrermemberpoint'];
				$mimimumorderamountforsamechannelreferrer = $PostData['mimimumorderamountforsamechannelreferrer'];
			
				if($productwisepoints==0){
					$productwisepointsmultiplywithqty = $productwisepointsforseller = $productwisepointsforbuyer = 0;
				}
				if($overallproductpoints==0){
					$sellerpointsforoverallproduct = $buyerpointsforoverallproduct = $mimimumorderqtyforoverallproduct = "";
				}
				if($pointsonsalesorder==0){
					$sellerpointsforsalesorder = $buyerpointsforsalesorder = $mimimumorderamountforsalesorder = "";
				}
				if($referandearn==0){
					$rewardforrefferedby = $rewardfornewregister = "";
				}

				$insertdata = array("name"=>$name,
									"color"=>$color,
									'priority'=>$priority,
									'advancepaymentcod'=>$advancepaymentcod,
									'advancepaymentpriority'=>$advancepaymentpriority,
									'minimumorderamount'=>$minimumorderamount,
									'multiplememberchannel'=>$multiplememberchannel,
									'allowedchannelmemberregistration'=>$allowedchannelmemberregistration,

									'quotation'=>$quotation,
									'partialpayment'=>$partialpayment,
									'identityproof'=>$identityproof,
									'debitlimit'=>$debitlimit,
									'memberspecificproduct'=>$memberspecificproduct,
									'discount'=>$discount,
									'website'=>$website,
									'discountpriority'=>$discountpriority,
									'discountcoupon'=>$discountcoupon,
									'rating'=>$rating,
									'mobileapplication'=>$mobileapplication,
									'showupperdirectory'=>$showupperdirectory,
									'multiplememberwithsamechannel'=>$multiplememberwithsamechannel,
									"memberbasicsalesprice"=>$memberbasicsalesprice,
									'incrementdecrementprice'=>$incrementdecrementprice,
									'addorderwithoutstock'=>$addorderwithoutstock,
									'edittaxrate'=>$edittaxrate,
									'addmemberforrapp'=>$addmemberforrapp,
									'automaticgenerateinvoice'=>$automaticgenerateinvoice,
									'productwisepoints'=>$productwisepoints,
									'productwisepointsmultiplywithqty'=>$productwisepointsmultiplywithqty,
									'productwisepointsforseller'=>$productwisepointsforseller,
									'productwisepointsforbuyer'=>$productwisepointsforbuyer,
									'overallproductpoints'=>$overallproductpoints,
									'sellerpointsforoverallproduct'=>$sellerpointsforoverallproduct,
									'buyerpointsforoverallproduct'=>$buyerpointsforoverallproduct,
									'mimimumorderqtyforoverallproduct'=>$mimimumorderqtyforoverallproduct,
									'pointsonsalesorder'=>$pointsonsalesorder,
									'sellerpointsforsalesorder'=>$sellerpointsforsalesorder,
									'buyerpointsforsalesorder'=>$buyerpointsforsalesorder,
									'mimimumorderamountforsalesorder'=>$mimimumorderamountforsalesorder,
									'referandearn'=>$referandearn,
									"rewardforrefferedby"=>$rewardforrefferedby,
									"rewardfornewregister"=>$rewardfornewregister,
									"conversationrate"=>$conversationrate,
									"minimumpointsonredeem"=>$minimumpointsonredeem,
									"minimumpointsonredeemfororder"=>$minimumpointsonredeemfororder,
									"mimimumpurchaseorderamountforredeem"=>$mimimumpurchaseorderamountforredeem,
									"samechannelreferrermemberpointonoff"=>$samechannelreferrermemberpointonoff,
									"samechannelreferrermemberpoint"=>$samechannelreferrermemberpoint,
									"mimimumorderamountforsamechannelreferrer"=>$mimimumorderamountforsamechannelreferrer,
									"offermodule"=>$offermodule,
									"productlisting"=>$productlisting,
									"crm"=>$crm,
									"websitetype"=>$websitetype,

									"themecolor" =>$themecolor,
									"fontcolor" =>$fontcolor,
									"sidebarbgcolor" =>$sidebarbgcolor,
									"sidebarmenuactivecolor" =>$sidebarmenuactivecolor,
									"sidebarsubmenubgcolor" =>$sidebarsubmenubgcolor,
									"sidebarsubmenuactivecolor" =>$sidebarsubmenuactivecolor,
									"footerbgcolor" =>$footerbgcolor,
									"linkcolor" => $linkcolor,
									"tableheadercolor" => $tableheadercolor,

									'gstondiscount'=>$gstondiscount,
									'discountonbilltype'=>$discountonbilltype,
									'discountonbillvalue'=>$discountval,
									'discountonbill'=>$discountonbill,
									'discountonbillminamount'=>$discountonbillminamount,
									"discountonbillstartdate" => $startdate,
									"discountonbillenddate" => $enddate,
									
									"status"=>$status,
									"createddate"=>$createddate,
									"addedby"=>$addedby,
									"modifieddate"=>$createddate,
									"modifiedby"=>$addedby);
			}else{
				$insertdata = array("name"=>$name,
									"color"=>$color,
									'priority'=>$priority,
									'advancepaymentcod'=>$advancepaymentcod,
									'advancepaymentpriority'=>$advancepaymentpriority,
									'minimumorderamount'=>$minimumorderamount,
									'multiplememberchannel'=>$multiplememberchannel,
									'allowedchannelmemberregistration'=>$allowedchannelmemberregistration,

									'quotation'=>$quotation,
									'partialpayment'=>$partialpayment,
									'identityproof'=>$identityproof,
									'debitlimit'=>$debitlimit,
									'memberspecificproduct'=>$memberspecificproduct,
									'discount'=>$discount,
									'website'=>$website,
									'discountpriority'=>$discountpriority,
									'discountcoupon'=>$discountcoupon,
									'rating'=>$rating,
									'mobileapplication'=>$mobileapplication,
									'showupperdirectory'=>$showupperdirectory,
									'multiplememberwithsamechannel'=>$multiplememberwithsamechannel,
									"memberbasicsalesprice"=>$memberbasicsalesprice,
									'incrementdecrementprice'=>$incrementdecrementprice,
									'addorderwithoutstock'=>$addorderwithoutstock,
									'edittaxrate'=>$edittaxrate,
									'addmemberforrapp'=>$addmemberforrapp,
									'automaticgenerateinvoice'=>$automaticgenerateinvoice,
									"offermodule"=>$offermodule,
									"productlisting"=>$productlisting,
									"crm"=>$crm,
									"websitetype"=>$websitetype,
						
									"themecolor" =>$themecolor,
									"fontcolor" =>$fontcolor,
									"sidebarbgcolor" =>$sidebarbgcolor,
									"sidebarmenuactivecolor" =>$sidebarmenuactivecolor,
									"sidebarsubmenubgcolor" =>$sidebarsubmenubgcolor,
									"sidebarsubmenuactivecolor" =>$sidebarsubmenuactivecolor,
									"footerbgcolor" =>$footerbgcolor,
									"linkcolor" => $linkcolor,
									"tableheadercolor" => $tableheadercolor,

									'gstondiscount'=>$gstondiscount,
									'discountonbilltype'=>$discountonbilltype,
									'discountonbillvalue'=>$discountval,
									'discountonbill'=>$discountonbill,
									'discountonbillminamount'=>$discountonbillminamount,
									"discountonbillstartdate" => $startdate,
									"discountonbillenddate" => $enddate,
									
									"status"=>$status,
									"createddate"=>$createddate,
									"addedby"=>$addedby,
									"modifieddate"=>$createddate,
									"modifiedby"=>$addedby);
			}
			$insertdata=array_map('trim',$insertdata);

			$Add = $this->Channel->Add($insertdata);
			if($Add){
				if($this->viewData['submenuvisibility']['managelog'] == 1){
					$this->general_model->addActionLog(1,'Channel','Add new '.$name.' channel.');
				}
				echo 1;
			}else{
				echo 0;
			}
		}else{
			echo 2;
		}
    }
    
    public function channel_enable_disable() {
        $this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
        $PostData = $this->input->post();

        $modifieddate = $this->general_model->getCurrentDateTime();
        $updatedata = array("status" => $PostData['val'], "modifieddate" => $modifieddate, "modifiedby" => $this->session->userdata(base_url() . 'ADMINID'));
        $this->Channel->_where = array("id" => $PostData['id']);
        $this->Channel->Edit($updatedata);

		if($PostData['val']==0){	

			$this->load->model('Member_model','Member');
			$this->Member->_fields = "GROUP_CONCAT(id) as memberid";
			$this->Member->_where = array("channelid"=>$PostData['id'],"status"=>1); 
			$member = $this->Member->getRecordsById();
			
			if(!empty($member)){
				$this->load->model('Fcm_model','Fcm');
				$fcmquery = $this->Fcm->getFcmDataByMemberId($member['memberid']);                            
				
				if(!empty($fcmquery)){
					$insertData = array();
					foreach ($fcmquery as $fcmrow){ 
						$fcmarray=array();                             
						
						$type = "6";
						$msg = "Dear ".ucwords($fcmrow['membername']).", Your Account Has Been Rejected.";   
						$memberid  = $fcmrow['memberid'];
						$pushMessage = '{"type":"'.$type.'", "message":"'.$msg.'","id":"'.$memberid.'"}';
						$fcmarray[] = $fcmrow['fcm'];
				
						//$this->Fcm->sendPushNotificationToFCM($fcmarray,$pushMessage);                         
						$this->Fcm->sendFcmNotification($type,$pushMessage,$memberid,$fcmarray,0,$fcmrow['devicetype']);
						
						$insertData[] = array(
							'type'=>$type,
							'message' => $pushMessage,
							'memberid'=>$memberid, 
							'isread'=>0,                      
							'createddate' => $modifieddate,               
							'addedby'=>$this->session->userdata(base_url() . 'ADMINID')
							);

					}                    
					if(!empty($insertData)){
						$this->load->model('Notification_model','Notification');
						$this->Notification->_table = tbl_notification;
						$this->Notification->add_batch($insertData);
					}
				}
			}
		}

		if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->Channel->_where = array("id"=>$PostData['id']);
            $data = $this->Channel->getRecordsById();
            $msg = ($PostData['val']==0?"Disable":"Enable").' '.$data['name'].' channel.';
            
            $this->general_model->addActionLog(2,'Channel', $msg);
        }
        echo $PostData['id'];
    }

	public function update_channel(){
		$PostData = $this->input->post();
		$modifieddate = $this->general_model->getCurrentDateTime();
		$modifiedby = $this->session->userdata(base_url().'ADMINID');
		$channelid = $PostData['channelid'];
		$name = $PostData['name'];
		$priority = $PostData['priority'];
		$advancepaymentcod = $PostData['advancepaymentcod'];
		$advancepaymentpriority = $PostData['advancepaymentpriority'];
		$minimumorderamount = $PostData['minimumorderamount'];

		$this->Channel->_where = "id!=".$channelid." AND (name='".trim($name)."' OR priority=".$priority.")";
		$Count = $this->Channel->CountRecords();
		
		if($Count==0){
			
			$status = $PostData['status'];
			$color = $PostData['color'];
			//$memberselection = (!empty($PostData['memberselection']))?implode(",",$PostData['memberselection']):'';
			$multiplememberchannel = (!empty($PostData['multiplememberchannel']))?implode(",",$PostData['multiplememberchannel']):'';
			$allowedchannelmemberregistration = (!empty($PostData['allowedchannelmemberregistration']))?implode(",",$PostData['allowedchannelmemberregistration']):'';
			
			$quotation = (isset($PostData['quotation']))?1:0;
			$partialpayment = (isset($PostData['partialpayment']))?1:0;
			$identityproof = (isset($PostData['identityproof']))?1:0;
			$memberspecificproduct = (isset($PostData['memberspecificproduct']))?1:0;
			$discount = (isset($PostData['discount']))?1:0;
			$discountcoupon = (isset($PostData['discountcoupon']))?1:0;
			$rating = (isset($PostData['rating']))?1:0;
			$debitlimit = (isset($PostData['debitlimit']))?1:0;
			$discountpriority = (isset($PostData['discountpriority']))?1:0;
			$website = (isset($PostData['website']))?1:0;
			$mobileapplication = (isset($PostData['mobileapplication']))?1:0;
			$showupperdirectory = (isset($PostData['showupperdirectory']))?1:0;
			$multiplememberwithsamechannel = (isset($PostData['multiplememberwithsamechannel']))?1:0;
			$memberbasicsalesprice = (isset($PostData['memberbasicsalesprice']))?1:0;
			$incrementdecrementprice = (isset($PostData['incrementdecrementprice']))?1:0;
			$addorderwithoutstock = (isset($PostData['addorderwithoutstock']))?1:0;
			$edittaxrate = (isset($PostData['edittaxrate']))?1:0;
			$addmemberforrapp = (isset($PostData['addmemberforrapp']))?1:0;
			$automaticgenerateinvoice = (isset($PostData['automaticgenerateinvoice']))?1:0;
			$productlisting = (isset($PostData['productlisting']))?1:0;
			$crm = (isset($PostData['crm']))?1:0;
			$websitetype = (isset($PostData['websitetype']))?1:0;
			$offermodule = (isset($PostData['offermodule']))?1:0;
			
			/* Color Setting */
			$themecolor = $PostData['themecolor'];
			$fontcolor = $PostData['fontcolor'];
			$sidebarbgcolor = $PostData['sidebarbgcolor'];
			$sidebarmenuactivecolor = $PostData['sidebarmenuactivecolor'];
			$sidebarsubmenubgcolor = $PostData['sidebarsubmenubgcolor'];
			$sidebarsubmenuactivecolor = $PostData['sidebarsubmenuactivecolor'];
			$footerbgcolor = $PostData['footerbgcolor'];
			$linkcolor=$PostData['linkcolor'];
			$tableheadercolor=$PostData['tableheadercolor'];
			
			/* Discount Setting */
			$discountonbill = $PostData['discountonbill'];
			$gstondiscount = $PostData['gstondiscount'];
			$discountonbilltype = $PostData['discountonbilltype'];
			$discountonbillminamount = $PostData['discountonbillminamount'];
			$startdate = ($PostData['startdate']!='')?$this->general_model->convertdate($PostData['startdate']):'';
			$enddate = ($PostData['enddate']!='')?$this->general_model->convertdate($PostData['enddate']):'';
			if($PostData['discountonbilltype']==0){
				$discountval = $PostData['amount'];
			}else{
				$discountval = $PostData['percentageval'];
			}
			if($discountonbill==0){ 
				$discountval = $discountonbillminamount = 0; 
				$startdate = $enddate = "";
			}

			if(REWARDSPOINTS==1){
				
				$firstlevel = 0;
				$channel = $this->Channel->getChannelIDByFirstLevel();
				if(!empty($channel)){
					$firstlevel = $channel['id']; 
				}
				
				//if($firstlevel != $channelid){
					$productwisepointsforseller = (isset($PostData['productwisepointsforseller']))?1:0;
					$sellerpointsforoverallproduct = $PostData['sellerpointsforoverallproduct'];
					$sellerpointsforsalesorder = $PostData['sellerpointsforsalesorder'];
				/* }else{
					$productwisepointsforseller = 0;
					$sellerpointsforoverallproduct = '';
					$sellerpointsforsalesorder = '';
				} */

				$productwisepoints = (isset($PostData['productwisepoints']))?1:0;
				$productwisepointsmultiplywithqty = (isset($PostData['productwisepointsmultiplywithqty']))?1:0;
				$productwisepointsforbuyer = (isset($PostData['productwisepointsforbuyer']))?1:0;
				
				$overallproductpoints = (isset($PostData['overallproductpoints']))?1:0;
				$buyerpointsforoverallproduct = $PostData['buyerpointsforoverallproduct'];
				$mimimumorderqtyforoverallproduct = $PostData['mimimumorderqtyforoverallproduct'];
				
				$pointsonsalesorder = (isset($PostData['pointsonsalesorder']))?1:0;
				$buyerpointsforsalesorder = $PostData['buyerpointsforsalesorder'];
				$mimimumorderamountforsalesorder = $PostData['mimimumorderamountforsalesorder'];
				
				$referandearn = (isset($PostData['referandearn']))?1:0;
				$rewardforrefferedby = $PostData['rewardforrefferedby'];
				$rewardfornewregister = $PostData['rewardfornewregister'];
	
				$conversationrate = $PostData['conversationrate'];
				$minimumpointsonredeem = $PostData['minimumpointsonredeem'];
				$minimumpointsonredeemfororder = $PostData['minimumpointsonredeemfororder'];
				$mimimumpurchaseorderamountforredeem = $PostData['mimimumpurchaseorderamountforredeem'];

				$samechannelreferrermemberpointonoff = (isset($PostData['samechannelreferrermemberpointonoff']))?1:0;
				$samechannelreferrermemberpoint = $PostData['samechannelreferrermemberpoint'];
				$mimimumorderamountforsamechannelreferrer = $PostData['mimimumorderamountforsamechannelreferrer'];
				
				if($productwisepoints==0){
					$productwisepointsmultiplywithqty = $productwisepointsforseller = $productwisepointsforbuyer = 0;
				}
				if($overallproductpoints==0){
					$sellerpointsforoverallproduct = $buyerpointsforoverallproduct = $mimimumorderqtyforoverallproduct = "";
				}
				if($pointsonsalesorder==0){
					$sellerpointsforsalesorder = $buyerpointsforsalesorder = $mimimumorderamountforsalesorder = "";
				}
				if($referandearn==0){
					$rewardforrefferedby = $rewardfornewregister = "";
				}

				$updatedata = array("name"=>$name,
									"color"=>$color,
									'priority'=>$priority,
									'advancepaymentcod'=>$advancepaymentcod,
									'advancepaymentpriority'=>$advancepaymentpriority,
									'minimumorderamount'=>$minimumorderamount,
									'multiplememberchannel'=>$multiplememberchannel,
									'allowedchannelmemberregistration'=>$allowedchannelmemberregistration,

									'quotation'=>$quotation,
									'partialpayment'=>$partialpayment,
									'identityproof'=>$identityproof,
									'debitlimit'=>$debitlimit,
									'memberspecificproduct'=>$memberspecificproduct,
									'discount'=>$discount,
									'website'=>$website,
									'discountpriority'=>$discountpriority,
									'discountcoupon'=>$discountcoupon,
									'rating'=>$rating,
									'mobileapplication'=>$mobileapplication,
									'showupperdirectory'=>$showupperdirectory,
									'multiplememberwithsamechannel'=>$multiplememberwithsamechannel,
									'addorderwithoutstock'=>$addorderwithoutstock,
									'edittaxrate'=>$edittaxrate,
									'addmemberforrapp'=>$addmemberforrapp,
									'automaticgenerateinvoice'=>$automaticgenerateinvoice,
									"productlisting"=>$productlisting,
									"crm"=>$crm,
									"websitetype"=>$websitetype,
									"memberbasicsalesprice"=>$memberbasicsalesprice,
									'incrementdecrementprice'=>$incrementdecrementprice,
									'productwisepoints'=>$productwisepoints,
									'productwisepointsmultiplywithqty'=>$productwisepointsmultiplywithqty,
									'productwisepointsforseller'=>$productwisepointsforseller,
									'productwisepointsforbuyer'=>$productwisepointsforbuyer,
									'overallproductpoints'=>$overallproductpoints,
									'sellerpointsforoverallproduct'=>$sellerpointsforoverallproduct,
									'buyerpointsforoverallproduct'=>$buyerpointsforoverallproduct,
									'mimimumorderqtyforoverallproduct'=>$mimimumorderqtyforoverallproduct,
									'pointsonsalesorder'=>$pointsonsalesorder,
									'sellerpointsforsalesorder'=>$sellerpointsforsalesorder,
									'buyerpointsforsalesorder'=>$buyerpointsforsalesorder,
									'mimimumorderamountforsalesorder'=>$mimimumorderamountforsalesorder,
									'referandearn'=>$referandearn,
									"rewardforrefferedby"=>$rewardforrefferedby,
									"rewardfornewregister"=>$rewardfornewregister,
									"conversationrate"=>$conversationrate,
									"minimumpointsonredeem"=>$minimumpointsonredeem,
									"minimumpointsonredeemfororder"=>$minimumpointsonredeemfororder,
									"mimimumpurchaseorderamountforredeem"=>$mimimumpurchaseorderamountforredeem,
									"samechannelreferrermemberpointonoff"=>$samechannelreferrermemberpointonoff,
									"samechannelreferrermemberpoint"=>$samechannelreferrermemberpoint,
									"mimimumorderamountforsamechannelreferrer"=>$mimimumorderamountforsamechannelreferrer,
									"offermodule"=>$offermodule,

									"themecolor" =>$themecolor,
									"fontcolor" =>$fontcolor,
									"sidebarbgcolor" =>$sidebarbgcolor,
									"sidebarmenuactivecolor" =>$sidebarmenuactivecolor,
									"sidebarsubmenubgcolor" =>$sidebarsubmenubgcolor,
									"sidebarsubmenuactivecolor" =>$sidebarsubmenuactivecolor,
									"footerbgcolor" =>$footerbgcolor,
									"linkcolor" => $linkcolor,
									"tableheadercolor" => $tableheadercolor,

									'gstondiscount'=>$gstondiscount,
									'discountonbilltype'=>$discountonbilltype,
									'discountonbillvalue'=>$discountval,
									'discountonbill'=>$discountonbill,
									'discountonbillminamount'=>$discountonbillminamount,
									"discountonbillstartdate" => $startdate,
									"discountonbillenddate" => $enddate,
									
									"status"=>$status,
									"modifieddate"=>$modifieddate,
									"modifiedby"=>$modifiedby
								);
			}else{
				$updatedata = array("name"=>$name,
									"color"=>$color,
									'priority'=>$priority,
									'advancepaymentcod'=>$advancepaymentcod,
									'advancepaymentpriority'=>$advancepaymentpriority,
									'minimumorderamount'=>$minimumorderamount,
									'multiplememberchannel'=>$multiplememberchannel,
									'allowedchannelmemberregistration'=>$allowedchannelmemberregistration,

									'quotation'=>$quotation,
									'partialpayment'=>$partialpayment,
									'identityproof'=>$identityproof,
									'debitlimit'=>$debitlimit,
									'memberspecificproduct'=>$memberspecificproduct,
									'discount'=>$discount,
									'website'=>$website,
									'discountpriority'=>$discountpriority,
									'discountcoupon'=>$discountcoupon,
									'rating'=>$rating,
									'mobileapplication'=>$mobileapplication,
									'showupperdirectory'=>$showupperdirectory,
									'multiplememberwithsamechannel'=>$multiplememberwithsamechannel,
									'addorderwithoutstock'=>$addorderwithoutstock,
									'edittaxrate'=>$edittaxrate,
									'addmemberforrapp'=>$addmemberforrapp,
									'automaticgenerateinvoice'=>$automaticgenerateinvoice,
									"productlisting"=>$productlisting,
									"crm"=>$crm,
									"websitetype"=>$websitetype,
									"memberbasicsalesprice"=>$memberbasicsalesprice,
									'incrementdecrementprice'=>$incrementdecrementprice,
									"offermodule"=>$offermodule,

									"themecolor" =>$themecolor,
									"fontcolor" =>$fontcolor,
									"sidebarbgcolor" =>$sidebarbgcolor,
									"sidebarmenuactivecolor" =>$sidebarmenuactivecolor,
									"sidebarsubmenubgcolor" =>$sidebarsubmenubgcolor,
									"sidebarsubmenuactivecolor" =>$sidebarsubmenuactivecolor,
									"footerbgcolor" =>$footerbgcolor,
									"linkcolor" => $linkcolor,
									"tableheadercolor" => $tableheadercolor,

									'discountonbill'=>$discountonbill,
									'gstondiscount'=>$gstondiscount,
									'discountonbilltype'=>$discountonbilltype,
									'discountonbillvalue'=>$discountval,
									'discountonbillminamount'=>$discountonbillminamount,
									"discountonbillstartdate" => $startdate,
									"discountonbillenddate" => $enddate,
									
									"status"=>$status,
									"modifieddate"=>$modifieddate,
									"modifiedby"=>$modifiedby
								);
			}

			$updatedata=array_map('trim',$updatedata);

			$this->Channel->_where = array("id"=>$channelid);
			$updateID = $this->Channel->Edit($updatedata);

			if($updateID){
				if($mobileapplication==0 || $status==0){	

					$this->load->model('Member_model','Member');
					$this->Member->_fields = "GROUP_CONCAT(id) as memberid";
					$this->Member->_where = array("channelid"=>$channelid,"status"=>1); 
					$member = $this->Member->getRecordsById();
					
					if(!empty($member)){
						$this->load->model('Fcm_model','Fcm');
						$fcmquery = $this->Fcm->getFcmDataByMemberId($member['memberid']);                            
						
						if(!empty($fcmquery)){
							$insertData = array();
							foreach ($fcmquery as $fcmrow){ 
								$fcmarray=array();                             
								
								$type = "6";
								$msg = "Dear ".ucwords($fcmrow['membername']).", Your Account Has Been Rejected.";   
								$memberid  = $fcmrow['memberid'];
								$pushMessage = '{"type":"'.$type.'", "message":"'.$msg.'","id":"'.$memberid.'"}';
								$fcmarray[] = $fcmrow['fcm'];
						
								//$this->Fcm->sendPushNotificationToFCM($fcmarray,$pushMessage);                         
								$this->Fcm->sendFcmNotification($type,$pushMessage,$memberid,$fcmarray,0,$fcmrow['devicetype']);
								
								$insertData[] = array(
									'type'=>$type,
									'message' => $pushMessage,
									'memberid'=>$memberid, 
									'isread'=>0,                      
									'createddate' => $modifieddate,               
									'addedby'=>$modifiedby
									);

							}                    
							if(!empty($insertData)){
								$this->load->model('Notification_model','Notification');
								$this->Notification->_table = tbl_notification;
								$this->Notification->add_batch($insertData);
							}
						}
					}
				}

				if($this->viewData['submenuvisibility']['managelog'] == 1){
					$this->general_model->addActionLog(2,'Channel','Edit '.$name.' channel.');
				}
			}

			echo 1;
		}else{
			echo 2;
		}
	}

	public function check_channel_use()
    {
	   $PostData = $this->input->post();
	   
	   $this->load->model('Member_model','Member');
         $count = 0;
	  	 $ids = explode(",",$PostData['ids']);
	     foreach($ids as $row){

			$this->Member->_where = array("channelid"=>$row);
			$Count = $this->Member->CountRecords();

	        if($Count > 0){
	          $count++;
	        }
	      }
      echo $count;
    }

    public function delete_mul_channel(){
	    $PostData = $this->input->post();
	    $ids = explode(",",$PostData['ids']);

	    foreach($ids as $row){

			if($this->viewData['submenuvisibility']['managelog'] == 1){

				$this->Channel->_where = array("id"=>$row);
				$data = $this->Channel->getRecordsById();
				$this->general_model->addActionLog(3,'Channel','Delete '.$data['name'].' channel.');
			}
			$this->Channel->Delete(array('id'=>$row));
	    }
	}

	public function update_priority(){

		$PostData = $this->input->post();
		// print_r($PostData);exit;
        $sequenceno = $PostData['sequencearray'];
        $updatedata = array();

        for($i = 0; $i < count($sequenceno); $i++){
            $updatedata[] = array(
                'priority'=>$sequenceno[$i]['sequenceno'],
                'id' => $sequenceno[$i]['id']
            );
        }
		// print_r($updatedata);exit;
        if(!empty($updatedata)){
            $this->Channel->edit_batch($updatedata, 'id');
        }

        echo 1;
	}

}