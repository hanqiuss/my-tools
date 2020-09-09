<?php

/**
 * Class FastCGI
 *
 * @link http://www.mit.edu/~yandros/doc/specs/fcgi-spec.html
 */
class FastCGI
{
    public array $param = [
        'SCRIPT_FILENAME' => '',
        'REQUEST_METHOD'  => 'GET',
        'QUERY_STRING'    => '',
        'CONTENT_TYPE'    => '',
        'CONTENT_LENGTH'  => '',
        'SCRIPT_NAME'     => '',
        'REQUEST_URI'     => '',
        'DOCUMENT_URI'    => '',
        'DOCUMENT_ROOT'   => '',
        'SERVER_NAME'     => '',
        'HTTP_HOST'       => '',
    ];
    public int $reqId = 1;
    public array $cgiResponse = [];
    public string $responseHeader = '';
    public string $responseBody = '';
    
    public function setFileName($filename)
    {
        $filename                       = trim($filename, DIRECTORY_SEPARATOR);
        $this->param['SCRIPT_FILENAME'] = __DIR__ . DIRECTORY_SEPARATOR . $filename;
        $this->param['SCRIPT_NAME']     = '/index.php';
    }
    
    /**
     * @param string $url
     *
     * @return string
     * @throws Exception
     */
    public function get(string $url)
    {
        $parse                       = parse_url($url);
        $this->param['REQUEST_URI']  = $parse['path'];
        $this->param['QUERY_STRING'] = @$parse['query'] ? : '';
        return $this->send();
    }
    
    /**
     * @return string
     * @throws Exception
     */
    public function send()
    {
        $fp = stream_socket_client('tcp://127.0.0.1:9000', $errno, $errStr, 5);
        if ( !$fp) {
            throw new \Exception('connect cgi error');
        }
        $in           = '';
        $param        = new FCGI_Params();
        $param->param = $this->param;
        $in           .= FCGI_Record::makeBeginReq($this->reqId);
        $in           .= FCGI_Record::makeParams($param->toString(), $this->reqId);
        $in           .= FCGI_Record::makeStdIn('', $this->reqId);
        fwrite($fp, $in);
        
        $ret  = FCGI_Record::readAll($fp);
        $data = '';
        foreach ($ret as $record) {
            if ($record->type == FCGI_Record::FCGI_STDERR && $record->contentLength > 0) {
                throw new \Exception('response type error : ' . $record->contentData);
            }
            if ($record->type == FCGI_Record::FCGI_STDOUT) {
                $data .= $record->contentData;
            }
        }
        @list($this->responseHeader, $this->responseBody) = explode("\r\n\r\n", $data, 2);
        return $this->responseBody;
    }
    
}

/**
 * Class FCGI_Record
 *
 * @link http://www.mit.edu/~yandros/doc/specs/fcgi-spec.html#S3.3
 */
class FCGI_Record
{
    const FCGI_BEGIN_REQUEST     = 1;
    const FCGI_ABORT_REQUEST     = 2;
    const FCGI_END_REQUEST       = 3;
    const FCGI_PARAMS            = 4;
    const FCGI_STDIN             = 5;
    const FCGI_STDOUT            = 6;
    const FCGI_STDERR            = 7;
    const FCGI_DATA              = 8;
    const FCGI_GET_VALUES        = 9;
    const FCGI_GET_VALUES_RESULT = 10;
    const FCGI_UNKNOWN_TYPE      = 11;
    
    public int $version = 1;
    public int $type = 0;
    public int $requestId = 0;
    public int $contentLength = 0;
    public int $paddingLength = 0;
    public string $contentData = '';
    public string $paddingData = '';
    
    public function toString(): string
    {
        $this->contentLength = strlen($this->contentData);
        $this->paddingLength = (8 - ($this->contentLength % 8)) % 8;
        $this->paddingData   = str_repeat("\0", $this->paddingLength);
        $header              =
            pack('ccnncc', $this->version, $this->type, $this->requestId, $this->contentLength, $this->paddingLength,
                0);
        return $header . $this->contentData . $this->paddingData;
    }
    
    /**
     * @param resource $fp
     *
     * @return FCGI_Record
     * @throws Exception
     */
    public static function read($fp): FCGI_Record
    {
        $header = fread($fp, 8);
        if ( !$header || strlen($header) < 8) {
            throw new \Exception('read error');
        }
        $r                     = unpack('cversion/ctype/nreq/nlen/cpLen', $header);
        $record                = new static();
        $record->version       = $r['version'];
        $record->type          = $r['type'];
        $record->requestId     = $r['req'];
        $record->contentLength = $r['len'];
        $record->paddingLength = $r['pLen'];
        $len                   = $record->contentLength + $record->paddingLength;
        $data                  = '';
        while ( !feof($fp) && strlen($data) < $len) {
            $data .= fread($fp, $len - strlen($data));
        }
        
        if ( !$data || strlen($data) < $len) {
            var_dump(bin2hex($data));
            throw new \Exception('read error');
        }
        $record->contentData = substr($data, 0, $record->contentLength);
        return $record;
    }
    
    /**
     * @param $fp
     *
     * @return static[]
     * @throws Exception
     */
    public static function readAll($fp): array
    {
        $ret = [];
        while ( !feof($fp)) {
            $ret[] = static::read($fp);
        }
        return $ret;
    }
    
    public static function makeBeginReq(int $id): string
    {
        return static::make(self::FCGI_BEGIN_REQUEST, $id, hex2bin('0001000000000000'));
    }
    
    public static function makeParams(string $data, int $id): string
    {
        $ret = static::make(self::FCGI_PARAMS, $id, $data);
        if ($data) {
            $ret .= static::make(self::FCGI_PARAMS, $id, '');
        }
        return $ret;
    }
    
    public static function makeStdIn(string $data, int $id): string
    {
        $ret = static::make(self::FCGI_STDIN, $id, $data);
        if ($data) {
            $ret .= static::make(self::FCGI_STDIN, $id, '');
        }
        return $ret;
    }
    
    protected static function make(int $type, int $id, string $data): string
    {
        $record              = new FCGI_Record();
        $record->type        = $type;
        $record->requestId   = $id;
        $record->contentData = $data;
        return $record->toString();
    }
}

class FCGI_Params
{
    public array $param = [
        'SCRIPT_FILENAME' => '',
        'REQUEST_METHOD'  => 'GET',
        'QUERY_STRING'    => '',
        'CONTENT_TYPE'    => '',
        'CONTENT_LENGTH'  => '',
        'SCRIPT_NAME'     => '',
        'REQUEST_URI'     => '',
        'DOCUMENT_URI'    => '',
        'DOCUMENT_ROOT'   => '',
        'SERVER_NAME'     => '',
        'HTTP_HOST'       => '',
    ];
    
    public function toString(): string
    {
        $s = '';
        foreach ($this->param as $k => $v) {
            if ( !$v) {
                continue;
            }
            $s .= pack('c', strlen($k));
            if (strlen($v) > 127) {
                $s .= pack('N', (1 << 31) + strlen($v)) . $k . $v;
            } else {
                $s .= pack('c', strlen($v)) . $k . $v;
            }
        }
        return $s;
    }
}
