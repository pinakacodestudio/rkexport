<?php

class Email_format_model extends Common_model {

    public $_table = tbl_emailtemplate;
    public $_fields = "*";
    public $_where = array();
    public $_except_fields = array();

    function __construct() {
        parent::__construct();
    }

    function getEmailformateListData($channelid=0,$memberid=0){

		$query = $this->readdb->select("id, mailid, subject, emailbody,createddate")
                ->from(tbl_emailtemplate)
                ->where("channelid='".$channelid."' AND memberid='".$memberid."'")
				->order_by("id","DESC")
				->get();
	
		return $query->result_array();
	}

    function CheckMailFormatAvailable($mailid,$id='',$channelid=0,$memberid=0)
    {
        if (isset($id) && $id != '') {
            $query = $this->readdb->query("SELECT id FROM ".tbl_emailtemplate." WHERE mailid ='".$mailid."' AND id <> '".$id."' AND channelid='".$channelid."' AND memberid='".$memberid."'");
        }else{
            $query = $this->readdb->query("SELECT id FROM ".tbl_emailtemplate." WHERE mailid ='".$mailid."' AND channelid='".$channelid."' AND memberid='".$memberid."'");
        }
       
        if($query->num_rows()  > 0){
            return 0;
        }
        else{
            return 1;
        }
    }

    function installationgaragemail($orderno){

        $query = $this->readdb->select("c.email,(SELECT CONCAT(cba.firstname,' ',cba.lastname) FROM ".tbl_customerbillingaddress." as cba WHERE cba.customerid=c.id ORDER BY cba.id DESC LIMIT 1) as customername")
                            ->from(tbl_orders." as o")
                            ->join(tbl_customer." as c","c.status=1 AND c.id=o.customerid","INNER")
                            ->where("ordernumber=".$orderno." AND o.status=0 AND o.isdelete=0")
                            ->get();

        $CustomerData = $query->row_array();
        if(!empty($CustomerData)){
            $this->load->model('Order_model', 'Order');
            $InstallationProduct = $this->Order->getOrderProductForInstallation($orderno);

            if(!empty($InstallationProduct)){
                    $gargedetails = '<table cellpadding="10" cellspacing="0" style="font-family: \'Helvetica Neue\', Helvetica, Roboto, Arial, sans-serif;color: #636363;font-size: 14px;width:100%;">
                                    <thead>
                                      <tr>
                                        <th style="border: 1px solid #ddd;">Product</th>
                                        <th style="border: 1px solid #ddd;">Garge Details</th>
                                      </tr>
                                    </thead><tbody>';

                    foreach ($InstallationProduct as $row) {
                        
                        $gargedetails .= '<tr>
                                            <td style="border: 1px solid #ddd;text-align: center;">'.$row["name"].$row["carmodel"].'<br><span style="font-size:13px;">'.$row['shortdescription'].'</span></td>
                                            <td style="border: 1px solid #ddd;">  
                                              <b>'.$row["firmname"].'</b><br>
                                              <span><b>Contact Person: </b>'.ucwords($row["contactperson"]).'</span><br>
                                              <span><b>Address: </b>'.$row["address"].', '.$row["cityname"].'</span><br>
                                              <span><b>Email: </b>'.$row["email"].'</span><br>
                                              <span><b>Mobile No: </b>'.$row["mobileno"].'</span><br>
                                            </td>
                                        </tr>';
                    }

                    $gargedetails .= '</tbody></table>';

                    /* SEND EMAIL TO USER */
                    $mailBodyArr1 = array(
                        "{logo}" => '<a href="' . DOMAIN_URL . '"><img src="' . MAIN_LOGO_IMAGE_URL.CompanyLogo.'" alt="' . Companyname . '" style="border: none; display: inline; font-size: 14px; font-weight: bold; height: auto; line-height: 100%; outline: none; text-decoration: none; text-transform: capitalize;"/></a>',
                        "{customername}" => ucwords($CustomerData['customername']),
                        "{gargedetails}" => $gargedetails,
                        "{companyemail}" => CompanyEmail,
                        "{companyname}" => Companyname,
                        "{companywebsite}" => '<a href="'.CompanyWebsite.'" target="_blank">'.CompanyWebsite.'</a>'
                    );

                    //Send mail with email format store in database
                    $emailSend = $this->Order->sendMail(7, $CustomerData['email'], $mailBodyArr1);
                }
        }

    }
    function productreviewemail($orderno){

        $query = $this->readdb->select("c.email,(SELECT CONCAT(cba.firstname,' ',cba.lastname) FROM ".tbl_customerbillingaddress." as cba WHERE cba.customerid=c.id ORDER BY cba.id DESC LIMIT 1) as customername")
                            ->from(tbl_orders." as o")
                            ->join(tbl_customer." as c","c.status=1 AND c.id=o.customerid","INNER")
                            ->where("ordernumber=".$orderno)
                            ->get();

        $CustomerData = $query->row_array();
        if(!empty($CustomerData)){
            $this->load->model('Order_model', 'Order');
            /* SEND EMAIL TO USER */
            $mailBodyArr1 = array(
                "{logo}" => '<a href="' . DOMAIN_URL . '"><img src="' . MAIN_LOGO_IMAGE_URL.CompanyLogo.'" alt="' . Companyname . '" style="border: none; display: inline; font-size: 14px; font-weight: bold; height: auto; line-height: 100%; outline: none; text-decoration: none; text-transform: capitalize;"/></a>',
                "{customername}" => ucwords($CustomerData['customername']),
                "{reviewlink}" => '<a href="'.FRONT_URL.'productreview/'.$orderno.'" target="_blank">Click here for product review</a>',
                "{companyemail}" => CompanyEmail,
                "{companyemail}" => CompanyEmail,
                "{companywebsite}" => '<a href="'.CompanyWebsite.'" target="_blank">'.CompanyWebsite.'</a>'
            );

            //Send mail with email format store in database
            $emailSend = $this->Order->sendMail(8, $CustomerData['email'], $mailBodyArr1);
        }

    }
}
