<?php
ini_set('memory_limit', '512M');
require __DIR__ . "/../vendor/autoload.php";

use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;


/*$whoops = new Whoops\Run();
$whoops->pushHandler(new Whoops\Handler\PrettyPageHandler());
$errorPage = new Whoops\Handler\PrettyPageHandler();
$errorPage->setPageTitle("Oops, it's broken!"); // Set the page's title
$errorPage->setEditor("sublime");         // Set the editor used for the "Open" link
$whoops->pushHandler($errorPage);
// Set Whoops as the default error and exception handler used by PHP:
$whoops->register();*/

/**
 * Starting F3
 */
$f3 = Base::instance();
$f3->set('CACHE', $f3->get('cfgCache'));


$f3->config(__DIR__ . '/../config/config.ini');
$f3->config(__DIR__ . '/../config/routes.ini');

$f3->set('DEBUG',3);

$db = new \DB\SQL(
    $f3->get('cfgDbConnection'),
    $f3->get('cfgDbUser'),
    $f3->get('cfgDbPassword')
);
$f3->set('DB',$db);
new DB\SQL\Session($db);


try {
    $models = Yaml::parse(file_get_contents(__DIR__ . '/../config/model.yml'));
    $services = Yaml::parse(file_get_contents(__DIR__ . '/../config/service.yml'));

    /**
     * Load Models
     */

    foreach ($models as $name => $classe) {
        $f3->set($name, new $classe($db));
    }

    $f3->set('logger', new \Log( '../var/log/log-' . date('d-m-Y') . '.log'));

    /**
     * Load services
     */

    foreach ($services as $name => $classe) {
        $f3->set($name, new $classe($f3));
    }

} catch (ParseException $e) {
    dump("Unable to parse the YAML string: %s", $e->getMessage());
} catch(\Exception $e)
{
    dump( $e->getMessage());
}



$f3->set('auth', new \Auth ($f3->get('model.administrateur'), array('id' => 'email', 'pw' => 'mot_passe')));
$f3->set('service.csv', new Services\CSV($f3));

$f3->set('ONERROR',function($f3) {
    while (ob_get_level())
        ob_end_clean();
    
        $f3->get('logger')->write("***********************************");
    $f3->get('logger')->write($f3->get('ERROR.text'));
    $f3->get('logger')->write($f3->get('DB')->log());
    dump("this is a custom error page. In Dev environment");
    dump("text: " . $f3->get('ERROR.text'));
    dump("status: " . $f3->get('ERROR.status'));
    dump("trace: " . $f3->get('ERROR.trace'));
    dump($f3->get('DB')->log());
});

/**
 * Exception class
 * Class HttpException
 */
class HttpException extends \Exception
{
    /**
     * List of additional headers
     *
     * @var array
     */
    private $headers = array();
    /**
     * Body message
     *
     * @var string
     */
    private $body = '';
    /**
     * List of HTTP status codes
     *
     * From http://en.wikipedia.org/wiki/List_of_HTTP_status_codes
     *
     * @var array
     */

    private $statusCode;

    /**
     * @param int[optional]    $statusCode   If NULL will use 500 as default
     * @param string[optional] $statusPhrase If NULL will use the default status phrase
     * @param array[optional]  $headers      List of additional headers
     */
    public function __construct($statusCode = 500, $statusPhrase = null, array $headers = array())
    {
        $this->statusCode = $statusCode;
        if (null === $statusPhrase && isset($this->status[$statusCode])) {
            $statusPhrase = $this->status[$statusCode];
        }
        parent::__construct($statusPhrase, $statusCode);
        $header  = sprintf('HTTP/1.1 %d %s', $statusCode, $statusPhrase);
        $this->addHeader($header);
        $this->addHeaders($headers);
    }
    private $status = array(
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot', // RFC 2324
        419 => 'Authentication Timeout', // not in RFC 2616
        420 => 'Method Failure', // Spring Framework
        422 => 'Unprocessable Entity', // WebDAV; RFC 4918
        423 => 'Locked', // WebDAV; RFC 4918
        424 => 'Failed Dependency', // WebDAV; RFC 4918
        425 => 'Unordered Collection', // Internet draft
        426 => 'Upgrade Required', // RFC 2817
        428 => 'Precondition Required', // RFC 6585
        429 => 'Too Many Requests', // RFC 6585
        431 => 'Request Header Fields Too Large', // RFC 6585
        444 => 'No Response', // Nginx
        449 => 'Retry With', // Microsoft
        450 => 'Blocked by Windows Parental Controls', // Microsoft
        451 => 'Unavailable For Legal Reasons', // Internet draft
        494 => 'Request Header Too Large', // Nginx
        495 => 'Cert Error', // Nginx
        496 => 'No Cert', // Nginx
        497 => 'HTTP to HTTPS', // Nginx
        499 => 'Client Closed Request', // Nginx
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        506 => 'Variant Also Negotiates', // RFC 2295
        507 => 'Insufficient Storage', // WebDAV; RFC 4918
        508 => 'Loop Detected', // WebDAV; RFC 5842
        509 => 'Bandwidth Limit Exceeded', // Apache bw/limited extension
        510 => 'Not Extended', // RFC 2774
        511 => 'Network Authentication Required', // RFC 6585
        598 => 'Network read timeout error', // Unknown
        599 => 'Network connect timeout error', // Unknown
    );
    /**
     * Returns the list of additional headers
     *
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }
    /**
     * @param string $header
     *
     * @return self
     */
    public function addHeader($header)
    {
        $this->headers[] = $header;
        return $this;
    }
    /**
     * @param array $headers
     *
     * @return self
     */
    public function addHeaders(array $headers)
    {
        foreach ($headers as $key => $header) {
            if (!is_int($key)) {
                $header = $key.': '.$header;
            }
            $this->addHeader($header);
        }
        return $this;
    }
    /**
     * Return the body message.
     *
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }
    /**
     * Define a body message.
     *
     * @param string $body
     *
     * @return self
     */
    public function setBody($body)
    {
        $this->body = (string) $body;
        return $this;
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }
}