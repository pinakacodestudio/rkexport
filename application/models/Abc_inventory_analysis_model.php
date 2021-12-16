<?php
class Abc_inventory_analysis_model extends Common_model {

	//put your code here
	public $_table = tbl_product;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
	public $column_order = array(null,'productname','pp.sku','pp.price','sold','cumulativeshare'); //set column field database for datatable orderable
	public $column_search = array('pp.sku',"((CONCAT(p.name,' ',IFNULL((SELECT CONCAT('[',GROUP_CONCAT(v.value),']') FROM ".tbl_productcombination." as pc INNER JOIN ".tbl_variant." as v ON v.id=pc.variantid WHERE pc.priceid=pp.id),''))))",'pp.price'); //set column field database for datatable searchable 
	public $order = array('p.id' => 'DESC'); // default order
    public $mainquery = '';
    
	function __construct() {
        parent::__construct();
    }

    //Export Data
    function exportABCInventoryReport(){
        
        $MEMBERID = $this->session->userdata(base_url().'MEMBERID');
        $startdate = $this->general_model->convertdate($_REQUEST['startdate']);
        $enddate = $this->general_model->convertdate($_REQUEST['enddate']);

        
        $this->mainquery = "SELECT temp.id,temp.productid,temp.productname,temp.isuniversal,temp.variantid,temp.sku,temp.price,temp.sold,(temp.sold*100/temp.totalqty) as cumulativeshare
                        FROM 
                            ( 

                            SELECT pp.id,p.isuniversal,pp.productid,
                            CONCAT(p.name,' ',IFNULL((SELECT CONCAT('[',GROUP_CONCAT(v.value),']') 
                                            FROM ".tbl_productcombination." as pc 
                                            INNER JOIN ".tbl_variant." as v ON v.id=pc.variantid WHERE pc.priceid=pp.id)
                                            ,'')) as productname,pp.sku,
                                            
                                            (SELECT GROUP_CONCAT(v.id) FROM ".tbl_productcombination." as pc INNER JOIN ".tbl_variant." as v on v.id=pc.variantid WHERE pc.priceid=pp.id) as variantid,";


        if(!is_null($MEMBERID)){
            $this->load->model("Channel_model","Channel");
            $channeldata = $this->Channel->getMemberChannelData($MEMBERID);
            $memberbasicsalesprice = (!empty($channeldata['memberbasicsalesprice']))?$channeldata['memberbasicsalesprice']:0;
            $memberspecificproduct = (!empty($channeldata['memberspecificproduct']))?$channeldata['memberspecificproduct']:0;
            $currentsellerid = (!empty($channeldata['currentsellerid']))?$channeldata['currentsellerid']:0;
            $totalproductcount = (!empty($channeldata['totalproductcount']))?$channeldata['totalproductcount']:0;
            $channelid = (!empty($channeldata['channelid']))?$channeldata['channelid']:0;

            $this->load->model('Product_model','Product');
            $CheckProduct = $this->Product->getMemberProductCount($MEMBERID);
            
            if($CheckProduct['count'] > 0 && $memberspecificproduct==1){
                $memberbasicsalesprice = 0;
                $this->mainquery .= "mvp.price,";
            }else{
                $memberbasicsalesprice = ($currentsellerid==0 && $memberbasicsalesprice==0)?1:$memberbasicsalesprice;

                $this->mainquery .= "IF(".$memberbasicsalesprice."=1,IFNULL((SELECT pbp.salesprice FROM ".tbl_productbasicpricemapping." as pbp WHERE pbp.productpriceid=pp.id AND pbp.channelid=".$channelid." AND pbp.productid=p.id AND pbp.salesprice!=0 AND pbp.allowproduct=1),0),pp.price) as price,";
            }
            $this->mainquery .= "IFNULL((SELECT SUM(quantity) 
                                    FROM ".tbl_transactionproducts." as trp
                                    INNER JOIN ".tbl_invoice." as i ON i.sellermemberid=".$MEMBERID." AND i.status=1 AND (i.invoicedate BETWEEN '".$startdate."' AND '".$enddate."')
                                    WHERE trp.productid=p.id AND (trp.priceid=pp.id OR trp.priceid=0) AND trp.transactionid=i.id AND trp.transactiontype=3
                                ),0) as sold,
                            
                            IFNULL((SELECT SUM(quantity) 
                                    FROM ".tbl_transactionproducts." as trp
                                    INNER JOIN ".tbl_invoice." as i ON i.sellermemberid=".$MEMBERID." AND i.status=1 AND (i.invoicedate BETWEEN '".$startdate."' AND '".$enddate."')
                                    INNER JOIN ".tbl_product." as p ON p.id = trp.productid AND p.producttype=0
                                    WHERE trp.transactionid=i.id AND trp.transactiontype=3 
                                ),0) as totalqty";

        }else{
            
            $this->mainquery .= "pp.price,
                                    
                                IFNULL((SELECT SUM(quantity) 
                                        FROM ".tbl_transactionproducts." as trp
                                        INNER JOIN ".tbl_invoice." as i ON i.sellermemberid=0 AND i.status=1 AND (i.invoicedate BETWEEN '".$startdate."' AND '".$enddate."')
                                        WHERE trp.productid=p.id AND (trp.priceid=pp.id OR trp.priceid=0) AND trp.transactionid=i.id AND trp.transactiontype=3
                                    ),0) as sold,
                                
                                IFNULL((SELECT SUM(quantity) 
                                        FROM ".tbl_transactionproducts." as trp
                                        INNER JOIN ".tbl_invoice." as i ON i.sellermemberid=0 AND i.status=1 AND (i.invoicedate BETWEEN '".$startdate."' AND '".$enddate."')
                                        INNER JOIN ".tbl_product." as p ON p.id = trp.productid AND p.producttype=0
                                        WHERE trp.transactionid=i.id AND trp.transactiontype=3
                                    ),0) as totalqty";
        }

