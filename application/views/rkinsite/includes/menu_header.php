<div class="btn-group dropdown dropdown-l dropdown-breadcrumbs">
      <a class="dropdown-toggle dropdown-toggle-style" data-toggle="dropdown" aria-expanded="false"><span>
          <i class="material-icons" style="font-size: 26px;">menu</i>
        </span> </a>
      <ul class="dropdown-menu dropdown-tl" role="menu">
        <label class="mt-sm ml-sm mb-n">Menu</label>
        <?php
            $subid = $this->session->userdata(base_url().'submenuid');
            $thirdsubid = $this->session->userdata(base_url().'thirdlevelsubmenuid');
            foreach($subnavtabsmenu as $row){
              if($row['url']!=''){
                if($subid == $row['id']){ ?>
                  <li class="active"><a href="javascript:void(0);"><i class="fa fa-caret-right mr-sm"></i> <?=$row['name']; ?></a></li>
                <?php }else{ ?>
                  <li><a href="<?=base_url().ADMINFOLDER.$row['url']; ?>"><i class="fa fa-caret-right mr-sm"></i> <?=$row['name']; ?></a></li>
                <?php } }else{ ?>
                  <li class=<?=($subid==$row['id']?'active':'')?>><a><i class="fa fa-caret-right mr-sm"></i> <?=$row['name']; ?></a>
                    <ul class="dropdown-menu dropdown-tl" style="left: 225px !important; top:0px;">
                        <?php foreach($thirdlevelsubnavtabsmenu as $trow){
                            if($row['id']==$trow['submenuid']){
                           
                            if($thirdsubid==$trow['id']){ ?>
                              <li class="active"><a href="<?=base_url().ADMINFOLDER.$trow['url']; ?>"><i class="fa fa-caret-right mr-sm"></i> <?=$trow['name']; ?></a></li>
                            <?php }else{
                           ?> 
                              <li><a href="<?=base_url().ADMINFOLDER.$trow['url']; ?>"><i class="fa fa-caret-right mr-sm"></i> <?=$trow['name']; ?></a></li>
                        <?php } } } ?>
                    </ul>
                  </li>
                <?php }
            } ?>
      </ul>
    </div>
    <h1><?=$this->session->userdata(base_url() . 'thirdlevelsubmenuname')!=''?$this->session->userdata(base_url() . "thirdlevelsubmenuname"):$this->session->userdata(base_url() . 'submenuname') ?></h1>
    <small>
      <ol class="breadcrumb">
        <li><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Dashboard</a></li>
        <li><a href="javascript:void(0)"><?= $this->session->userdata(base_url() . 'mainmenuname') ?></a></li>
        <li class="<?=$this->session->userdata(base_url() . 'thirdlevelsubmenuname')==''?'active':''?>"><?= $this->session->userdata(base_url() . 'submenuname') ?></li>
        <?=$this->session->userdata(base_url() . 'thirdlevelsubmenuname')!=''?'<li class="active">'.$this->session->userdata(base_url() . "thirdlevelsubmenuname").'</li>':''?>
      </ol>
    </small>