<?php
function pr($array)
{
    echo '<pre>';
    print_r($array);
    echo '</pre>';
}

function redirect($link, $top = false)
{
    if ($top) {
        echo "<script>top.location.href='$link';</script>";
    } else {
        echo "<script>document.location.href='$link';</script>";
    }
    exit;
}

function cleanData($str)
{
    $stripped = preg_replace(array('/\s{2,}/', '/[\t\n]/'), ' ', $str);
    $validtext = trim(strip_tags($stripped));
    $validtext = addslashes($validtext);
    return $validtext;
}

if (!function_exists('currentPageName')) {
    function currentPageName()
    {
        return substr($_SERVER["SCRIPT_NAME"], strrpos($_SERVER["SCRIPT_NAME"], "/") + 1);
    }
}

if (!function_exists('format_date')) {
    function format_date($input_date)
    {
        //return date("M d, Y", strtotime($input_date));

        if (empty($input_date) || $input_date == '0000-00-00 00:00:00') {
            return '';
        }
        return date('M d, Y', strtotime($input_date));
    }
}
if (!function_exists('display_datetime')) {
    function display_datetime($input_date)
    {
        return date("M d, Y H:i:s", strtotime($input_date));
    }
}

if (!function_exists('getFileExtension')) {
    function getFileExtension($path)
    {
        $ext = pathinfo($path, PATHINFO_EXTENSION);
        return strtolower($ext);
    }
}

function realEscapeMimic($input)
{
    //Used as mysql_real_escape_string alternative
    if (!empty($input) && is_string($input)) {
        return str_replace(array('\\', "\0", "\n", "\r", "'", '"', "\x1a"), array('\\\\', '\\0', '\\n', '\\r', "\\'", '\\"', '\\Z'), $input);
    }

    return $input;
}