        $this->mainquery .= " FROM ".tbl_product." as p";
        if(!is_null($MEMBERID)){
            if($CheckProduct['count'] > 0 && $memberspecificproduct==1){
            
                $this->mainquery .= " INNER JOIN ".tbl_member." as m ON m.id='".$MEMBERID."'";
                $this->mainquery .= " INNER JOIN ".tbl_memberproduct." as mp ON mp.memberid=m.id AND sellermemberid=".$currentsellerid." AND mp.productid=p.id";
                $this->mainquery .= " INNER JOIN ".tbl_productprices." as pp ON pp.productid=p.id AND (FIND_IN_SET(pp.id, (SELECT GROUP_CONCAT(priceid) FROM ".tbl_membervariantprices." WHERE memberid='".$MEMBERID."' AND sellermemberid=".$currentsellerid."))>0 OR '".$MEMBERID."'='0')";
                $this->mainquery .= " INNER JOIN ".tbl_membervariantprices." as mvp ON mvp.memberid=mp.memberid AND mvp.sellermemberid=".$currentsellerid." AND mvp.priceid=pp.id";
                $this->mainquery .= " WHERE p.producttype=0";
    
            }else{
                
                $this->mainquery .= " INNER JOIN ".tbl_productprices." as pp ON pp.productid=p.id";
                $this->mainquery .= " WHERE p.producttype=0 AND pp.id IN (IF(".$memberbasicsalesprice."=1,IFNULL((SELECT pbp.productpriceid FROM ".tbl_productbasicpricemapping." as pbp WHERE pbp.productpriceid=pp.id AND pbp.channelid=".$channelid." AND pbp.allowproduct=1 AND pbp.productid=p.id AND pbp.salesprice!=0),0),pp.id))";
    
            }
        }else{
            $this->mainquery .= " INNER JOIN ".tbl_productprices." as pp ON pp.productid=p.id";
            $this->mainquery .= " WHERE p.producttype=0";
        }

        $this->mainquery .= " ORDER BY ".key($this->order)." ".$this->order[key($this->order)];
        $this->mainquery .= ") as temp ORDER BY sold DESC";
        
