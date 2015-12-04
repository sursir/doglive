<?php
class Foo
{
    public function __construct(Bar $foo, $arg1 = 1, $arg2 = 2)
    {
        echo 'Foo is instanced', "\n";
    }
}

class Bar
{
    public function __construct(Jor $jor, $arg2 = [1, 2])
    {
        echo 'Bar is instanced', "\n";
    }
}

class Jor
{
    public $val = '11';

    public function __construct()
    {
        echo 'Jor is instanced', "\n";
    }
}

class Di
{
    /** @var array 容器 */
    protected $container = [];

    public function __set($key, $fn){
        $this->container[$key] = $fn;
    }
    public function __get($key){
        return $this->build($this->container[$key]);
    }

    private function build($className){
        if ($className instanceof Closure)
            return $className($this);

        $reflector = new ReflectionClass($className);
        if (! $reflector->isInstantiable()) {
            throw new Exception('不可被实例化');
            return false;
        }

        $constructor = $reflector->getConstructor();

        if (! $constructor)
            return new $className;

        $refParams = $constructor->getParameters();

        return $reflector->newInstanceArgs($this->getParams($refParams));
    }

    private function getParams($refParams){
        $params = [];
        foreach ($refParams as $refParam) {
            if ($depend = $refParam->getClass())
                $params[] = $this->build($depend->name);
            else
                $params[] = $this->getScalarParam($refParam);
        }
        return $params;
    }
    private function getScalarParam($reflectParam){
        if ($reflectParam->isDefaultValueAvailable())
            return $reflectParam->getDefaultValue();
        else
            throw new Exception('Missed Param' . "\n");
    }
}

$di = new Di();

$h = 'hhhhhhhh';
// $di->jor = function() use ($h) {
//     return new Jor();
// };
// var_dump($di->jor);
echo '
';

$di->foo = function($that){
    return new Foo(new Bar(new Jor()), 1, 2);
};

var_dump($di->foo);
