<?php

class BinaryTree
{
    /**
     * 根节点
     * @var Node
     */
    public $root;

    /**
     * 节点数
     * @var int
     */
    public $count = 0;


    public function __construct(Node $root)
    {
        $this->root = $root;
        $this->count++;
    }


    /**
     * 插入一个节点 返回父节点
     * @param Node $node
     * @return Node|null
     */
    public function insert(Node $node): ?Node
    {
        return $this->check($this->root, $node);
    }


    /**
     * 遍历插入
     * @param Node $root 父节点
     * @param Node $node 插入节点
     * @return Node|null  父节点
     */
    private function check(Node $root, Node $node): ?Node
    {
        if ($root->value->compare($node->value) > 0) {
            // root 节点大一些 插入左边
            if (empty($root->left)) {
                $root->left   = $node;
                $node->parent = $root;
                $this->count++;
                return $root;
            } else {
                return $this->check($root->left, $node);
            }
        } elseif ($root->value->compare($node->value) == 0) {
            // 相等
            return null;
        } else {
            // root 节点小一些 插入右边
            if (empty($root->right)) {
                $root->right  = $node;
                $node->parent = $root;
                $this->count++;
                return $root;
            } else {
                return $this->check($root->right, $node);
            }
        }
    }

    /**
     * 遍历 从根节点出发开始遍历
     *
     * 左节点->中间节点->右节点
     *
     */
    public function foreachNode(Node $node, array &$arr): void
    {
        if ($node->left) {
            $this->foreachNode($node->left, $arr);
        }
        array_push($arr, $node->value->getValue());
        if ($node->right) {
            $this->foreachNode($node->right, $arr);
        }
    }

    /**
     * 查找节点
     * @param Node $node
     * @param Node|null $root
     * @return bool
     */
    public function searchNode(Node $node, Node $root = null): bool
    {
        $root === null && $root = $this->root;

        if ($node->value->compare($root->value) > 0) {
            // 当前大 查找右边
            if (empty($root->right)) {
                return false;
            }
            return $this->searchNode($node, $root->right);
        } elseif ($node->value->compare($root->value) === 0) {
            return true;
        } else {
            // 当前小
            if (empty($root->left)) {
                return false;
            }
            return $this->searchNode($node, $root->left);
        }
    }

}