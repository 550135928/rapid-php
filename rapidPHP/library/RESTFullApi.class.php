<?php

namespace rapidPHP\library;


use rapid\library\rapid;

class RESTFullApi
{
    /**
     * @var array 结果
     */
    private $result = [];

    /**
     * @var
     */
    private $dataKey, $codeKey, $msgKey;

    /**
     * 失败状态
     */
    const CODE_FAIL = 0;

    /**
     * 成功状态
     */
    const CODE_SUCCESS = 1;

    /**
     * 构造函数，设置参数
     * api constructor.
     * @param string $dataKey
     * @param string $codeKey
     * @param string $msgKey
     */
    public function __construct($codeKey = 'code', $msgKey = 'msg', $dataKey = 'data')
    {
        $this->setDataKey($dataKey)->setMsgKey($msgKey)->setRetKey($codeKey);
    }

    /**
     * @param array $result
     */
    public function setResult($result)
    {
        if (is_string($result)) {
            $result = json_decode($result, true);

            if (empty($result)) $result = X()->decode($result);
        } else if ($result instanceof AB) {
            $result = $result->getData();
        }

        $this->result = $result;
    }

    /**
     * 设置返回key
     * @param mixed $dataKey
     * @return $this
     */
    public function setDataKey($dataKey)
    {
        $this->dataKey = $dataKey;

        return $this;
    }

    /**
     * 设置返回状态key
     * @param mixed $codeKey
     * @return $this
     */
    public function setRetKey($codeKey)
    {
        $this->codeKey = $codeKey;
        return $this;
    }


    /**
     * 设置返回状态key
     * @param mixed $msgKey
     * @return $this
     */
    public function setMsgKey($msgKey)
    {
        $this->msgKey = $msgKey;
        return $this;
    }


    /**
     * 预设返回值
     * @param string $msg
     * @param string $data
     * @param int $code
     * @return $this
     */
    public function go($msg = '', $data = null, $code = 0)
    {
        $this->setMsg($msg);

        $this->setData($data);

        $this->setCode($code);

        return $this;
    }


    /**
     * 设置返回消息
     * @param $msg
     * @return $this
     */
    public function setMsg($msg)
    {
        $this->result[$this->msgKey] = $msg;

        return $this;
    }


    /**
     * 设置返回状态
     * @param $code
     * @return $this
     */
    public function setCode($code)
    {
        $this->result[$this->codeKey] = $code;

        return $this;
    }

    /**
     * 设置返回状态
     * @param $data
     * @return $this
     */
    public function setData($data)
    {
        if ($data instanceof AB) $data = $data->getData();

        $this->result[$this->dataKey] = $data;

        return $this;
    }


    /**
     * 获取结果状态
     * @param $result
     * @return array|null|string
     */
    public function getCode($result)
    {
        return B()->getData($result, $this->codeKey);
    }

    /**
     * 获取结果消息
     * @param $result
     * @return array|null|string
     */
    public function getMsg($result)
    {
        return B()->getData($result, $this->msgKey);
    }

    /**
     * 获取结果数据
     * @param $result
     * @return array|null|string
     */
    public function getData($result)
    {
        return B()->getData($result, $this->dataKey);
    }


    /**
     * 获取返回值
     * @return array|string
     */
    public function getResult()
    {
        if (!isset($this->result)) $this->result = [$this->codeKey => RESTFullApi::CODE_FAIL, $this->msgKey => 'error', $this->dataKey => null];

        return $this->result;
    }

    /**
     * 转json
     * @return array|string
     */
    public function toJson()
    {
        return json_encode($this->getResult());
    }

    /**
     * 转json
     * @return array|string
     */
    public function toXml()
    {
        return X()->encode($this->getResult());
    }

    /**
     * 错误
     * @param $msg
     * @param int $code
     * @param null $data
     * @return RESTFullApi
     */
    public static function error($msg, $code = self::CODE_FAIL, $data = null)
    {
        return (new RESTFullApi())->go($msg, $data, $code);
    }

    /**
     * 成功
     * @param null $data
     * @param string $msg
     * @param int $code
     * @return RESTFullApi
     */
    public static function success($data = null, $msg = 'success!', $code = self::CODE_SUCCESS)
    {
        return (new RESTFullApi())->go($msg, $data, $code);
    }
}
