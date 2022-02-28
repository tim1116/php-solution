<?php

// 二叉搜索树
$autoload = include "autoload.php";
call_user_func($autoload);

$rootNode = new Node(new IntValue(1));
$tree     = new BinaryTree($rootNode);

$insert1 = new Node(new IntValue(3));
$tree->insert($insert1);

$insert1 = new Node(new IntValue(5));
$tree->insert($insert1);

$insert1 = new Node(new IntValue(4));
$tree->insert($insert1);

$insert1 = new Node(new IntValue(-10));
$tree->insert($insert1);

$insert1 = new Node(new IntValue(-5));
$tree->insert($insert1);


//---------------------
//           1
//       -10   3
//         -5  4  5


// 遍历
$arr = [];
$tree->foreachNode($tree->root, $arr);
var_dump($arr);

$search1 = new Node(new IntValue(-5));
$search2 = new Node(new IntValue(-50));
var_dump($tree->searchNode($search1));
var_dump($tree->searchNode($search2));