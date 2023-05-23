<?php

/*
 * This file is part of the php-solution/tp-pro package.
 */

namespace phpSolution\TpPro\traits;

use phpSolution\TpPro\exception\InputArgumentException;
use think\facade\Db;
use think\model;

trait ServiceTool
{
    public static $MOVE_TYPE_UP   = 1;
    public static $MOVE_TYPE_DOWN = 2;

    /**
     * 是否使用缓存.
     *
     * @var bool
     */
    protected $useCache = true;

    /**
     * @var mixed 字段信息
     */
    protected $forceField = '';

    public static function MOVE_TYPE(): array
    {
        return [
            self::$MOVE_TYPE_UP   => '上移',
            self::$MOVE_TYPE_DOWN => '下移',
        ];
    }

    public function setForceField($field): void
    {
        $this->forceField = $field;
    }

    public function setUseCache(bool $useCache): self
    {
        $this->useCache = $useCache;

        return $this;
    }

    /**
     * 后台更新显示状态
     *
     * @param string $field 需要更新的字段
     *
     * @return int|null
     */
    public function editShowInAdmin(model $model, ?int $isShow, string $field = 'is_show'): ?bool
    {
        $show = $model->$field;
        if (!in_array($field, $model->checkAllowFields())) {
            throw new \InvalidArgumentException('更新显示/隐藏出错-字段不存在');
        }

        if (null === $isShow) {
            $show = $show ^ 1;
        } elseif (in_array($isShow, [0, 1])) {
            $show = $isShow;
        } else {
            $this->errorMsg = '更新成参数异常';

            return null;
        }
        $model->$field = $show;
        $model->save();

        return $show;
    }

    /**
     * 后台上移下移 - 数字越大越靠上.
     *
     * @param int $type 1 上移 2 下移
     */
    public function moveInAdmin(model $model, int $type, string $field = 'sort'): ?bool
    {
        if (!isset(self::MOVE_TYPE()[$type])) {
            throw new InputArgumentException('移动字段-type参数异常');
        }
        if (!in_array($field, $model->checkAllowFields())) {
            throw new \InvalidArgumentException('移动字段-字段不存在');
        }

        Db::startTrans();
        try {
            if ($type == self::$MOVE_TYPE_UP) {
                // 上移
                $next = $model->where('sort', '>', $model->$field)->order('sort', 'ASC')->find();
                if (empty($next)) {
                    throw new InputArgumentException('已经是最上层');
                }
                $nextSort      = $next->$field;
                $next->$field  = $model->$field;
                $model->$field = $nextSort;
                $model->save();
                $next->save();
            } else {
                // 下移
                $next = $model->where('sort', '<', $model->$field)->order('sort', 'DESC')->find();
                if (empty($next)) {
                    throw new InputArgumentException('已经是最下层');
                }
                $nextSort      = $next->$field;
                $next->$field  = $model->$field;
                $model->$field = $nextSort;
                $model->save();
                $next->save();
            }
            Db::commit();
        } catch (\Exception $e) {
            Db::rollback(); // 回滚事务
            if ($e instanceof InputArgumentException) {
                throw $e;
            }
            $this->errorMsg = $e->getMessage();

            return null;
        }

        return true;
    }
}