if (!function_exists('convertToAscii')) {
    function convertToAscii($string)
    {
        $replace = [
            '&lt;' => '', '&gt;' => '', '&#039;' => '', '&amp;' => '',
            '&quot;' => '', 'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A',
            '&Auml;' => 'A', 'Å' => 'A', 'Ā' => 'A', 'Ą' => 'A', 'Ă' => 'A', 'Æ' => 'Ae',
            'Ç' => 'C', 'Ć' => 'C', 'Č' => 'C', 'Ĉ' => 'C', 'Ċ' => 'C', 'Ď' => 'D', 'Đ' => 'D',
            'Ð' => 'D', 'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E', 'Ē' => 'E',
            'Ę' => 'E', 'Ě' => 'E', 'Ĕ' => 'E', 'Ė' => 'E', 'Ĝ' => 'G', 'Ğ' => 'G',
            'Ġ' => 'G', 'Ģ' => 'G', 'Ĥ' => 'H', 'Ħ' => 'H', 'Ì' => 'I', 'Í' => 'I',
            'Î' => 'I', 'Ï' => 'I', 'Ī' => 'I', 'Ĩ' => 'I', 'Ĭ' => 'I', 'Į' => 'I',
            'İ' => 'I', 'Ĳ' => 'IJ', 'Ĵ' => 'J', 'Ķ' => 'K', 'Ł' => 'K', 'Ľ' => 'K',
            'Ĺ' => 'K', 'Ļ' => 'K', 'Ŀ' => 'K', 'Ñ' => 'N', 'Ń' => 'N', 'Ň' => 'N',
            'Ņ' => 'N', 'Ŋ' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O',
            'Ö' => 'Oe', '&Ouml;' => 'Oe', 'Ø' => 'O', 'Ō' => 'O', 'Ő' => 'O', 'Ŏ' => 'O',
            'Œ' => 'OE', 'Ŕ' => 'R', 'Ř' => 'R', 'Ŗ' => 'R', 'Ś' => 'S', 'Š' => 'S',
            'Ş' => 'S', 'Ŝ' => 'S', 'Ș' => 'S', 'Ť' => 'T', 'Ţ' => 'T', 'Ŧ' => 'T',
            'Ț' => 'T', 'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U', 'Ü' => 'Ue', 'Ū' => 'U',
            '&Uuml;' => 'Ue', 'Ů' => 'U', 'Ű' => 'U', 'Ŭ' => 'U', 'Ũ' => 'U', 'Ų' => 'U',
            'Ŵ' => 'W', 'Ý' => 'Y', 'Ŷ' => 'Y', 'Ÿ' => 'Y', 'Ź' => 'Z', 'Ž' => 'Z',
            'Ż' => 'Z', 'Þ' => 'T', 'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a',
            'ä' => 'ae', '&auml;' => 'ae', 'å' => 'a', 'ā' => 'a', 'ą' => 'a', 'ă' => 'a',
            'æ' => 'ae', 'ç' => 'c', 'ć' => 'c', 'č' => 'c', 'ĉ' => 'c', 'ċ' => 'c',
            'ď' => 'd', 'đ' => 'd', 'ð' => 'd', 'è' => 'e', 'é' => 'e', 'ê' => 'e',
            'ë' => 'e', 'ē' => 'e', 'ę' => 'e', 'ě' => 'e', 'ĕ' => 'e', 'ė' => 'e',
            'ƒ' => 'f', 'ĝ' => 'g', 'ğ' => 'g', 'ġ' => 'g', 'ģ' => 'g', 'ĥ' => 'h',
            'ħ' => 'h', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i', 'ī' => 'i',
            'ĩ' => 'i', 'ĭ' => 'i', 'į' => 'i', 'ı' => 'i', 'ĳ' => 'ij', 'ĵ' => 'j',
            'ķ' => 'k', 'ĸ' => 'k', 'ł' => 'l', 'ľ' => 'l', 'ĺ' => 'l', 'ļ' => 'l',
            'ŀ' => 'l', 'ñ' => 'n', 'ń' => 'n', 'ň' => 'n', 'ņ' => 'n', 'ŉ' => 'n',
            'ŋ' => 'n', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'oe',
            '&ouml;' => 'oe', 'ø' => 'o', 'ō' => 'o', 'ő' => 'o', 'ŏ' => 'o', 'œ' => 'oe',
            'ŕ' => 'r', 'ř' => 'r', 'ŗ' => 'r', 'š' => 's', 'ù' => 'u', 'ú' => 'u',
            'û' => 'u', 'ü' => 'ue', 'ū' => 'u', '&uuml;' => 'ue', 'ů' => 'u', 'ű' => 'u',
            'ŭ' => 'u', 'ũ' => 'u', 'ų' => 'u', 'ŵ' => 'w', 'ý' => 'y', 'ÿ' => 'y',
            'ŷ' => 'y', 'ž' => 'z', 'ż' => 'z', 'ź' => 'z', 'þ' => 't', 'ß' => 'ss',
            'ſ' => 'ss', 'ый' => 'iy', 'А' => 'A', 'Б' => 'B', 'В' => 'V', 'Г' => 'G',
            'Д' => 'D', 'Е' => 'E', 'Ё' => 'YO', 'Ж' => 'ZH', 'З' => 'Z', 'И' => 'I',
            'Й' => 'Y', 'К' => 'K', 'Л' => 'L', 'М' => 'M', 'Н' => 'N', 'О' => 'O',
            'П' => 'P', 'Р' => 'R', 'С' => 'S', 'Т' => 'T', 'У' => 'U', 'Ф' => 'F',
            'Х' => 'H', 'Ц' => 'C', 'Ч' => 'CH', 'Ш' => 'SH', 'Щ' => 'SCH', 'Ъ' => '',
            'Ы' => 'Y', 'Ь' => '', 'Э' => 'E', 'Ю' => 'YU', 'Я' => 'YA', 'а' => 'a',
            'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e', 'ё' => 'yo',
            'ж' => 'zh', 'з' => 'z', 'и' => 'i', 'й' => 'y', 'к' => 'k', 'л' => 'l',
            'м' => 'm', 'н' => 'n', 'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's',
            'т' => 't', 'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'c', 'ч' => 'ch',
            'ш' => 'sh', 'щ' => 'sch', 'ъ' => '', 'ы' => 'y', 'ь' => '', 'э' => 'e',
            'ю' => 'yu', 'я' => 'ya'
        ];

        return str_replace(array_keys($replace), $replace, $string);
    }
}

