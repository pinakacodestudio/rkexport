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
ERROR - 2021-12-16 18:15:01 --> Severity: Warning --> mysqli::real_connect(): php_network_getaddresses: getaddrinfo failed: No such host is known.  D:\xampp\htdocs\rkexport\system\database\drivers\mysqli\mysqli_driver.php 203
ERROR - 2021-12-16 18:15:01 --> Severity: Warning --> mysqli::real_connect(): (HY000/2002): php_network_getaddresses: getaddrinfo failed: No such host is known.  D:\xampp\htdocs\rkexport\system\database\drivers\mysqli\mysqli_driver.php 203
ERROR - 2021-12-16 18:15:01 --> Unable to connect to the database
ERROR - 2021-12-16 18:17:37 --> Query error: Table 'rkexport.frontendmenu' doesn't exist - Invalid query: SELECT `fm`.`id`, `fm`.`name`, IF(fm.url='', `mwc`.`slug`, fm.url) as url, `fm`.`menuicon`, `fm`.`coverimage`, IFNULL((SELECT 1 FROM frontendsubmenu as fsm WHERE fsm.frontendmenuid=fm.id AND fsm.status=1 LIMIT 1), 0) as submenuavailable
FROM `frontendmenu` as `fm`
LEFT JOIN `managewebsitecontent` as `mwc` ON `mwc`.`frontendmenuid`=`fm`.`id` AND `mwc`.`frontendsubmenuid`=0
WHERE `fm`.`status` = 1 AND `fm`.`channelid` = '0' AND `fm`.`memberid` = '0'
ORDER BY `fm`.`priority` ASC
ERROR - 2021-12-16 18:19:10 --> Query error: Table 'rkexport.frontendmenu' doesn't exist - Invalid query: SELECT `fm`.`id`, `fm`.`name`, IF(fm.url='', `mwc`.`slug`, fm.url) as url, `fm`.`menuicon`, `fm`.`coverimage`, IFNULL((SELECT 1 FROM frontendsubmenu as fsm WHERE fsm.frontendmenuid=fm.id AND fsm.status=1 LIMIT 1), 0) as submenuavailable
FROM `frontendmenu` as `fm`
LEFT JOIN `managewebsitecontent` as `mwc` ON `mwc`.`frontendmenuid`=`fm`.`id` AND `mwc`.`frontendsubmenuid`=0
WHERE `fm`.`status` = 1 AND `fm`.`channelid` = '0' AND `fm`.`memberid` = '0'
ORDER BY `fm`.`priority` ASC
ERROR - 2021-12-16 18:19:49 --> Severity: Warning --> A non-numeric value encountered D:\xampp\htdocs\rkexport\application\models\Dashboard_model.php 120
ERROR - 2021-12-16 18:19:51 --> Query error: Table 'rkexport.frontendmenu' doesn't exist - Invalid query: SELECT `fm`.`id`, `fm`.`name`, IF(fm.url='', `mwc`.`slug`, fm.url) as url, `fm`.`menuicon`, `fm`.`coverimage`, IFNULL((SELECT 1 FROM frontendsubmenu as fsm WHERE fsm.frontendmenuid=fm.id AND fsm.status=1 LIMIT 1), 0) as submenuavailable
FROM `frontendmenu` as `fm`
LEFT JOIN `managewebsitecontent` as `mwc` ON `mwc`.`frontendmenuid`=`fm`.`id` AND `mwc`.`frontendsubmenuid`=0
WHERE `fm`.`status` = 1 AND `fm`.`channelid` = '0' AND `fm`.`memberid` = '0'
ORDER BY `fm`.`priority` ASC
ERROR - 2021-12-16 18:19:51 --> Query error: Table 'rkexport.frontendmenu' doesn't exist - Invalid query: SELECT `fm`.`id`, `fm`.`name`, IF(fm.url='', `mwc`.`slug`, fm.url) as url, `fm`.`menuicon`, `fm`.`coverimage`, IFNULL((SELECT 1 FROM frontendsubmenu as fsm WHERE fsm.frontendmenuid=fm.id AND fsm.status=1 LIMIT 1), 0) as submenuavailable
FROM `frontendmenu` as `fm`
LEFT JOIN `managewebsitecontent` as `mwc` ON `mwc`.`frontendmenuid`=`fm`.`id` AND `mwc`.`frontendsubmenuid`=0
WHERE `fm`.`status` = 1 AND `fm`.`channelid` = '0' AND `fm`.`memberid` = '0'
ORDER BY `fm`.`priority` ASC
ERROR - 2021-12-16 18:23:19 --> Query error: Table 'rkexport.frontendmenu' doesn't exist - Invalid query: SELECT `fm`.`id`, `fm`.`name`, IF(fm.url='', `mwc`.`slug`, fm.url) as url, `fm`.`menuicon`, `fm`.`coverimage`, IFNULL((SELECT 1 FROM frontendsubmenu as fsm WHERE fsm.frontendmenuid=fm.id AND fsm.status=1 LIMIT 1), 0) as submenuavailable
FROM `frontendmenu` as `fm`
LEFT JOIN `managewebsitecontent` as `mwc` ON `mwc`.`frontendmenuid`=`fm`.`id` AND `mwc`.`frontendsubmenuid`=0
WHERE `fm`.`status` = 1 AND `fm`.`channelid` = '0' AND `fm`.`memberid` = '0'
ORDER BY `fm`.`priority` ASC
ERROR - 2021-12-16 18:23:19 --> Query error: Table 'rkexport.frontendmenu' doesn't exist - Invalid query: SELECT `fm`.`id`, `fm`.`name`, IF(fm.url='', `mwc`.`slug`, fm.url) as url, `fm`.`menuicon`, `fm`.`coverimage`, IFNULL((SELECT 1 FROM frontendsubmenu as fsm WHERE fsm.frontendmenuid=fm.id AND fsm.status=1 LIMIT 1), 0) as submenuavailable
FROM `frontendmenu` as `fm`
LEFT JOIN `managewebsitecontent` as `mwc` ON `mwc`.`frontendmenuid`=`fm`.`id` AND `mwc`.`frontendsubmenuid`=0
WHERE `fm`.`status` = 1 AND `fm`.`channelid` = '0' AND `fm`.`memberid` = '0'
ORDER BY `fm`.`priority` ASC
ERROR - 2021-12-16 18:23:31 --> Query error: Table 'rkexport.frontendmenu' doesn't exist - Invalid query: SELECT `fm`.`id`, `fm`.`name`, IF(fm.url='', `mwc`.`slug`, fm.url) as url, `fm`.`menuicon`, `fm`.`coverimage`, IFNULL((SELECT 1 FROM frontendsubmenu as fsm WHERE fsm.frontendmenuid=fm.id AND fsm.status=1 LIMIT 1), 0) as submenuavailable
FROM `frontendmenu` as `fm`
LEFT JOIN `managewebsitecontent` as `mwc` ON `mwc`.`frontendmenuid`=`fm`.`id` AND `mwc`.`frontendsubmenuid`=0
WHERE `fm`.`status` = 1 AND `fm`.`channelid` = '0' AND `fm`.`memberid` = '0'
ORDER BY `fm`.`priority` ASC
ERROR - 2021-12-16 18:23:31 --> Query error: Table 'rkexport.frontendmenu' doesn't exist - Invalid query: SELECT `fm`.`id`, `fm`.`name`, IF(fm.url='', `mwc`.`slug`, fm.url) as url, `fm`.`menuicon`, `fm`.`coverimage`, IFNULL((SELECT 1 FROM frontendsubmenu as fsm WHERE fsm.frontendmenuid=fm.id AND fsm.status=1 LIMIT 1), 0) as submenuavailable
FROM `frontendmenu` as `fm`
LEFT JOIN `managewebsitecontent` as `mwc` ON `mwc`.`frontendmenuid`=`fm`.`id` AND `mwc`.`frontendsubmenuid`=0
WHERE `fm`.`status` = 1 AND `fm`.`channelid` = '0' AND `fm`.`memberid` = '0'
ORDER BY `fm`.`priority` ASC
ERROR - 2021-12-16 18:23:32 --> Query error: Table 'rkexport.productquantityprices' doesn't exist - Invalid query: SELECT `p`.`id`, `pp`.`id` as `priceid`, `pc`.`name` as `categoryname`, `p`.`name`, `description`, IFNULL((select name from brand where id=p.brandid), "-") as brandname, `p`.`createddate`, `p`.`priority`, `p`.`status`, `isuniversal`, (SELECT GROUP_CONCAT(pc.variantid) FROM productcombination as pc INNER JOIN productprices as pp on pp.id=pc.priceid WHERE pp.productid=p.id) as variantid, `p`.`quantitytype`, `p`.`priority` as `productpriority`, IFNULL((select filename from productimage where productid=p.id limit 1), "") as productimage, 0 as `salesprice`, (SELECT max(pqp.price) FROM productquantityprices as pqp WHERE pqp.productpricesid IN (SELECT id FROM productprices as pp where pp.productid=p.id))  as maxprice, (SELECT min(pqp.price) FROM productquantityprices as pqp WHERE pqp.productpricesid IN (SELECT id FROM productprices as pp where pp.productid=p.id))  as minprice
FROM `product` as `p`
INNER JOIN `productcategory` as `pc` ON `pc`.`id`=`p`.`categoryid`
INNER JOIN `productprices` as `pp` ON `pp`.`productid`=`p`.`id`
WHERE `p`.`memberid` = '0' AND `p`.`channelid` = '0'
AND (FIND_IN_SET(p.categoryid, '') >0 OR '' = '')
AND (FIND_IN_SET(p.brandid, '') >0 OR '' = '')
AND (`p`.`producttype` = '0' OR '0' = '0')
GROUP BY `p`.`id`
ORDER BY `p`.`id` DESC
 LIMIT 50
