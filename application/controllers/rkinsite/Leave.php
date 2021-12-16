<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Leave extends Admin_Controller
{

	public $viewData = array();
	function __construct()
	{
		parent::__construct();
		$this->viewData = $this->getAdminSettings('submenu', 'leave');
		$this->load->model('Leave_model', 'Leave');
		$this->load->model('User_model', 'User');
	}

	public function index()
	{
		$this->checkAdminAccessModule('submenu', 'view', $this->viewData['submenuvisibility']);
		$this->viewData['title'] = "Leave";
		$this->viewData['module'] = "leave/Leave";

		$this->load->model('User_model', 'User');
		
		$this->viewData['employee_data'] = $this->Leave->getUser();
    // pre($this->viewData['employee_data']);
		//$this->viewData['employee_data'] = $this->User->getUserListDataByHierarchy();

		$this->admin_headerlib->add_javascript("leave", "pages/leave.js");
		$this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker", "bootstrap-datepicker/bootstrap-datepicker.js");
		$this->admin_headerlib->add_plugin("bootstrap-datepicker", "bootstrap-datetimepicker/bootstrap-datetimepicker.css");
		$this->load->view(ADMINFOLDER . 'template', $this->viewData);
	}

	public function listing()
	{
		$PostData = $this->input->post();
		$sessiondata = array();
		$sessiondata = $this->session->userdata;

		if (isset($PostData['fromdate'])) {
			if (isset($sessiondata["fromdatefilter"])) {
				if ($sessiondata["fromdatefilter"] != $PostData['fromdate']) {
					$sessiondata["fromdatefilter"] = $PostData['fromdate'];
				}
			} else {
				$sessiondata["fromdatefilter"] = $PostData['fromdate'];
			}
		}

		if (isset($PostData['todate'])) {
			if (isset($sessiondata["todatefilter"])) {
				if ($sessiondata["todatefilter"] != $PostData['todate']) {
					$sessiondata["todatefilter"] = $PostData['todate'];
				}
			} else {
				$sessiondata["todatefilter"] = $PostData['todate'];
			}
		}

		if (isset($PostData['filteremployee'])) {
			if (isset($sessiondata["employeefilter"])) {
				if ($sessiondata["employeefilter"] != $PostData['filteremployee']) {
					$sessiondata["employeefilter"] = $PostData['filteremployee'];
				}
			} else {
				$sessiondata["employeefilter"] = $PostData['filteremployee'];
			}
		}

		if (isset($PostData['filterstatus'])) {
			if (isset($sessiondata["statusfilter"])) {
				if ($sessiondata["statusfilter"] != $PostData['filterstatus']) {
					$sessiondata["statusfilter"] = $PostData['filterstatus'];
				}
			} else {
				$sessiondata["statusfilter"] = $PostData['filterstatus'];
			}
		}

		if (!empty($sessiondata)) {
			$this->session->set_userdata($sessiondata);
		}

		$loginid = $this->session->userdata(base_url() . 'ADMINID');
		$list = $this->Leave->get_datatables();
		// print_r($list);exit;
		$data = array();
		$counter = $_POST['start'];
		foreach ($list as $key => $Leave) {
			$row = array();

			$paidunpaidhistoryarr = array();
			$this->Leave->_table = tbl_leavepaidunpaidhistory;
			$this->Leave->_where = ["leaveid"=>$Leave['id']];
			$paidunpaidhistoryarr = $this->Leave->get_many_by();
			$paidunpaidhistorycontent = "<table class='table table-striped table-bordered table-responsive' style='width:45%;'><thead><tr><th>Sno</th><th>Date</th><th>Old</th><th>New</th><th>Reason</th></tr></thead><tbody>";
			$paidunpaidcount = 1;
			foreach($paidunpaidhistoryarr as $row){
				$leavedate = $this->general_model->convertdatetime($row['createdate']);
				$leavefrom = ($row['leavefrom'] == 1)?"Paid":"Unpaid";
				$leaveto = ($row['leaveto'] == 1)?"Paid":"Unpaid";
				$reason = trim($row['reason']);
				$paidunpaidhistorycontent .= "<tr><th>$paidunpaidcount</th><td ><div style=' max-width:150%;'>$leavedate</div></td><td>$leavefrom</td><td>$leaveto</td><td><div style=' max-width:55px; word-wrap: break-word;'>$reason</div></td></tr>";
				$paidunpaidcount++;
			}
			$paidunpaidhistorycontent .= "</tbody></table>";
			$this->Leave->_table = tbl_leave;

			$row[] = ++$counter;
			$row[] = $this->general_model->displaydate($Leave['createddate']);
			$row[] =  $Leave['name'];

			if ($Leave['leavetype'] == 1) {

				if ($Leave['leavetype'] == 1 && $Leave['fromdate'] != "0000-00-00" && $Leave['todate'] != "0000-00-00") {
					if ($Leave['fromdate'] != $Leave['todate']) {
						$date1 = strtotime($Leave['fromdate']);
						$date2 = strtotime($Leave['todate']);
						$datediff = $date2 - $date1;
						$diff = floor($datediff / (60 * 60 * 24));
						$diff = ($diff + 1) . " " . "days";
					} else {
						$diff = "1 day";
					}
				}
			} else {
				$diff = $Leave['halfleave'] . " " . "half";
			}

			$row[] = $diff;

			$row[] = $this->general_model->displaydate($Leave['fromdate']);

			if ($Leave['todate'] == "0000-00-00") {
				$row[] = "-";
			} else {
				$row[] = $this->general_model->displaydate($Leave['todate']);
			}
			$row[] = $Leave['reason'];

			if($Leave['paidunpaid'] == 1){
				if(in_array($this->session->userdata[base_url() . 'ADMINUSERTYPE'], [1,2])){
					$paidunpaid = '<button class="popoverButton btn btn-info btn-raised" title="Paid/Unpaid History" data-toggle="popover" data-trigger="hover" onclick="loadpaidunpaidmodal('.$Leave['id'].','.$Leave['paidunpaid'].')" data-content="'.$paidunpaidhistorycontent.'" style="cursor:pointer;text-decoration: underline;">Paid</button><button class="popoverButton btn btn-info btn-raised" title="Paid/Unpaid History" data-toggle="popover" data-trigger="hover" onclick="loadpaidunpaidmodal('.$Leave['id'].','.$Leave['paidunpaid'].')" data-content="'.$paidunpaidhistorycontent.'" style="display:none;">Paid</button>';
				}
				else{
					$paidunpaid = '<button class="btn btn-info btn-raised" style="cursor:pointer;">Paid</button>';
				}
			}
			else{
				if(in_array($this->session->userdata[base_url() . 'ADMINUSERTYPE'], [1,2])){
					$paidunpaid = '<button class="popoverButton btn btn-info btn-raised" title="Paid/Unpaid History" data-toggle="popover" data-trigger="hover" onclick="loadpaidunpaidmodal('.$Leave['id'].','.$Leave['paidunpaid'].')" data-content="'.$paidunpaidhistorycontent.'" style="cursor:pointer;text-decoration: underline;">Unpaid</button><button class="popoverButton btn btn-info btn-raised" title="Paid/Unpaid History" data-toggle="popover" data-trigger="hover" onclick="loadpaidunpaidmodal('.$Leave['id'].','.$Leave['paidunpaid'].')" data-content="'.$paidunpaidhistorycontent.'" style="display:none;">Unpaid</button>';
				}
				else{
					$paidunpaid = '<button class="btn btn-info btn-raised" style="cursor:pointer;">Unpaid</button>';
				}
			}
			$row[] = $paidunpaid;
			
			$buttons = "<div class='text-center' style='width:151px;' >";
			if (strpos($this->viewData['submenuvisibility']['submenuedit'], ',' . $this->session->userdata[base_url() . 'ADMINUSERTYPE'] . ',') !== false) {
				if ($Leave['status'] != 3) {
					if ($Leave['granted'] == "true") {
						$buttons .= '<button  style="padding:4px 11px;border-radius:5px;" class="btn btn-success btn-raised">Approved</button>';
					} else if ($Leave['granted'] == "false") {
						$buttons .= '<button  style="padding:4px 11px;border-radius:5px;" class="btn btn-danger btn-raised">Decline</button>';
					} else {
						if ($loginid != $Leave['employeeid']) {
							$buttons .= '<button   class="btn btn-success btn-raised"  onclick="changestatus(true,' . $Leave['id'] . ',' . $Leave['employeeid'] . ');">Approve</button>';
							$buttons .= '<button  class="btn btn-danger btn-raised"  onclick="changestatus(false,' . $Leave['id'] . ',' . $Leave['employeeid'] . ');">Decline</button>';
						} else {
							$buttons .= '<button class="btn btn-warning btn-raised ">Pending </button>';
						}
					}
				} else if ($Leave['status'] == 3 && $Leave['granted'] == "") {
					$buttons .= '<button class="btn btn-warning btn-raised ">Pending </button>';
				} else {
					$buttons .= "-";
				}
			}
			$buttons .= "</div>";
			$row[] = $buttons;

			$Action = '<div class="text-center" style="width: 81px;">';

			if ($Leave['granted'] != "true" && $Leave['status'] == 0) {
				if (strpos($this->viewData['submenuvisibility']['submenuedit'], ',' . $this->session->userdata[base_url() . 'ADMINUSERTYPE'] . ',') !== false) {
					$Action .= ' <a class="' . edit_class . '" href="' . ADMIN_URL . 'leave/leaveedit/' . $Leave['id'] . '" title=' . edit_title . '>' . edit_text . '</a>';
					//if leave status is pendding and ADMINUSERTYPE == 5 then show delete button
					if ($Leave['status'] == 0 && $this->session->userdata[base_url() . 'ADMINUSERTYPE'] == 5) {
						$Action .= ' <a class="' . delete_class . '" href="javascript:void(0)" title="' . delete_title . '" onclick=deleterow(' . $Leave['id'] . ',"' . ADMIN_URL . 'leave/checkleaveuse","leave","' . ADMIN_URL . 'leave/deletemulleave") >' . delete_text . '</a>';
					}
				}
			}
			if ($Leave['fromdate'] >= date("Y-m-d")) {
				if ($Leave['status'] != 1 && $Leave['granted'] != "true") {
					if (strpos($this->viewData['submenuvisibility']['submenudelete'], ',' . $this->session->userdata[base_url() . 'ADMINUSERTYPE'] . ',') !== false) {
						$Action .= ' <a class="' . delete_class . '" href="javascript:void(0)" title="' . delete_title . '" onclick=deleterow(' . $Leave['id'] . ',"' . ADMIN_URL . 'leave/checkleaveuse","leave","' . ADMIN_URL . 'leave/deletemulleave") >' . delete_text . '</a>';
					}
				}
			}
			$Action .= '</div>';


			$row[] = $Action;

			$multidelete = '';
			if ($Leave['fromdate'] >= date("Y-m-d")) {
				if ($Leave['status'] != 1 && $Leave['granted'] != "true") {
					$multidelete = '<div class="checkbox table-checkbox">
							<input id="deletecheck' . $Leave['id'] . '" onchange="singlecheck(this.id)" type="checkbox" value="' . $Leave['id'] . '" name="deletecheck' . $Leave['id'] . '" class="checkradios">
							<label for="deletecheck' . $Leave['id'] . '"></label>
							</div>';
				}
			}
			$row[] = $multidelete;

			$data[] = $row;
		}
		$output = array(
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->Leave->count_all(),
			"recordsFiltered" => $this->Leave->count_filtered(),
			"data" => $data,
		);
		echo json_encode($output);
	}

	public function leaveadd()
	{
		$this->checkAdminAccessModule('submenu', 'add', $this->viewData['submenuvisibility']);
		$this->viewData['title'] = "Add Leave";
		$this->viewData['module'] = "leave/Addleave";
		
		
		// $this->admin_headerlib->add_plugin("bootstrap-datepicker", "bootstrap-datepicker/bootstrap-datepicker.css");
		$this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker", "bootstrap-datepicker/bootstrap-datepicker.js");
		$this->admin_headerlib->add_javascript("addleave", "pages/addleave.js");
		$this->load->view(ADMINFOLDER . 'template', $this->viewData);
	}

	public function addleave()
	{
		$PostData = $this->input->post();
		// print_r($PostData);exit;
		$createddate = $this->general_model->getCurrentDateTime();
		$addedby = $this->session->userdata(base_url() . 'ADMINID');

		if ($PostData['leavetype'] == 1) {
			$halfleave = "0";
			$todate = $this->general_model->convertdate($PostData['todate']);
		} else {
			$halfleave = $PostData['halfleave'];
			$todate = "0000-00-00";
		}
		$this->Leave->_table = tbl_employeeleave;
		$this->Leave->_where = "employeeid='" . $addedby . "' AND fromdate='" . $this->general_model->convertdate($PostData['fromdate']) . "' AND leavetype = '" . $PostData['leavetype'] . "' AND status = 2";
		$checkdata = $this->Leave->getRecordsById();

		if (count($checkdata) == 0) {
			$insertdata = array(
				"employeeid" => $addedby,
				"fromdate" => $this->general_model->convertdate($PostData['fromdate']),
				"todate" => $todate,
				//"remarks"=>$PostData['remark'],
				"reason" => $PostData['reason'],
				"status" => 0,
				"leavetype" => $PostData['leavetype'],
				"halfleave" => $halfleave,
				"createddate" => $createddate,
				"addedby" => $addedby
			);

			$insertdata = array_map('trim', $insertdata);
			
			$Add = $this->Leave->Add($insertdata);
			if ($Add) {

				//Send mail to head employee
				$data = array();
				$date = date('Y-m-d h:i:s');

				$this->Leave->_fields = "*,(SELECT u.name FROM user AS u WHERE u.id = employeeid) AS name,(SELECT u.email FROM user AS u WHERE u.id = employeeid) AS email";
				$this->Leave->_where = array("id" => $Add);
				$leaverow = $this->Leave->getRecordsById();
				//print_r($leaverow);exit;

				$employeeemail = $leaverow['email'];
				if ($leaverow['leavetype'] == 1) {
					if ($leaverow['fromdate'] != $leaverow['todate']) {
						$date1 = strtotime($leaverow['fromdate']);
						$date2 = strtotime($leaverow['todate']);
						$datediff = $date2 - $date1;
						$diff = floor($datediff / (60 * 60 * 24));
						$diff = $diff . " " . "days";
					} else {
						$diff = "1 day";
					}
				} else {
					$diff = $leaverow['halfleave'] . " " . "half";
				}
				$fromdate = date('d/m/Y', strtotime($leaverow['fromdate']));
				if ($leaverow['todate'] == "0000-00-00") {
					$todate =  "-";
				} else {
					$todate =  date('d/m/Y', strtotime($leaverow['todate']));
				}

				$profileid = LeaveMail;

				$this->User->_fields = "email";
				$this->User->_where = "FIND_IN_SET(id,'" . $profileid . "')>0 AND status = 1 ";
				$leavemail = $this->User->getRecordById();

				$toarray = array();
				$to = "";

				foreach ($leavemail as $row2) {

					if ($employeeemail != $row2['email']) {
						$toarray[] = $row2['email'];
					}
				}

				$to = implode(',', $toarray);

				$data['myleavedata'] = $leaverow;
				$data['myleavediff'] = $diff;

				if (count($leaverow) > 0) {
					$table = $this->load->view("requestforleavetable", $data, true);

					$mailBodyArr1 = array(
						"{logo}" => '<a href="' . DOMAIN_URL . '"><img src="' . MAIN_LOGO_IMAGE_URL . COMPANY_LOGO . '" alt="' . COMPANY_NAME . '" style="border: none; display: inline; font-size: 14px; font-weight: bold; max-width: 100px !important; max-height: 100px !important; line-height: 100%; outline: none; text-decoration: none; text-transform: capitalize;"/></a>',
						//"{name}" => $leaverow['name'],
						//"{date}" => date("d-m-Y", strtotime($this->general_model->getCurrentDateTime())),
						"{detailtable}" => $table,
						"{companyemail}" => $employeeemail,
						"{companyname}" => $leaverow['name']
					);
					//$subjecttext = array("{name}"=>$leaverow['name'],"{date}" => date("d-m-Y", strtotime($this->general_model->getCurrentDateTime())));

					$subjecttext = "RK infotech - Request for leave";

					$mailid = array_search('Request For Leave', $this->Emailformattype);
					
					$emailSend = $this->Leave->sendMail($mailid, $mailBodyArr1, $subjecttext);
				}

				/*Notification*/
				$this->User->_table = (tbl_user);
				$this->User->_fields = "reportingto,name";
				$this->User->_where = 'id=' . $addedby;
				$reportingtoemployee = $this->User->getRecordsByID();
				// print_r($reportingtoemployee);exit;
				if (count($reportingtoemployee) > 0) {
					$fcmquery = $this->db->query("SELECT * FROM " . tbl_fcmdata . " WHERE usertype=1 AND memberid=" . $reportingtoemployee['reportingto']);
					$androidfcmid = $iosfcmid = array();
					if ($fcmquery->num_rows() > 0) {
						$this->load->model('Common_model', 'FCMData');
						$type = 15;
						$msg = "New Leave Added";
						$pushMessage = '{"type":"' . $type . '", "message":"' . $msg . '"}';
						$description = "New Leave Added by " . $reportingtoemployee['name'];

						foreach ($fcmquery->result_array() as $fcmrow) {
							if (trim($fcmrow['fcm']) !== '' && $fcmrow['devicetype'] == 1) {
								$androidfcmid[] = $fcmrow['fcm'];
							} elseif (trim($fcmrow['fcm']) !== '' && $fcmrow['devicetype'] == 2) {
								$iosfcmid[] = $fcmrow['fcm'];
							}
						}
						if (!empty($androidfcmid)) {
							$this->FCMData->sendFcmNotification($type, $pushMessage, $reportingtoemployee['reportingto'], $androidfcmid, 0, $description, 1);
						}
						if (!empty($iosfcmid)) {
							$this->FCMData->sendFcmNotification($type, $pushMessage, $reportingtoemployee['reportingto'], $iosfcmid, 0, $description, 2);
						}
						$notificationdata = array(
							'memberid' => $reportingtoemployee['reportingto'],
							'message' => $pushMessage,
							'type' => $type,
							'description' => $description,
							'createddate' => $createddate
						);
						$insertfcmnotification = $this->db->insert(tbl_notification, $notificationdata);
					}
				}
				echo 1;
			} else {
				echo 0;
			}
		} else {
			echo 2;
		}
	}

	public function updateleave()
	{
		$PostData = $this->input->post();
		// pre($PostData);
		$modifieddate = $this->general_model->getCurrentDateTime();
		$modifiedby = $this->session->userdata(base_url() . 'ADMINID');

		if ($PostData['leavetype'] == 1) {
			$halfleave = "0";
			$todate = $this->general_model->convertdate($PostData['todate']);
		} else {
			$halfleave = $PostData['halfleave'];
			$todate = "0000-00-00";
		}

		$updatedata = array(
			"employeeid" => $modifiedby,
			"fromdate" => $this->general_model->convertdate($PostData['fromdate']),
			"todate" => $todate,
			"reason" => $PostData['reason'],
			"status" => 0,
			"leavetype" => $PostData['leavetype'],
			"halfleave" => $halfleave,
			"modifieddate" => $modifieddate,
			"modifiedby" => $modifiedby
		);

		$updatedata = array_map('trim', $updatedata);

		$this->Leave->_where = array("id" => $PostData['id']);
		$Edit = $this->Leave->Edit($updatedata);
		if ($Edit) {
			echo 1;
		} else {
			echo 0;
		}
	}

	public function leaveedit($id)
	{
		$this->checkAdminAccessModule('submenu', 'edit', $this->viewData['submenuvisibility']);
		$this->viewData['title'] = "Edit Leave";
		$this->viewData['module'] = "leave/Addleave";
		$this->viewData['action'] = "1"; //Edit

		$where = array();
		if (isset($this->viewData['submenuvisibility']['submenuviewalldata']) && strpos($this->viewData['submenuvisibility']['submenuviewalldata'], ',' . $this->session->userdata[base_url() . 'ADMINUSERTYPE'] . ',') === false) {
			$where = array('(reportingto=' . $this->session->userdata(base_url() . 'ADMINID') . " or id=" . $this->session->userdata(base_url() . 'ADMINID') . ")" => null);
		}
		$this->viewData['userdata'] = $this->User->getUserListData($where);
		$this->Leave->_where = 'id=' . $id;
		$this->viewData['leavedata'] = $this->Leave->getRecordsByID();
		// pre($this->viewData['leavedata']);
		$this->admin_headerlib->add_plugin("bootstrap-datepicker", "bootstrap-datepicker/bootstrap-datepicker.css");
		$this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker", "bootstrap-datepicker/bootstrap-datepicker.js");
		$this->admin_headerlib->add_javascript("addleave", "pages/addleave.js");
		$this->load->view(ADMINFOLDER . 'template', $this->viewData);
	}

	public function checkleaveuse()
	{
		echo $count = 0;
	}

	public function deletemulleave()
	{
		$PostData = $this->input->post();
		$ids = explode(",", $PostData['ids']);

		$count = 0;
		$ADMINID = $this->session->userdata(base_url() . 'ADMINID');
		foreach ($ids as $row) {
			$this->db->where('id', $row);
			$this->db->delete(tbl_leave);
		}
	}

	public function insertapproved()
	{

		$PostData = $this->input->post();
		// print_r($PostData);exit;
		$granted = $PostData['granted'];
		$id = $PostData['id'];
		$employeeid = $PostData['employeeid'];
		$remarksForEmail = isset($PostData['remarks'])?$PostData['remarks']:'';

		if ($granted == "true") {
			$leavestatus = 1;
		} else {
			$leavestatus = 2;
		}

		$updatedata = array("granted" => $granted, "status" => $leavestatus);
		$updatedata = array_map('trim', $updatedata);
		$this->Leave->_where = array("id" => $id);
		$Edit = $this->Leave->Edit($updatedata);

		if ($Edit) {

			$this->Leave->_fields = "*,(SELECT u.name FROM ".tbl_user." AS u WHERE u.id = employeeid) AS name,(SELECT u.email FROM ".tbl_user." AS u WHERE u.id = employeeid) AS email";
			$this->Leave->_where = array("id" => $id);
			$leaverow = $this->Leave->getRecordsById();

			$this->User->_fields = "email";
			$this->User->_where = "id = '" . $this->session->userdata(base_url() . 'ADMINID') . "'";
			$officeemail = $this->User->getRecordsById();

			$email = $officeemail['email'];

			if ($granted == "true") {
				$status = "Approved";
			} else {
				$status = "Decline";
			}
			$to = $leaverow['email'];

			$leavedates = " for " . $this->general_model->displaydate($leaverow['fromdate']);
			if ($leaverow['todate'] != "0000-00-00") {
				$leavedates .= " to " . $this->general_model->displaydate($leaverow['todate']);
			} else {
				$leavedates .= " to " . $this->general_model->displaydate($leaverow['fromdate']);
			}

			$subject = "RK infotech - Request for leave";
			if (trim($remarksForEmail) != "") {
				$Message = "
					<html>
					<head>
					<title>Request for leave</title>
					</head>
					<body>
						<p>Hello " . $leaverow['name'] . ",</p>
						<p>Your Leave " . $status . $leavedates . ".</p>
						<p>Reason: " . $remarksForEmail . "</p>
						<p>Regards, </p>
						<p>RK infotech</p>
						</body>
					</html>
				";
			} else {
				$Message = "
					<html>
					<head>
					<title>Request for leave</title>
					</head>
					<body>
						<p>Hello " . $leaverow['name'] . ",</p>
						<p>Your Leave " . $status . $leavedates . ".</p>
						<p>Regards, </p>
						<p>RK infotech</p>
					</body>
					</html>
					";
			}
			/* Always set content-type when sending HTML email */
			$headers = "MIME-Version: 1.0" . "\r\n";
			$headers .= "Content-type:text/html;charset=iso-8859-1" . "\r\n";

			/* More headers */
			$headers .= "From:" . $email;
			$sent = mail($to, $subject, $Message, $headers);
			echo 1;
		} else {
			echo 0;
		}
	}

	/* public function viewleave($id)
    {
    	$this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
		$this->viewData['title'] = "View Leave";
		$this->viewData['module'] = "leave/Viewleave";

		$this->viewData['leavedata'] = $this->Leave->getsingleLeave($id);
		
		$this->load->view(ADMINFOLDER.'template',$this->viewData);
    } */

	public function changestatus()
	{
		$PostData = $this->input->post();
		if ($PostData['granted'] == "false") {
			$remarks = $PostData['remarks'];
			$changestatus = 2;
		} else {
			$remarks = "";
			$changestatus = 1;
		}
		$updatedata = array("status" => $changestatus, "remarks" => $remarks);
		$updatedata = array_map('trim', $updatedata);
		$this->Leave->_where = array("id" => $PostData['id']);
		$Edit = $this->Leave->Edit($updatedata);

		if ($Edit) {
			// if($PostData['status']==1 || $PostData['status']==2) 
			// {
			// 	$createddate = $this->general_model->getCurrentDateTime();
			// 	$leaveemployee = $this->db->query("SELECT employeeid,fromdate,todate FROM ".tbl_leave." WHERE id=".$PostData['id']);

			// 	$description="";
			// 	if($leaveemployee->num_rows() > 0)
			// 	{
			// 		$leaveemployeedata = $leaveemployee->row_array();
			// 		$leaveemployeeid = $leaveemployeedata['employeeid'];
			// 		$description = "Date : ".date("d/m/Y",strtotime($leaveemployeedata['fromdate']))." - ".date("d/m/Y",strtotime($leaveemployeedata['todate']));
			// 	}
			// 	if(isset($leaveemployeeid))
			// 	{
			// 		$message="";
			// 		if($PostData['status']==1) {
			// 		$message_type=8;
			// 		$message = "Your Leave Approved";
			// 		}
			// 		if($PostData['status']==2){
			// 		$message_type=9;
			// 		$message = "Your Leave Rejected";
			// 		}
			// 		if($message!="")
			// 		{
			// 		$fcmquery = $this->db->query("SELECT * FROM ".tbl_fcmdata." WHERE employeeid=".$leaveemployeeid); 
			// 		$this->load->model('Common_model','FCMData'); 
			// 		$employeearr = $androidfcmid = $iosfcmid = array();
			// 		if($fcmquery->num_rows() > 0)
			// 		{
			// 				$pushMessage = '{"type":"'.$message_type.'", "message":"'.$message.'"}';
			// 				$employeearr[] = $leaveemployeeid;

			// 				foreach ($fcmquery->result_array() as $fcmrow) {
			// 					if(trim($fcmrow['fcm'])!=='' && $fcmrow['devicetype']==1){
			// 						$androidfcmid[] = $fcmrow['fcm']; 	 
			// 					}else if(trim($fcmrow['fcm'])!=='' && $fcmrow['devicetype']==2){
			// 						$iosfcmid[] = $fcmrow['fcm'];
			// 					}
			// 				}   
			// 				if(!empty($androidfcmid)){
			// 					$this->FCMData->sendFcmNotification($message_type, $pushMessage,implode(",",$employeearr) ,$androidfcmid ,0,$description,1);
			// 				}
			// 				if(!empty($iosfcmid)){							
			// 					$this->FCMData->sendFcmNotification($message_type, $pushMessage,implode(",",$employeearr) ,$iosfcmid ,0,$description,2);		
			// 				}

			// 				$notificationdata = array('employeeid' => implode(",",$employeearr),
			// 				'message' => $pushMessage,
			// 				'type' => $message_type,
			// 				'description' => $description,
			// 				'createddate' => $createddate);
			// 				$insertfcmnotification = $this->db->insert(tbl_notification, $notificationdata);
			// 		}
			// 		}
			// 	}
			// }
			echo 1;
		} else {
			echo 0;
		}
	}

	public function addpaidunpaidleave(){
		$PostData = $this->input->post();
		$leaveid = $PostData["leaveid"];
		$previousleaveis = $PostData["previousleaveis"];
		$changeleave = $PostData["changeleave"];
		$reason = $PostData["reason"];
		$createddate = $this->general_model->getCurrentDateTime();
		$modifieddate = $this->general_model->getCurrentDateTime();
		$addedby = $this->session->userdata(base_url() . 'ADMINID');

		$updatedata = array(
			"paidunpaid" => $changeleave
		);
		$this->Leave->_where = array("id" => $leaveid);
		$this->Leave->Edit($updatedata);

		$insertdata = array(
			"leaveid" => $leaveid,
			"leavefrom" => $previousleaveis,
			"leaveto" => $changeleave,
			"reason" => $PostData["reason"],
			"createdate" => $createddate,
			"modifydate" => $modifieddate,
			"addedby" => $addedby,
			"modifiedby" => $addedby
		);
		$this->Leave->_table = tbl_leavepaidunpaidhistory;
		$Add = $this->Leave->Add($insertdata);

		if($Add){
			echo 1;
		}
		else{
			echo 0;
		}
		
	}
}