        $query = $this->readdb->query($this->mainquery);
		return $query->result();
    }
    //Get ABC Inventory Report Data On API
    function getABCInventoryReportDataOnAPI($memberid,$fromdate,$todate,$counter){
        
        $limit = 10;
        $this->mainquery = "SELECT temp.id,temp.productid,temp.productname,temp.isuniversal,temp.variantid,temp.sku,temp.price,temp.sold,(temp.sold*100/temp.totalqty) as cumulativeshare
                        FROM 
                            ( 

                            SELECT pp.id,p.isuniversal,pp.productid,
                            CONCAT(p.name,' ',IFNULL((SELECT CONCAT('[',GROUP_CONCAT(v.value),']') 
                                            FROM ".tbl_productcombination." as pc 
                                            INNER JOIN ".tbl_variant." as v ON v.id=pc.variantid WHERE pc.priceid=pp.id)
                                            ,'')) as productname,pp.sku,
                                            
                                            (SELECT GROUP_CONCAT(v.id) FROM ".tbl_productcombination." as pc INNER JOIN ".tbl_variant." as v on v.id=pc.variantid WHERE pc.priceid=pp.id) as variantid,";
        
        $this->load->model("Channel_model","Channel");
        $channeldata = $this->Channel->getMemberChannelData($memberid);
        $memberbasicsalesprice = (!empty($channeldata['memberbasicsalesprice']))?$channeldata['memberbasicsalesprice']:0;
        $memberspecificproduct = (!empty($channeldata['memberspecificproduct']))?$channeldata['memberspecificproduct']:0;
        $currentsellerid = (!empty($channeldata['currentsellerid']))?$channeldata['currentsellerid']:0;
        $totalproductcount = (!empty($channeldata['totalproductcount']))?$channeldata['totalproductcount']:0;
        $channelid = (!empty($channeldata['channelid']))?$channeldata['channelid']:0;

        $this->load->model('Product_model','Product');
        $CheckProduct = $this->Product->getMemberProductCount($memberid);
        
        if($CheckProduct['count'] > 0 && $memberspecificproduct==1){
            $memberbasicsalesprice = 0;
            $this->mainquery .= "mvp.price,";
        }else{
            $memberbasicsalesprice = ($currentsellerid==0 && $memberbasicsalesprice==0)?1:$memberbasicsalesprice;

            $this->mainquery .= "IF(".$memberbasicsalesprice."=1,IFNULL((SELECT pbp.salesprice FROM ".tbl_productbasicpricemapping." as pbp WHERE pbp.productpriceid=pp.id AND pbp.channelid=".$channelid." AND pbp.productid=p.id AND pbp.salesprice!=0 AND pbp.allowproduct=1),0),pp.price) as price,";
        }
        $this->mainquery .= "IFNULL((SELECT SUM(quantity) 
                                FROM ".tbl_transactionproducts." as trp
                                INNER JOIN ".tbl_invoice." as i ON i.sellermemberid=".$memberid." AND i.status=1 AND (i.invoicedate BETWEEN '".$fromdate."' AND '".$todate."')
                                WHERE trp.productid=p.id AND (trp.priceid=pp.id OR trp.priceid=0) AND trp.transactionid=i.id AND trp.transactiontype=3
                            ),0) as sold,
                        
                        IFNULL((SELECT SUM(quantity) 
                                FROM ".tbl_transactionproducts." as trp
                                INNER JOIN ".tbl_invoice." as i ON i.sellermemberid=".$memberid." AND i.status=1 AND (i.invoicedate BETWEEN '".$fromdate."' AND '".$todate."')
                                INNER JOIN ".tbl_product." as p ON p.id = trp.productid AND p.producttype=0
                                WHERE trp.transactionid=i.id AND trp.transactiontype=3 
                            ),0) as totalqty";

        $this->mainquery .= " FROM ".tbl_product." as p";
        if($CheckProduct['count'] > 0 && $memberspecificproduct==1){
        
            $this->mainquery .= " INNER JOIN ".tbl_member." as m ON m.id='".$memberid."'";
            $this->mainquery .= " INNER JOIN ".tbl_memberproduct." as mp ON mp.memberid=m.id AND sellermemberid=".$currentsellerid." AND mp.productid=p.id";
            $this->mainquery .= " INNER JOIN ".tbl_productprices." as pp ON pp.productid=p.id AND (FIND_IN_SET(pp.id, (SELECT GROUP_CONCAT(priceid) FROM ".tbl_membervariantprices." WHERE memberid='".$memberid."' AND sellermemberid=".$currentsellerid."))>0 OR '".$memberid."'='0')";
            $this->mainquery .= " INNER JOIN ".tbl_membervariantprices." as mvp ON mvp.memberid=mp.memberid AND mvp.sellermemberid=".$currentsellerid." AND mvp.priceid=pp.id";
            $this->mainquery .= " WHERE p.producttype=0";

        }else{
            
            $this->mainquery .= " INNER JOIN ".tbl_productprices." as pp ON pp.productid=p.id";
            $this->mainquery .= " WHERE p.producttype=0 AND pp.id IN (IF(".$memberbasicsalesprice."=1,IFNULL((SELECT pbp.productpriceid FROM ".tbl_productbasicpricemapping." as pbp WHERE pbp.productpriceid=pp.id AND pbp.channelid=".$channelid." AND pbp.allowproduct=1 AND pbp.productid=p.id AND pbp.salesprice!=0),0),pp.id))";

        }
       
        $this->mainquery .= " ORDER BY ".key($this->order)." ".$this->order[key($this->order)];
        $this->mainquery .= ") as temp ORDER BY sold DESC";
        if($counter != -1){
			$this->mainquery .= " LIMIT ".$counter.", ".$limit;
        } 
        $query = $this->readdb->query($this->mainquery);
		return $query->result_array();
    }
    
	//LISTING DATA
	function _get_datatables_query($type=1){

        $MEMBERID = $this->session->userdata(base_url().'MEMBERID');
        $startdate = $this->general_model->convertdate($_REQUEST['startdate']);
        $enddate = $this->general_model->convertdate($_REQUEST['enddate']);

        $this->mainquery = "SELECT temp.id,temp.productid,temp.productname,temp.isuniversal,temp.variantid,temp.sku,temp.minprice,temp.maxprice,temp.sold,
        
                            (temp.sold*100/temp.totalqty) as cumulativeshare
                        FROM 
                            ( 

                                SELECT pp.id,p.isuniversal,pp.productid,
                                CONCAT(p.name,' ',IFNULL((SELECT CONCAT('[',GROUP_CONCAT(v.value),']') 
                                                FROM ".tbl_productcombination." as pc 
                                                INNER JOIN ".tbl_variant." as v ON v.id=pc.variantid WHERE pc.priceid=pp.id)
                                            ,'')) as productname,pp.sku,
                                    (SELECT GROUP_CONCAT(v.id) FROM ".tbl_productcombination." as pc INNER JOIN ".tbl_variant." as v on v.id=pc.variantid WHERE pc.priceid=pp.id) as variantid,";
                                    
        if(!is_null($MEMBERID)){
            $this->load->model("Channel_model","Channel");
            $channeldata = $this->Channel->getMemberChannelData($MEMBERID);
            $memberbasicsalesprice = (!empty($channeldata['memberbasicsalesprice']))?$channeldata['memberbasicsalesprice']:0;
            $memberspecificproduct = (!empty($channeldata['memberspecificproduct']))?$channeldata['memberspecificproduct']:0;
            $currentsellerid = (!empty($channeldata['currentsellerid']))?$channeldata['currentsellerid']:0;
            $totalproductcount = (!empty($channeldata['totalproductcount']))?$channeldata['totalproductcount']:0;
            $channelid = (!empty($channeldata['channelid']))?$channeldata['channelid']:0;

            $this->load->model('Product_model','Product');
            $CheckProduct = $this->Product->getMemberProductCount($MEMBERID);
			
            if($CheckProduct['count'] > 0 && $memberspecificproduct==1){
                $memberbasicsalesprice = 0;
                $this->mainquery .= "mvp.price,
                    
                    IFNULL((SELECT min(mpqp.price) FROM ".tbl_memberproductquantityprice." as mpqp WHERE mpqp.membervariantpricesid=mvp.id AND mpqp.price>0),0) as minprice,
                    
                    IFNULL((SELECT max(mpqp.price) FROM ".tbl_memberproductquantityprice." as mpqp WHERE mpqp.membervariantpricesid=mvp.id AND mpqp.price>0),0) as maxprice,
                ";
            }else{
                $memberbasicsalesprice = ($currentsellerid==0 && $memberbasicsalesprice==0)?1:$memberbasicsalesprice;

                $this->mainquery .= "
                    IF(".$memberbasicsalesprice."=1,
                        IFNULL((SELECT pbp.salesprice FROM ".tbl_productbasicpricemapping." as pbp WHERE pbp.productpriceid=pp.id AND pbp.channelid=".$channelid." AND pbp.productid=p.id AND pbp.salesprice!=0 AND pbp.allowproduct=1),0)
                    ,pp.price) as price,
                    
                    IF(".$memberbasicsalesprice."=1,
                        IFNULL((SELECT min(pbqp.salesprice) FROM ".tbl_productbasicpricemapping." as pbp 
                            INNER JOIN ".tbl_productbasicquantityprice." as pbqp ON pbqp.productbasicpricemappingid=pbp.id AND pbqp.salesprice >0
                            WHERE pbp.productpriceid=pp.id AND pbp.channelid=".$channelid." AND pbp.productid=p.id AND pbp.allowproduct=1),0),
                        IFNULL((SELECT min(pqp.price) FROM ".tbl_productquantityprices." as pqp WHERE pqp.productpricesid=pp.id AND pqp.price>0),0)
                    ) as minprice,
                    
                    IF(".$memberbasicsalesprice."=1,
                        IFNULL((SELECT max(pbqp.salesprice) FROM ".tbl_productbasicpricemapping." as pbp 
                            INNER JOIN ".tbl_productbasicquantityprice." as pbqp ON pbqp.productbasicpricemappingid=pbp.id AND pbqp.salesprice >0
                            WHERE pbp.productpriceid=pp.id AND pbp.channelid=".$channelid." AND pbp.productid=p.id AND pbp.allowproduct=1),0),
                        IFNULL((SELECT max(pqp.price) FROM ".tbl_productquantityprices." as pqp WHERE pqp.productpricesid=pp.id AND pqp.price>0),0)
                    ) as maxprice,
                ";
            }
            $this->mainquery .= "IFNULL((SELECT SUM(quantity) 
                                    FROM ".tbl_transactionproducts." as trp
                                    INNER JOIN ".tbl_invoice." as i ON i.sellermemberid=".$MEMBERID." AND i.status=1 AND (i.invoicedate BETWEEN '".$startdate."' AND '".$enddate."')
                                    WHERE trp.productid=p.id AND (trp.priceid=pp.id OR trp.priceid=0) AND trp.transactionid=i.id AND trp.transactiontype=3
                                ),0) as sold,
                            
                            IFNULL((SELECT SUM(quantity) 
                                    FROM ".tbl_transactionproducts." as trp
                                    INNER JOIN ".tbl_invoice." as i ON i.sellermemberid=".$MEMBERID." AND i.status=1 AND (i.invoicedate BETWEEN '".$startdate."' AND '".$enddate."')
                                    INNER JOIN ".tbl_product." as p ON p.id = trp.productid AND p.producttype=0
                                    WHERE trp.transactionid=i.id AND trp.transactiontype=3 
                                ),0) as totalqty";

        }else{
            
            $this->mainquery .= "
                                IFNULL((SELECT min(pqp.price) FROM ".tbl_productquantityprices." as pqp WHERE pqp.productpricesid=pp.id AND pqp.price>0),0) as minprice,
                                
                                IFNULL((SELECT max(pqp.price) FROM ".tbl_productquantityprices." as pqp WHERE pqp.productpricesid=pp.id AND pqp.price>0),0) as maxprice,
                                    
                                IFNULL((SELECT SUM(quantity) 
                                        FROM ".tbl_transactionproducts." as trp
                                        INNER JOIN ".tbl_invoice." as i ON i.sellermemberid=0 AND i.status=1 AND (i.invoicedate BETWEEN '".$startdate."' AND '".$enddate."')
                                        WHERE trp.productid=p.id AND (trp.priceid=pp.id OR trp.priceid=0) AND trp.transactionid=i.id AND trp.transactiontype=3
                                    ),0) as sold,
                                
                                IFNULL((SELECT SUM(quantity) 
                                        FROM ".tbl_transactionproducts." as trp
                                        INNER JOIN ".tbl_invoice." as i ON i.sellermemberid=0 AND i.status=1 AND (i.invoicedate BETWEEN '".$startdate."' AND '".$enddate."')
                                        INNER JOIN ".tbl_product." as p ON p.id = trp.productid AND p.producttype=0
                                        WHERE trp.transactionid=i.id AND trp.transactiontype=3
                                    ),0) as totalqty";
        }
                           
        $this->mainquery .= " FROM ".tbl_product." as p";
        
        if(!is_null($MEMBERID)){

            if($CheckProduct['count'] > 0 && $memberspecificproduct==1){
            
            $this->mainquery .= " INNER JOIN ".tbl_member." as m ON m.id='".$MEMBERID."'";
            $this->mainquery .= " INNER JOIN ".tbl_memberproduct." as mp ON mp.memberid=m.id AND sellermemberid=".$currentsellerid." AND mp.productid=p.id";
            $this->mainquery .= " INNER JOIN ".tbl_productprices." as pp ON pp.productid=p.id AND (FIND_IN_SET(pp.id, (SELECT GROUP_CONCAT(priceid) FROM ".tbl_membervariantprices." WHERE memberid='".$MEMBERID."' AND sellermemberid=".$currentsellerid."))>0 OR '".$MEMBERID."'='0')";
            $this->mainquery .= " INNER JOIN ".tbl_membervariantprices." as mvp ON mvp.memberid=mp.memberid AND mvp.sellermemberid=".$currentsellerid." AND mvp.priceid=pp.id";
            $this->mainquery .= " WHERE p.producttype=0";

            }else{
            
            $this->mainquery .= " INNER JOIN ".tbl_productprices." as pp ON pp.productid=p.id";
            $this->mainquery .= " WHERE p.producttype=0 AND pp.id IN (IF(".$memberbasicsalesprice."=1,IFNULL((SELECT pbp.productpriceid FROM ".tbl_productbasicpricemapping." as pbp WHERE pbp.productpriceid=pp.id AND pbp.channelid=".$channelid." AND pbp.allowproduct=1 AND pbp.productid=p.id AND pbp.salesprice!=0),0),pp.id))";

            }
           
        }else{

            $this->mainquery .= " INNER JOIN ".tbl_productprices." as pp ON pp.productid=p.id";
            $this->mainquery .= " WHERE p.producttype=0";
        }

		$i = 0;
        foreach ($this->column_search as $item) // loop column 
        {
            if($_POST['search']['value']) // if datatable send POST for search
            {
                
                if($i===0) // first loop
                {
                    $this->mainquery .= " AND (";
                    $this->mainquery .= $item." LIKE '%".$_POST['search']['value']."%'";
                }
                else
                {
                    $this->mainquery .= " OR ".$item." LIKE '%".$_POST['search']['value']."%'";
                }

                if(count($this->column_search) - 1 == $i) //last loop
                    $this->mainquery .= ")"; //close bracket
            }
            $i++;
        }
        
        if(isset($_POST['order'])) { // here order processing
            $this->mainquery .= " ORDER BY ".$this->column_order[$_POST['order']['0']['column']]." ".$_POST['order']['0']['dir'];
        }else if(isset($this->order)) {
            $order = $this->order;
            $this->mainquery .= " ORDER BY ".key($order)." ".$order[key($order)];
        }
    }

    function get_datatables() {
        $this->_get_datatables_query();
        if($_POST['length'] != -1) {
            $this->mainquery .= ") as temp ORDER BY sold DESC";
            $this->mainquery .= " LIMIT ".$_POST['start'].','.$_POST['length'];
            $query = $this->readdb->query($this->mainquery);
            //echo $this->readdb->last_query(); exit;
            return $query->result();
        }
    }

    function count_all() {
        $this->_get_datatables_query();
        $this->mainquery .= ") as temp ORDER BY sold DESC";
        $query = $this->readdb->query($this->mainquery);
        
        return $query->num_rows();
    }

    function count_filtered() {
        $this->_get_datatables_query();
        $this->mainquery .= ") as temp ORDER BY sold DESC";
        $query = $this->readdb->query($this->mainquery);
        return $query->num_rows();
    }
}
        