ERROR - 2021-12-16 18:54:30 --> Query error: Table 'rkexport.frontendmenu' doesn't exist - Invalid query: SELECT `fm`.`id`, `fm`.`name`, IF(fm.url='', `mwc`.`slug`, fm.url) as url, `fm`.`menuicon`, `fm`.`coverimage`, IFNULL((SELECT 1 FROM frontendsubmenu as fsm WHERE fsm.frontendmenuid=fm.id AND fsm.status=1 LIMIT 1), 0) as submenuavailable
FROM `frontendmenu` as `fm`
LEFT JOIN `managewebsitecontent` as `mwc` ON `mwc`.`frontendmenuid`=`fm`.`id` AND `mwc`.`frontendsubmenuid`=0
WHERE `fm`.`status` = 1 AND `fm`.`channelid` = '0' AND `fm`.`memberid` = '0'
ORDER BY `fm`.`priority` ASC
ERROR - 2021-12-16 18:54:30 --> Query error: Table 'rkexport.frontendmenu' doesn't exist - Invalid query: SELECT `fm`.`id`, `fm`.`name`, IF(fm.url='', `mwc`.`slug`, fm.url) as url, `fm`.`menuicon`, `fm`.`coverimage`, IFNULL((SELECT 1 FROM frontendsubmenu as fsm WHERE fsm.frontendmenuid=fm.id AND fsm.status=1 LIMIT 1), 0) as submenuavailable
FROM `frontendmenu` as `fm`
LEFT JOIN `managewebsitecontent` as `mwc` ON `mwc`.`frontendmenuid`=`fm`.`id` AND `mwc`.`frontendsubmenuid`=0
WHERE `fm`.`status` = 1 AND `fm`.`channelid` = '0' AND `fm`.`memberid` = '0'
ORDER BY `fm`.`priority` ASC
