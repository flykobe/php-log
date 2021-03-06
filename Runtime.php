<?php

/**
 * Lib层抛出的异常
 *
 * 该异常业务无关，包括网络交互、DB交互、内存不足等
 * 该异常被下游或用户视为系统错误
 *
 * @author chengyi02
 */


class Base_Exception_Runtime extends Exception {

    public function __construct($strMsg, $intCode = Base_ErrDef::DEFAULT_FAIL) {
        parent::__construct($strMsg, $intCode);
    }
}
