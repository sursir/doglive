<?php

// class Bar implements IteratorAggregate
// {
//     private function foo()
//     {
//         $i = 5;
//         while ($i--)
//             yield $i;
//     }

//     public function getIterator()
//     {
//         return $this->foo();
//     }
// }

// $bar = new Bar();
// foreach ($bar as $item) {
//     var_dump($item);
// }

function gen()
{
    $i = 1;
    while (true) {
        yield $i += yield;
        echo $i, "\n";
    }
}

$gen = gen();
var_dump($gen->send(3));
$gen->next();
echo "----\n";
var_dump($gen->send(4));
$gen->next();
echo "----\n";
$gen->send(5);
$gen->next();
echo "----\n";
$gen->send(6);
$gen->next();
echo "----\n";
$gen->send(7);
$gen->next();
echo "----\n";

