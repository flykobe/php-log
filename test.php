<?php

require 'Log.php';

Base_Log::setConfigs(array(
        'level'         => Base_Log::ALL & ~Base_Log::DEBUG, // 线上关闭Debug
        'logdir'        => './log',
        'writehandler'  => '_write_with_buf',
        'ucid'          => 1234567,
        'ignorepath'    => dirname(dirname(__FILE__)).'/',
));


Base_Log::error("I am error");
