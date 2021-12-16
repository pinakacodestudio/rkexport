<div class="page-content">
    <ol class="breadcrumb">                        
        <?php
            $subid = $this->session->userdata(base_url().'submenuid');
            foreach($subnavtabsmenu as $row){
                if($subid == $row['id']){
          ?>
          <li class="active"><a href="javascript:void(0);"><?=$row['name']; ?></a></li>
          <?php }else{ ?>
          <li class=""><a href="<?=base_url().$row['url']; ?>"><?=$row['name']; ?></a></li>
          <?php } } ?>
    </ol>
    <div class="page-heading">            
        <h1><?=$this->session->userdata(base_url().'submenuname')?></h1>
        <small>
          <ol class="breadcrumb">                        
            <li><a href="<?=base_url(); ?><?=ADMINFOLDER; ?>dashboard">Dashboard</a></li>
            <li><a href="javascript:void(0)"><?=$this->session->userdata(base_url().'mainmenuname')?></a></li>
            <li class="active"><?=$this->session->userdata(base_url().'submenuname')?></li>
          </ol>
        </small>
    </div>

    <div class="container-fluid">
                                    
      <div data-widget-group="group1">
        <div class="row">
          <div class="col-md-12">
            <div class="panel panel-default">
              <div class="panel-heading">

                <div class="col-md-6">
                  <div class="panel-ctrls"></div>
                </div>
                <div class="col-md-6 form-group" style="text-align: right;">
                  <a class="<?=exportbtn_class;?>" href="javascript:void(0)" onclick="exportcustomer()" title="<?=exportbtn_title?>"><?=exportbtn_text;?></a>
                </div>
              </div>
              <div class="panel-body no-padding">
                <table id="customertable" class="table table-striped table-bordered" cellspacing="0" width="100%">
                  <thead>
                    <tr>
                      <th class="width8">Sr.No.</th>
                      <th>Customer Name</th>
                      <th>Mobile No.</th>
                      <th>Email</th>
                      <th>Cart Count</th>
                      <th>Entry Date</th>
                      <th class="width15">Action</th>
                    </tr>
                  </thead>
                  <tbody>
                  </tbody>
                </table>
              </div>
              <div class="panel-footer"></div>
            </div>
          </div>
        </div>
      </div>

    </div> <!-- .container-fluid -->
</div> <!-- #page-content -->