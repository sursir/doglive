<?php

/** CURRY */
/**
 * 未指定$fn怎样使用$data, 硬化了入参顺序
 * 还是硬化编码
 *
 * @param  function $fn   主要实现逻辑的函数
 * @param  mixed $data 数据 预加载数据
 * @return function       得到一个扩展函数
 */
function curry($fn, $data)
{
    return function ($argv) use ($data, $fn) {
        return $fn($data, $argv);
    };
}

// 一次性 curry
// $increment = function ($n) {
//     return add(1, $n);
// };

$increment = curry('add', 1);

echo $increment(1);
echo $increment($increment($increment(3)));



function map($fn, $a)
{
    $newA = array();
    foreach ($a as $key => $val) {
        $newA[] = $fn($val, $key);
    }

    return $newA;
}


$love = array(
    'I' => 'me',
    'Love' => 'miss',
    'You' => 'aar',
);
$tell = function($val, $key){
    echo $key, ' => ', $val, "\n";
};

map($tell, $love);

$increment = curry('map', function($val, $key) use ($tell) {
    $val .= ' more';
    $tell($val, $key);
});


$increment($love);


// ***************************
//  NEW CURRY (For me)
// ***************************
function newCurry($fn, $data)
{
    return function ($argv) use ($data, $fn) {
        return $fn();
    };
}

/** demo 1: */
$match = newCurry(function($match, $data){
    preg_match($match, $data, $results);
    return $results;
}, '/\s+/');

// $match('/\s+/')('hello, world');


/** demo 2: */

function add($a, $b)
{
    return $a + $b;
}

$curriedAdd = newCurry(function($increment, $data){
    return add($increment, $data);
}, 1);

// echo $curriedAdd(0)(1);



