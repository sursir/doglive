<?php
class CurlMulti
{
    public $mh;
    public $urls = array();
    public $chs = array();
    public $res = array();
    public $req = array();
    public $options = array(
        CURLOPT_TIMEOUT => 60,
        CURLOPT_RETURNTRANSFER => 1,
    );
    public $multiOptions = array();

    public function __construct($urls, $options = array())
    {
        $this->addUrl($urls);
        $this->setOpts($options);
    }

    public function setOpt($curlOpt, $value, $index = false)
    {
        // 为某个请求单独设置 Option
        if ($index !== false)
            $this->req[$index][$curlOpt] = $value;
        else
            $this->options[$curlOpt] = $value;
    }

    public function setOpts($opts, $index = false)
    {
        if ($index !== false) {
            $this->req[$index] = $opts + $this->req[$index];
        } else {
            var_dump($opts, $this->options);
            $this->options = $opts + $this->options;
        }

        var_dump($this->options);
    }

    public function resetOpts($opts)
    {
        $this->options = $opts;
    }

    public function setMultiOpt($curlOpt, $value)
    {
        $this->multiOptions[$curlOpt] = $value;
    }

    public function setMultiOpts($options)
    {
        $this->multiOptions = $options + $this->multiOptions;
    }

    public function setUrl($urls)
    {
        $this->urls = $urls;
    }

    public function addUrl($urls)
    {
        if (is_string($urls)) $this->urls[] = $urls;
        if (is_array($urls))
            $this->urls = array_merge($this->urls, $urls);
    }

    public function emptyUrl()
    {
        $this->urls = array();
    }

    public function _initMh()
    {
        $this->mh = curl_multi_init();
    }
    public function _addCh()
    {
        foreach ($this->urls as $index => $url) {
            $ch = curl_init($url);
            // 为所有ch设置option
            curl_multi_add_handle($this->mh, $ch);
            // 保持映射关系
            $this->chs[$index] = $ch;
        }
    }

    public function _setOption()
    {
        foreach ($this->chs as $index => $ch) {
            // 为所有ch设置option
            curl_setopt_array($ch, $this->options);
            // 单独为每一个ch设置option
            if (isset($this->req[$index]) && ! empty($this->req[$index]))
                curl_setopt_array($ch, $this->req[$index]);
        }
    }

    // 为mh设置选项
    public function _setMultiOption()
    {
        if ((float) phpversion() >= 5.5) {
            foreach ($this->multiOptions as $optKey => $optValue) {
                printf('%s   =>   %s', $optKey, $optValue);
                curl_multi_setopt($this->mh, $optKey, $optValue);
            }
        }
    }

    public function send()
    {
        $running = null;

        do {
            while (CURLM_CALL_MULTI_PERFORM === curl_multi_exec($this->mh, $running));
            if (!$running) break;
            while (($res = curl_multi_select($this->mh)) === 0) {};
            if ($res === false) break;
        } while (true);
    }

    public function getResponse()
    {
        foreach ($this->chs as $index => $ch) {
            $cont = curl_multi_getcontent($ch);
            $chInfo = curl_getinfo($ch);

            $this->res[$index] = array(
                'url' => $this->urls[$index],
                'content' => $cont,
                'contentType' => $chInfo['content_type'],
                'status' => $chInfo['http_code'],
            );
        }
    }

    public function destroyMh()
    {
        foreach ($this->chs as $index => $ch) {
            curl_close($ch);
            curl_multi_remove_handle($this->mh, $ch);
        }
        curl_multi_close($this->mh);
        $this->chs = array();
    }

    public function destroyCh($index)
    {
        curl_close($this->chs[$index]);
        unset($this->chs[$index]);
        curl_multi_remove_handle($this->mh, $this->chs[$index]);
    }

    public function run()
    {
        $this->_initMh();
        $this->_addCh();
        $this->_setOption();
        $this->_setMultiOption();
        $this->send();
        $this->getResponse();
        $this->destroyMh();

        return $this->res;
    }
}
