<?php

require 'Log.php';
require 'Timer.php';

global $arrWriteHandlers;
$arrWriteHandlers = array(
        '_fwrite',  // tag = e
        '_file_put_contents',  // tag = s
        '_write_with_buf', // tag = f
        );

// Parse command args
if (count($argv) !== 4) {
    usage_exit();
}
$intCnt     = intval($argv[1]);
$intLineLen = intval($argv[2]);
$strMethod  = strval($argv[3]);
if ($intCnt <= 0 || $intLineLen <= 0 || !$strMethod) {
    usage_exit();
}

// Prepare unique pad
$strTag = substr($strMethod, -1);

// Output log, and record timecost
Base_Util_Timer::start($strMethod);
real_log($intCnt, $intLineLen, $strTag, $strMethod);
$intUsed = Base_Util_Timer::stop($strMethod);
printf("%-30s\t%d\n", $strMethod, $intUsed);

function usage_exit()
{
    global $argv;
    global $arrWriteHandlers;
    fprintf(STDERR, "Usage: %s <intCnt> <intLineLen> <write_handler>\n", $argv[0]);
    fprintf(STDERR, "\t\twrite_handler: %s\n", implode(' ', $arrWriteHandlers));
    exit(1);
}

function pad($intLineLen, $strTag)
{
    return str_pad("", $intLineLen, $strTag);
}

function real_log($intCnt, $intLineLen, $strTag, $strLogHandler) 
{
    // prepare log content
    $strLine = pad($intLineLen, $strTag);

    // config loghandler
    Base_Log::setConfigs(array(
                'level'         => Base_Log::ALL & ~Base_Log::DEBUG, // 线上关闭Debug
                'logdir'        => './log',
                'writehandler'  => $strLogHandler,
                'ucid'          => 1234567,
                'ignorepath'    => dirname(dirname(__FILE__)).'/',
                'writebufmax'   => 1000,
                ));


    // write log
    while ($intCnt--) {
        Base_Log::notice($strLine);
    }
}