if (!function_exists('getSuccessMsg')) {
    function getSuccessMsg($message, $isSticky = false)
    {
        $alertClass = 'alert-success';
        if ($isSticky) {
            $alertClass = 'alert-custsuccess';
        }

        return '<div class="alert ' . $alertClass . ' fade in"> 
                    <i class="fa fa-check-circle"></i> 
                    ' . $message . '
                </div>';
    }
}

if (!function_exists('getErrorMsg')) {
    function getErrorMsg($message, $isSticky = false)
    {
        $alertClass = 'alert-danger';
        if ($isSticky) {
            $alertClass = 'alert-custdanger';
        }

        return '<div class="alert ' . $alertClass . ' fade in"> 
                    <i class="fa fa-times-circle"></i> 
                    ' . $message . '
                </div>';
    }
}

if (!function_exists('getWarningMsg')) {
    function getWarningMsg($message)
    {
        return '<div class="alert alert-warning fade in"> 
                    <i class="fa fa-exclamation-triangle"></i> 
                ' . $message . ' 
                </div>';
    }
}

if (!function_exists('getAlertWell')) {
    function getAlertWell($title, $message)
    {
        return '<div class="alert well">
                    <b>' . $title . '</b> 
                    ' . $message . '
                </div>';
    }
}

function br2nl($text)
{
    return preg_replace('/\<br(\s*)?\/?\>/i', "\n", $text);
}

function getRandomWord($len = 5)
{
    $word = array_merge(range('0', '9'), range('A', 'Z'));
    shuffle($word);
    return substr(implode($word), 0, $len);
}

function captchaImage()
{
    if(!isset($_SESSION)) {
        session_start();
    }
    $ranStr = !empty($_SESSION["vercode_signup"]) ? $_SESSION["vercode_signup"] : getRandomWord();
    $_SESSION["vercode"] = $ranStr;
    unset($_SESSION["vercode_signup"]);

    $height = 35; //CAPTCHA image height
    $width = 150; //CAPTCHA image width
    $font_size = 24;
    $image_p = imagecreate($width, $height);

    $graybg = imagecolorallocate($image_p, 245, 245, 245);
    $textcolor = imagecolorallocate($image_p, 34, 34, 34);

    $font_path = ABSLPATHROOT;
    //$font_path = str_replace(DIRECTORY_SEPARATOR . 'include', DIRECTORY_SEPARATOR, $font_path);
    $font_path = $font_path . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'fonts' . DIRECTORY_SEPARATOR . 'mono.ttf';
    imagefttext($image_p, $font_size, -2, 15, 26, $textcolor, $font_path, $ranStr);


    ob_start();
    imagepng($image_p);
    $image_data = ob_get_contents();
    ob_end_clean();

    imagedestroy($image_p);

    return 'data:image/png;base64,' . base64_encode($image_data);


}

function encryptor($action, $string)
{

    $output = false;

    $encrypt_method = "AES-256-CBC";
    $secret_key = 'wishaproperty';
    $secret_iv = 'wishaproperty2019';

    $key = hash('sha256', $secret_key);

    $iv = substr(hash('sha256', $secret_iv), 0, 16);

    if ($action == 'encrypt') {
        $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
        $output = base64_encode($output);
    } else if ($action == 'decrypt') {
        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
    }

    return $output;
}

function checkMemberLoginNotAllowPage()
{
    $restricted_page = ['add_property','my_property','edit_property'];

    if ((empty($_SESSION['loggedin_userid'])) && in_array($_GET['page'], $restricted_page)) {
        header('Location: index.php');
        exit;
    }
    return true;
}

function checkMemberLogin()
{
    if ((empty($_SESSION['loggedin_userid']))) {
        header('Location: index.php');
        exit;
    }
    return true;
}

