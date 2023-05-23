<?php


class ErrorCode
{
    const OK = [1000, 'ok'];

    const COM_ERR = [1001, '请求异常'];
    const NOT_LOGIN = [1003, '登录状态有误，请重新登录'];
    const NEED_BIND_PHONE = [1401, '需要绑定手机号'];
}