<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

ERROR - 2021-12-16 16:19:01 --> 404 Page Not Found: 
ERROR - 2021-12-16 16:19:09 --> 404 Page Not Found: 
ERROR - 2021-12-16 16:24:27 --> Query error: Table 'rkexport.frontendmenu' doesn't exist - Invalid query: SELECT `fm`.`id`, `fm`.`name`, IF(fm.url='', `mwc`.`slug`, fm.url) as url, `fm`.`menuicon`, `fm`.`coverimage`, IFNULL((SELECT 1 FROM frontendsubmenu as fsm WHERE fsm.frontendmenuid=fm.id AND fsm.status=1 LIMIT 1), 0) as submenuavailable
FROM `frontendmenu` as `fm`
LEFT JOIN `managewebsitecontent` as `mwc` ON `mwc`.`frontendmenuid`=`fm`.`id` AND `mwc`.`frontendsubmenuid`=0
WHERE `fm`.`status` = 1 AND `fm`.`channelid` = '0' AND `fm`.`memberid` = '0'
ORDER BY `fm`.`priority` ASC
ERROR - 2021-12-16 16:24:33 --> Query error: Table 'rkexport.frontendmenu' doesn't exist - Invalid query: SELECT `fm`.`id`, `fm`.`name`, IF(fm.url='', `mwc`.`slug`, fm.url) as url, `fm`.`menuicon`, `fm`.`coverimage`, IFNULL((SELECT 1 FROM frontendsubmenu as fsm WHERE fsm.frontendmenuid=fm.id AND fsm.status=1 LIMIT 1), 0) as submenuavailable
FROM `frontendmenu` as `fm`
LEFT JOIN `managewebsitecontent` as `mwc` ON `mwc`.`frontendmenuid`=`fm`.`id` AND `mwc`.`frontendsubmenuid`=0
WHERE `fm`.`status` = 1 AND `fm`.`channelid` = '0' AND `fm`.`memberid` = '0'
ORDER BY `fm`.`priority` ASC
ERROR - 2021-12-16 16:24:41 --> Query error: Table 'rkexport.frontendmenu' doesn't exist - Invalid query: SELECT `fm`.`id`, `fm`.`name`, IF(fm.url='', `mwc`.`slug`, fm.url) as url, `fm`.`menuicon`, `fm`.`coverimage`, IFNULL((SELECT 1 FROM frontendsubmenu as fsm WHERE fsm.frontendmenuid=fm.id AND fsm.status=1 LIMIT 1), 0) as submenuavailable
FROM `frontendmenu` as `fm`
LEFT JOIN `managewebsitecontent` as `mwc` ON `mwc`.`frontendmenuid`=`fm`.`id` AND `mwc`.`frontendsubmenuid`=0
WHERE `fm`.`status` = 1 AND `fm`.`channelid` = '0' AND `fm`.`memberid` = '0'
ORDER BY `fm`.`priority` ASC
