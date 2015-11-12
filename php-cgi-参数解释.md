CGI / PHP-SERVER 参数解释
=================

### 关于 fast-cgi

> CGI(Common Gateway Interface) 公共网关接口

> PHP 是一种后端程序处理语言, 无法接受客户端发送的请求, 则需要Nginx, apached等
> 服务器来接受请求并作出一系列的处理, 然后交给后端处理语言。
> 那么问题来了, 服务器和后端程序处理语言的开发语言不一定相同。那么他们之间怎么交换
> 数据, 进行通信呢。
> 所以引入了 *`CGI`* 这样一个概念。它可以将服务器接收到的请求转换为一种通用格式
> 然后发送给后端程序语言。而后端程序语言进行处理之后再将结果返回到服务器, 由服务器
> 再返回给用户


> *`FastCGI`* 可以加速 *`CGI`*处理的一种协议

> 在不使用 *`FastCGI`* 的情况下, 每开启一个连接就要进行一次环境的`初始化`与`回收`
> 工作, 这样在连接数较多的情况下对于资源的消耗是巨大的。会造成资源瓶颈。降低了服务器
> 的性能。所以出现了一种 *`FastCGI`*协议。它可以保存环境, 避免了每次连接都要进行环境
> 载入回收, 降低了每次请求的消耗。

### CGI 通信中的参数

*Param_name* | *Param_value (at Nginx)* | *Comment*
------------ | ------------ | -----------
`SCRIPT_FILENAME`    | `$document_root$fastcgi_script_name` | 最后访问的脚本绝对路径 (完整路径)
`QUERY_STRING`       | `$query_string` | 请求参数 (/\?.*/)
`REQUEST_METHOD`     | `$request_method` | 请求方法 (Get \| Post \| Put ...)
`CONTENT_TYPE `      | `$content_type` | 请求内容类型 (Content-type: text/plain ...)
`CONTENT_LENGTH`     | `$content_length` | 请求内容长度
                     |
`SCRIPT_NAME`        | `$fastcgi_script_name` | 最后请求的脚本地址 (redirect之后 域名之后的部分)
`REQUEST_URI`        | `$request_uri` | 用户最初请求的 URI (域名之后的部分)
`DOCUMENT_URI`       | `$document_uri` | 最后请求的脚本地址 (目测和SCRIPT_NAME 没什么不同)
`DOCUMENT_ROOT`      | `$document_root` | 根目录
`SERVER_PROTOCOL`    | `$server_protocol` | 协议类型
`HTTPS`              | `$https if_not_empty` | 是否为HTTPS
                     |
`GATEWAY_INTERFACE`  | `CGI/1.1` | 网关接口
`SERVER_SOFTWARE`    | `nginx/$nginx_version` | 服务器
                     |
`REMOTE_ADDR`        | `$remote_addr` | 远程地址
`REMOTE_PORT`        | `$remote_port` | 远程端口
`SERVER_ADDR`        | `$server_addr` | 服务器地址
`SERVER_PORT`        | `$server_port` | 服务器端口
`SERVER_NAME`        | `$server_name` | SERVER NAME
                     |
`# PHP only, required if PHP was built with --enable-force-cgi-redirect` |
`REDIRECT_STATUS`    | 200 | 重定向状态

### 比较

###### 在PHP中
最初要看这部分的主要原因是想弄清楚PHP中获取当前文件的路径(included 文件的路径)
和 执行路径分别的表示方法。

得出结果:

1. **`__DIR__`** 与 **`__FILE__`** **当前**文件的路径与文件名 (绝对路径)

2. **`$_SERVER['SCRIPT_FILENAME']`** **执行**文件的文件名 (绝对路径 带DOCUMENT_ROOT)

3. **`$_SERVER['ORIG_SCRIPT_FILENAME']`** **原始发送给 CGI**请求文件名
    (带`DOCUMENT_ROOT`
     会经过CGI解析然后得到真正的 `SCRIPT_FILENAME`。
     即如果Nginx给的**`SCRIPT_FILENAME`**被CGI再次解析,
     那么`$_SERVER['SCRIPT_FILENAME']`会变为CGI解析后的文件真实地址,
     而Nginx给的值将会被赋予`$_SERVER['ORIG_SCRIPT_FILENAME']`)

4. **`$_SERVER['SCRIPT_NAME']`** **执行**文件的文件名 (不带DOCUENT_ROOT)

5. **`$_SERVER['PHP_SELF']`**,**`$_SERVER['DOCUMENT_URI']`**
   与 *`$_SERVER['SCRIPT_NAME']`*相同 (暂时来看 没有理论依据)

6. **`$_SERVER['REQUEST_URI']`** **用户最初请求**的URI地址 (不带HOST)

7. **`$_SERVER['DOCUMET_URI']`** **用户最终请求**的URI地址 (不带HOST)

### 总结

* 当需要用到执行文件路径的时候用 `$_SERVER['SCRIPT_FILENAME']`

* 当前文件路径用 `__DIR__`, `__FILE__`