// check that admin login or not , if not then redirect to login page
function checkAdminLogin(){
    if ((!isset($_SESSION['admin_uid']))) {
        header('Location: index.php');
        exit;
    }
    return true;
}

function sendEMailForUser($recipient, $subject, $content)
{
    // Set the email subject.
    require_once ABSLPATHROOT . "function/PHPMailer/PHPMailer.php";
    require_once ABSLPATHROOT . "function/PHPMailer/SMTP.php";
    require_once ABSLPATHROOT . "function/PHPMailer/Exception.php";

    $mail = new PHPMailer\PHPMailer\PHPMailer();
    $mail->IsSMTP();

    $mail->SMTPDebug = 3;                               // Enable verbose debug output

    $mail->Mailer = "mail";                                     // Set mailer to use SMTP
    $mail->Host = 'sg2plcpnl0097.prod.sin2.secureserver.net';  // Specify main and backup SMTP servers
    $mail->SMTPAuth = true;                               // Enable SMTP authentication
    $mail->Username = 'shafiq@wishaproperty.com';                 // SMTP username
    $mail->Password = 'l%YJGbWlhmCJ';                           // SMTP password
    $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
    $mail->Port = 465;                                    // TCP port to connect to

    $mail->From = 'shafiq@wishaproperty.com';
    $mail->FromName = 'WishaProperty';
    $mail->addAddress($recipient);     // Add a recipient

    $mail->SMTPOptions = array(
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        )
    );

    $mail->WordWrap = 50;                                 // Set word wrap to 50 characters

    $mail->isHTML(true);                                  // Set email format to HTML

    $mail->Subject = $subject;
    $mail->Body    = $content;

    if(!$mail->send()) {
        return 2;
    } else {
        return 0;
    }
}

function access_control($todo_name) {
    if(!$_SESSION['loggedin_userid']) {
        return false;
    }

    // get the user type
    $user_info = new Users();
    $get_user = $user_info->get(['uid'=>$_SESSION['loggedin_userid']]);
    if($get_user['user_type'] == 'sellers') {
        $todos = [
            'add_property',
            'edit_property',
            'compare_property',
            'my_property',
            'profile',
            'change_password',
            'property',
            'my_watchlist',
        ];
    }
    else if($get_user['user_type'] == 'service_provider') {
        $todos = [
            'property',
            'profile',
            'change_password',
            'add_service',
            'edit_service',
            'manage_service',
            'my_watchlist',
        ];
    }
    else if($get_user['user_type'] == 'buyers') {
        $todos = [
            'profile',
            'property',
            'change_password',
            'my_watchlist',
        ];
    }
    else if($get_user['user_type'] == 'agents') {
        $todos = [
            'add_property',
            'edit_property',
            'my_property',
            'profile',
            'property',
            'change_password',
            'my_watchlist',
        ];
    }

    if(in_array($todo_name, $todos)) {
        return true;
    }
    return false;
}

function setAllInputDataToSession($input_data)
{
    if (!empty($input_data)) {
        foreach($input_data as $key => $value) {
            $_SESSION['input_data'][$key] = $value;
        }
    }
}

if (!function_exists('getRealIP')) {
    function getRealIP()
    {
        if (isset($_SERVER["HTTP_CLIENT_IP"])) {
            $ip = $_SERVER["HTTP_CLIENT_IP"];
        } elseif (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) {
            $ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
        } elseif (isset($_SERVER["HTTP_X_FORWARDED"])) {
            $ip = $_SERVER["HTTP_X_FORWARDED"];
        } elseif (isset($_SERVER["HTTP_FORWARDED_FOR"])) {
            $ip = $_SERVER["HTTP_FORWARDED_FOR"];
        } elseif (isset($_SERVER["HTTP_FORWARDED"])) {
            $ip = $_SERVER["HTTP_FORWARDED"];
        } else {
            $ip = $_SERVER["REMOTE_ADDR"];
        }

        // Strip any secondary IP etc from the IP address
        if (strpos($ip, ',') > 0) {
            $ip = substr($ip, 0, strpos($ip, ','));
        }
        return $ip;
    }
}

?>