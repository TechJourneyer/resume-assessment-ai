<?php 
require_once 'vendor/autoload.php'; // Include the Guzzle library
error_reporting(0);
ini_set('display_errors',0);
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

define('OPENAI_API_KEY' , getenv('OPENAI_API_KEY'));
define('BASE_URI' , getenv('BASE_URI'));
define('BASE_URL' , getenv('BASE_URL'));
define('POPPLER_UTILS_BINARY' , getenv('POPPLER_UTILS_BINARY'));

define('DB_HOST' , getenv('DB_HOST'));
define('DB_USER' , getenv('DB_USER'));
define('DB_PASS' , getenv('DB_PASS'));
define('DB_NAME' , getenv('DB_NAME'));

define('RESUME_PDF_UPLOAD_DIR' , getenv('RESUME_PDF_UPLOAD_DIR'));
define('RESUME_PDF2TEXT_UPLOAD_DIR' , getenv('RESUME_PDF2TEXT_UPLOAD_DIR'));
define('FILE_SUFFIX_DATE_FORMAT' , 'YmdHis');
define('ACTIVE_LOG_TYPES' , 'info,warning,error,critical,notice,alert,emergency');

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);