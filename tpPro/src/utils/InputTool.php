<?php

/*
 * This file is part of the php-solution/tp-pro package.
 */

namespace phpSolution\TpPro\utils;

use phpSolution\TpPro\exception\InputArgumentException;

class InputTool
{
    /** @var int 不需要校验 */
    const NOT_VERIFY = -1;

    /**
     * 分页 页数.
     */
    public static function getPage(array $input): int
    {
        $page               = isset($input['page']) ? intval($input['page']) : 1;
        $page <= 0 && $page = 1;

        return $page;
    }

    /**
     * 分页 单页限制.
     */
    public static function getLimit(array $input): int
    {
        $limit               = isset($input['limit']) ? intval($input['limit']) : 10;
        $limit < 0 && $limit = 10;

        return $limit;
    }

    public static function getInt(string $name, array $input, bool $strict, callable $decrypt = null, string $decryptKey = ''): int
    {
        return self::checkInt($name, $input, $strict, $decrypt, $decryptKey);
    }

    /**
     * 公共int(正整数)类型变量校验.
     *
     * @see getProductType
     */
    private static function checkInt(string $type, array $input, bool $strict, callable $decrypt = null, string $decryptKey = ''): int
    {
        // 这里命名以productType为例子
        $productType = $input[$type] ?? null;
        if ($decrypt && isset($productType)) {
            // 解密
            try {
                if ($decryptKey) {
                    $productType = $decrypt($productType, $decryptKey);
                } else {
                    $productType = $decrypt($productType);
                }
            } catch (\Throwable $e) {
                throw new InputArgumentException("{$type}参数解析异常");
            }
        }
        if ($strict) {
            if (is_null($productType)) {
                throw new InputArgumentException("{$type}参数缺失");
            }
            if (!$productType) {
                throw new InputArgumentException("{$type}不能为空类型");
            }
        }

        if ($productType) {
            // 不管严格模式还是非严格模式 获取传入非零参数都会去校验
            if (!is_numeric($productType)) {
                throw new InputArgumentException("{$type}必须是数字类型");
            }
            $productType = intval($productType);
            if ($productType <= 0) {
                throw new InputArgumentException("{$type}必须大于0");
            }

            return $productType;
        }

        return self::NOT_VERIFY;
    }
}
