<?php

class Frontend_Controller extends MY_Controller {

    public $viewData = array();
    
    function __construct() {
        
        parent::__construct();
        $this->load->library("Frontend_headerlib");
        $this->load->library('user_agent');
        
        $this->load->model('Frontendmainmenu_model', 'Frontendmainmenu');
        $this->viewData['frontendmainmenu'] = $this->Frontendmainmenu->getFrontendMainmenu();
        
        $this->load->model('Frontendsubmenu_model', 'Frontendsubmenu');
        $this->viewData['frontendsubmenu'] = $this->Frontendsubmenu->getActiveFrontendSubmenu();

        $this->load->model('Managecontent_model','Footermanagecontentdata');

        $this->viewData['footerquicklink'] = $this->help("quicklink");
        $this->viewData['footerproducts'] = $this->help("ourproduct");
        $this->viewData['footerlinks'] = $this->help("footerlink");

        $this->viewData['sidebar'] = array();

        $this->load->model('Blogcategory_model', 'Blog_category');
        $this->viewData['sidebar']['blogcategorydata'] = $this->Blog_category->getActiveBlogCategoryListOnFront();

        $this->load->model('Blog_model', 'Blog');
        $this->viewData['sidebar']['recentblogdata'] = $this->Blog->getRecentBlogs();

        $this->load->model('Country_model', 'Country');
		// $this->Country->_fields = "id, CONCAT(phonecode,' (',name,')') as phonecodewithname";
        $this->viewData['countrycodedata'] = $this->Country->getCountrycode();
        
        $this->viewData['viewcartproducts'] = $this->getcartproducts();

        $arrSessionDetails = $this->session->userdata;
        if(isset($arrSessionDetails[base_url().'MEMBER_ID'])){
        
        }else{

            /**GOOGLE LOGIN**/
            //Include two files from google-php-client library in controller
            require_once APPPATH . 'third_party/Googleapi/src/Google/autoload.php';
            include_once APPPATH . "third_party/Googleapi/src/Google/Client.php";
            include_once APPPATH . "third_party/Googleapi/src/Google/Service/Oauth2.php";

            // Store values in variables from project created in Google Developer Console
            $client_id = CLIENT_ID;
            $client_secret = CLIENT_SECRET;
            $redirect_uri = REDIRECT_URL;
            $simple_api_key = SIMPLE_API_KEY;

            // Create Client Request to access Google API
            $client = new Google_Client();
            $client->setApplicationName(COMPANY_NAME);
            $client->setClientId($client_id);
            $client->setClientSecret($client_secret);
            $client->setRedirectUri($redirect_uri);
            $client->setDeveloperKey($simple_api_key);
            $client->addScope("https://www.googleapis.com/auth/userinfo.profile https://www.googleapis.com/auth/userinfo.email");

            // Send Client Request
            $objOAuthService = new Google_Service_Oauth2($client);
            
            $authUrl = $client->createAuthUrl();
            $this->viewData['googleauthUrl'] = $authUrl;

            /**FACEBOOK LOGIN**/
            $this->load->library('facebook');
            $this->viewData['facebookauthUrl'] = $this->facebook->login_url();
        }
    }
    function getcartproducts() {
        
        $this->load->model("Product_prices_model","Product_prices");  
        $Cartproduct = $viewcartproducts = array();
        $arrSessionDetails = $this->session->userdata;
        if(isset($arrSessionDetails[base_url().'MEMBER_ID'])){
            $this->load->model('Cart_model', 'Cart');
            
            if(isset($arrSessionDetails[base_url().'PRODUCT']) && !empty($arrSessionDetails[base_url().'PRODUCT'])){
                $product = json_decode($arrSessionDetails[base_url().'PRODUCT'],true);
                $createddate = $this->general_model->getCurrentDateTime();
                
                if(!empty($arrSessionDetails[base_url().'MEMBER_ID'])){
                    $memberid = $arrSessionDetails[base_url().'MEMBER_ID'];
                    $pricechannelid = CUSTOMERCHANNELID;
                    
                    for ($i=0; $i < count($product); $i++) {
                       
                        $this->Cart->_fields = "id";
                        $this->Cart->_where = "memberid=".$memberid." AND sellermemberid=0 AND productid=".$product[$i]['productid']." AND priceid=".$product[$i]['productpriceid']." AND type=1";
                        $CartData = $this->Cart->getRecordsByID();

                        $ProductData = $this->Product_prices->getProductpriceById($product[$i]['productpriceid']);
                        if($product[$i]['referencetype']==1){
                            $multipleprice = $this->Product_prices->getProductBasicQuantityPriceDataByPriceID($pricechannelid,$product[$i]['productpriceid'],$product[$i]['productid']);
                        }else{
                            $multipleprice = $this->Product_prices->getProductQuantityPriceDataByPriceID($product[$i]['productpriceid']);
                        }
                        if(!empty($CartData)){

                            $quantity = $product[$i]['quantity'];   
                            $referenceid = "";
                            if(!empty($multipleprice)){
                              if(!empty($ProductData) && $ProductData['pricetype']==1){
                                if($ProductData['quantitytype']==0){
                
                                  foreach($multipleprice as $pr){
                                    if($product[$i]['quantity'] >= $pr['quantity']){
                                      $referenceid = $pr['id'];             
                                    }
                                  }
                                }else{
                                  $referenceid = $product[$i]['referenceid'];   
                                  $quantity = $product[$i]['quantity'];         
                                }
                              }else{
                                $referenceid = $multipleprice[0]['id'];
                              }
                            }
                            $updatedata = array("quantity"=>$quantity,
                                                "referencetype"=>$product[$i]['referencetype'],
                                                "referenceid"=>$referenceid,
                                                "modifieddate"=>$createddate);

                            $updatedata=array_map('trim',$updatedata);

                            $this->Cart->_where = "id=".$CartData['id'];
                            $this->Cart->Edit($updatedata);
                        }else{

                            $referenceid = "";
                            if(!empty($multipleprice)){
                                if(!empty($Product) && $Product['pricetype']==1){
                                    if($Product['quantitytype']==0){

                                    foreach($multipleprice as $pr){
                                        if($product[$i]['quantity'] >= $pr['quantity']){
                                            $referenceid = $pr['id'];             
                                        }
                                    }
                                    }else{
                                        $referenceid = $product[$i]['referenceid']; 
                                    }
                                }else{
                                    $referenceid = $multipleprice[0]['id'];
                                }
                            }
                            
                            $insertdata = array("memberid"=>$memberid,
                                        "productid"=>$product[$i]['productid'],
                                        "priceid"=>$product[$i]['productpriceid'],
                                        "quantity"=>$product[$i]['quantity'],
                                        "referencetype"=>$product[$i]['referencetype'],
                                        "referenceid"=>$referenceid,
                                        "type"=>1,
                                        "createddate"=>$createddate,
                                        "modifieddate"=>$createddate);

                            $insertdata=array_map('trim',$insertdata);

                            $this->Cart->Add($insertdata);
                        }
                    }
                }
            }
            
            $cartproduct = $this->Cart->getCustomerCartProducts($arrSessionDetails[base_url().'MEMBER_ID']);
            $productdata = array(base_url().'PRODUCT' => json_encode($cartproduct));
            $this->session->set_userdata($productdata);
        }
        
        if(isset($arrSessionDetails[base_url().'PRODUCT']) && !empty($arrSessionDetails[base_url().'PRODUCT'])){
            $this->load->model('Product_model', 'Product');
            $viewcartproducts = $this->Product->getCartProductBysession($arrSessionDetails);
        }else{
            if(!empty($Cartproduct)){
                $this->load->model('Product_model', 'Product');
                $viewcartproducts = $this->Product->getCartProduct();
            }
        }
        
        return $viewcartproducts;
    }
    /* function getcaptcha() {
        $this->load->library('mathcaptcha');
        $config = array('question_format'=>'numeric',
                        'answer_format'=>'either');
        $this->mathcaptcha->init($config);
     
        $this->viewData['math_captcha_question'] = $this->mathcaptcha->get_question();

    }
    function social() {
    	$this->load->model('socialmedia_model','Socialmedia');
    	$this->Socialmedia->_where="status=1";
    	return $this->viewData['socialmediadata']=$this->Socialmedia->getRecordByID();

    }

    function knowroger() {
    	$this->load->model('Managecontent_model','Footermanagecontentdata');
    	
        $query=$this->readdb->query("SELECT contentid, slug FROM ".tbl_managecontent." ORDER BY id ASC LIMIT 6");
    	return $this->viewData['footermanagecontentdata']=$query->result_array();
    } */

    function help($section,$channelid=0,$memberid=0) {
        $this->load->model('Managecontent_model','Footermanagecontentdata');
        
        $where = "1=1";
        if($section=="footerlink"){
            $where = "footerlink=1 AND channelid='".$channelid."' AND memberid='".$memberid."'";
        }else if($section=="ourproduct"){
            $where = "ourproduct=1 AND channelid='".$channelid."' AND memberid='".$memberid."'";
        }else{
            $where = "quicklink=1 AND channelid='".$channelid."' AND memberid='".$memberid."'";
        }
        $query=$this->readdb->query("SELECT title, slug FROM ".tbl_managewebsitecontent." WHERE ".$where." ORDER BY id ASC");
        return $query->result_array();
    }

    /* function save_routes(){
        $output="<?php \n";
       
        //for the add dynamic route trough the helper file
        $output.="\$route['".CATEGORY_SLUG."/(:any)'] = 'Collections/index/$1';\n";
        $output.="\$route['^(?!".CATEGORY_SLUG.")(:any)'] = 'manage_website_content/index/$1';";
        
        $this->load->helper('file');
        //write to the helper file
        write_file(APPPATH . "helpers/define_routes_helper.php", $output);
    } */

}
