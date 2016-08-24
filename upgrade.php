<?php 


if(!pdo_fieldexists('hc_tonganbb_exchange_order', 'status')) {
    pdo_query("ALTER TABLE ".tablename('hc_tonganbb_exchange_order')." ADD `status` tinyint(1) DEFAULT NULL COMMENT '3表示未领取，4表示已领取';");
}


