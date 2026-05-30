<?php
require_once ABSLPATHROOT . 'models/users.php';
require_once ABSLPATHROOT . 'models/country.php';
require_once ABSLPATHROOT . 'models/subscribers.php';
require_once ABSLPATHROOT . 'models/admin_users.php';
require_once ABSLPATHROOT . 'models/services.php';


class KBUserManagement
{
    const SUCCESS = 0;
    const FAILED = 1;
    const INVALID = 2;
    const INVALID_DATA = 3;
    const INACTIVE = 4;

    protected $usersInfo;
    protected $country;
    protected $adminUsers;
    protected $services;

    public function __construct()
    {
        $this->usersInfo = new Users();
        $this->country = new Country();
        $this->subscribers = new Subscribers();
        $this->adminUsers = new AdminUsers();
        $this->services = new Services();
    }

    public function register($postdata)
    {
        global $HOMEPAGE_ROOT;

        $response = self::FAILED;
        $first_name = $postdata['first_name'];
        $last_name = $postdata['last_name'];
        $user_type = $postdata['user_type'];
        $username = $postdata['username'];
        $phone = $postdata['phone'];
        $city = $postdata['city'];
        $address = $postdata['address'];
        $email = $postdata['email'];
        $password = password_hash($postdata['password'], PASSWORD_DEFAULT);
        $country = $postdata['country'];

        $firstname_error = $this->validateFirstName($first_name);
        $lastname_error = $this->validateLastName($last_name);
        $user_type_error = $this->validateUserType($user_type);
        $city_error = $this->validateCity($city);
        $address_type_error = $this->validateAddress($address);
        if($postdata['service_name']) {
            $service_name_error = $this->validateServiceName($postdata['service_name']);
        }
        $username_error = $this->validateUsername($username);
        $email_error = $this->validateEmail($email);
        $password_error = $this->validatePassword($postdata['password'], $postdata['confirm_password']);
        $captcha_error = $this->validateCaptcha($postdata['captcha']);

        $data_object = '';
        if ($firstname_error != '') {
            $data_object = $firstname_error . ',';
        }
        if ($lastname_error != '') {
            $data_object .= $lastname_error . ',';
        }
        if ($user_type_error != '') {
            $data_object .= $user_type_error . ',';
        }
        if ($city_error != '') {
            $data_object .= $city_error . ',';
        }
        if ($address_type_error != '') {
            $data_object .= $address_type_error . ',';
        }
        if($postdata['service_name']) {
            if ($service_name_error != '') {
                $data_object .= $service_name_error . ',';
            }
        }
        if ($username_error != '') {
            $data_object .= $username_error . ',';
        }
        if ($email_error != '') {
            $data_object .= $email_error . ',';
        }
        if ($password_error != '') {
            $data_object .= $password_error . ',';
        }
        if ($captcha_error != '') {
            $data_object .= $captcha_error . ',';
        }

        if ($data_object != '') {
            return $data_object;
        }

        $data = [
            'first_name' => $first_name,
            'last_name' => $last_name,
            'user_type' => $user_type,
            'username' => $username,
            'phone' => $phone,
            'city' => $city,
            'address' => $address,
            'email' => $email,
            'country' => $country,
            'password' => $password,
            'created' => date('Y-m-d H:i:s'),
        ];


        $last_id = $this->usersInfo->save($data);
        if ($last_id) {

            if($postdata['service_name'] && $user_type == 'service_provider') {
                $services_data = [
                    'service_name' => $postdata['service_name'],
                    'user_id' => $last_id
                ];
                $this->services->save($services_data);
            }

            $recipient = $email;
            $subject = "Welcome to the Wishaproperty";

            $enc_uid = encryptor('encrypt', $last_id);

            $verify_link = $HOMEPAGE_ROOT . '/index.php?page=verifyuser&uid=' . $enc_uid;

            $message_content = '<html><body>';
            $message_content .= '<h4 style="color:#5b5a5a;"> Hello : ' . $first_name . ' ' . $last_name . '</h4>';
            $message_content .= '<p>Thank you for registering to Wishaproperty. Your registration has been successfully completed.
                              Please <a href="' . $verify_link . '" style="color:#0000EE !important"  target="_blank"> click the confirm link </a> to active your register account:</p>';
            $message_content .= '<p>&nbsp;</p><p>Kind Regards,</p>
                        <p>Wishaproperty Staff</p>
                        <p>info@wishaproperty.com</p>';
            $message_content .= '</body></html>';

            // send contact user email
            sendEMailForUser($recipient, $subject, $message_content);

            $response = self::SUCCESS;
        }
        return $response;
    }

    public function addSubscriber($postdata)
    {
        $response = self::FAILED;
        $email = $postdata['subscriber_email'];
        $email_error = $this->validateSubscribersEmail($email);

        $data_object = '';
        if ($email_error != '') {
            $data_object .= $email_error . ',';
        }

        if ($data_object != '') {
            return $data_object;
        }

        $data = [
            'email' => $email,
            'created' => date('Y-m-d H:i:s'),
        ];

        $last_id = $this->subscribers->save($data);
        if ($last_id) {
            $response = self::SUCCESS;
        }
        return $response;
    }

    public function login($postdata)
    {
        $username = strtolower($postdata['username']);
        $line = $this->usersInfo->get(['username' => $username]);
        $password = $postdata['password'];
        $db_password = $line['password'];
        if (!empty($line) && (password_verify($password, $db_password) == 1)) {
            if (strtolower($postdata['captcha']) == strtolower($_SESSION['vercode'])) {
                if($line['useractivated'] == 1) {
                    $_SESSION['loggedin_userid'] = $line['uid'];
                    $response = self::SUCCESS;
                } else {
                    $response = self::INACTIVE;
                }
            } else {
                $response = self::FAILED;
            }
        } else {
            $response = self::INVALID_DATA;
        }
        return $response;
    }

    function logout()
    {
        unset($_SESSION['loggedin_userid']);
        session_destroy();
    }

    public function editProfile($postdata, $user_id) {

        $response = self::FAILED;
        $first_name = $postdata['first_name'];
        $last_name = $postdata['last_name'];
        $phone = $postdata['phone'];
        $email = $postdata['email'];
        $country = $postdata['country'];
        $city = $postdata['city'];
        $address = $postdata['address'];

        $firstname_error = $this->validateFirstName($first_name);
        $lastname_error = $this->validateLastName($last_name);
        $email_error = $this->validateEditUserEmail($email);
        $dup_email_error = $this->validateDuplicateEmail($email, $user_id);
        $city_error = $this->validateCity($city);
        $address_type_error = $this->validateAddress($address);

        $data_object = '';
        if ($firstname_error != '') {
            $data_object = $firstname_error . ',';
        }
        if ($lastname_error != '') {
            $data_object .= $lastname_error . ',';
        }
        if ($email_error != '') {
            $data_object .= $email_error . ',';
        }

        if ($dup_email_error != '') {
            $data_object .= $dup_email_error . ',';
        }

        if ($city_error != '') {
            $data_object .= $city_error . ',';
        }
        if ($address_type_error != '') {
            $data_object .= $address_type_error . ',';
        }

        if ($data_object != '') {
            return $data_object;
        }

        $data = [
            'first_name' => $first_name,
            'last_name' => $last_name,
            'phone' => $phone,
            'email' => $email,
            'address' => $address,
            'city' => $city,
            'country' => $country
        ];

        $where = [
            'uid' => $user_id
        ];
        $data_array = $this->usersInfo->save($data, $where);
        if ($data_array) {
            $response = self::SUCCESS;
        }
        return $response;
    }


    //Validating Password
    function validatePassword($pwd1, $pwd2)
    {
        $return = '';
        if (strlen($pwd1) < 8) {
            $return = "Password too short (min 8 characters)";
        } else if ((strlen($pwd1) >= 8) && ($pwd1 != $pwd2)) {
            $return = "Password entry mismatch";
        }
        return $this->removeEmptySpaces($return);
    }

    //Validating Password
    function validateCaptcha($captcha)
    {
        $return = '';
        if (strtolower($captcha) != strtolower($_SESSION['vercode'])) {
            $return = "Captcha entry mismatch";
        }
        return $this->removeEmptySpaces($return);
    }

    //Email checking
    function validateEmail($email)
    {
        $return = '';

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $return = "Please enter valid email";
        } else {
            $all_user_email = $this->usersInfo->getAll(['email' => $email]);
            if (is_array($all_user_email) && count($all_user_email) > 0) {
                $return = "E-mail address already registered in database";
            }
        }

        return $this->removeEmptySpaces($return);
    }

    function validateSubscribersEmail($email)
    {
        $return = '';

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $return = "Please enter valid email";
        } else {
            $all_user_email = $this->subscribers->getAll(['email' => $email]);
            if (is_array($all_user_email) && count($all_user_email) > 0) {
                $return = "E-mail address already registered in database";
            }
        }

        return $this->removeEmptySpaces($return);
    }

    function validateEditUserEmail($email)
    {
        $return = '';

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $return = "Please enter valid email";
        }

        return $this->removeEmptySpaces($return);
    }

    // duplicate Email checking when updating profile
    function validateDuplicateEmail($email, $uid)
    {
        $return = '';
        $where = array(
            'email' => $email,
            'uid' => array($uid, '!=')
        );
        $user = $this->usersInfo->getAll($where);
        if (count($user) > 0) {
            $return = "E-mail address already registered in database";
        }

        return $this->removeEmptySpaces($return);
    }

    function validateCorrectEmail($email)
    {
        $return = '';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $return = "Please enter valid email";
        }

        return $this->removeEmptySpaces($return);
    }

    //Validating First name
    function validateFirstName($name)
    {
        $return = '';
        if (strlen($name) == 0) {
            $return = 'First name is required';
        }

        if (!$this->validateAsciiCharacter($name)) {
            $return = 'Invalid character on firstname';
        }

        return $this->removeEmptySpaces($return);
    }


    //Validating service name
    function validateServiceName($name)
    {
        $return = '';
        if (strlen($name) == 0) {
            $return = 'Service name is required';
        }

        if (!$this->validateAsciiCharacter($name)) {
            $return = 'Invalid character on service name';
        }

        return $this->removeEmptySpaces($return);
    }


    //Validating Lastname
    function validateLastName($name)
    {
        $return = '';
        if (strlen($name) == 0) {
            $return = 'Last name is required';
        }

        if (!$this->validateAsciiCharacter($name)) {
            $return = 'Invalid character on lastname';
        }
        return $this->removeEmptySpaces($return);
    }

    function validateUserType($user_type)
    {
        $return = '';
        if (strlen($user_type) == 0) {
            $return = 'User type is required';
        }

        if (!$this->validateAsciiCharacter($user_type)) {
            $return = 'Invalid character on user type';
        }
        return $this->removeEmptySpaces($return);
    }


    //Validating Address
    function validateAddress($address)
    {
        $return = '';
        if (strlen($address) == 0) {
            $return = 'Address is required';
        }
        if (!$this->validateAsciiCharacter($address)) {
            $return = 'Invalid character on address';
        }
        return $this->removeEmptySpaces($return);
    }


    //Validating city
    function validateCity($city)
    {
        $return = '';
        if (strlen($city) == 0) {
            $return = 'City is required';
        }
        if (!$this->validateAsciiCharacter($city)) {
            $return = 'Invalid character on city';
        }
        return $this->removeEmptySpaces($return);
    }


    //Validating Zipcode
    function validateZipCode($zip)
    {
        $return = '';
        if (strlen($zip) == 0) {
            $return = 'Zip/Postcode is required';
        }
        return $this->removeEmptySpaces($return);
    }

    //Validating country
    function validateCountry($country)
    {
        $return = '';
        if ($country == '0' || strlen($country) < 1) {
            $return = 'Please select country';
        } else {
            $where = array(
                'countrycode' => $country,
            );
            $fields = 'countryname';
            $country = $this->country->get($where, $fields);
            if (empty($country)) {
                $return = 'Gold Purchase not allowed for this country';
            }
        }
        return $this->removeEmptySpaces($return);
    }


    //Validating Phone/Mobile number
    function validatePhoneNumber($phoneno, $countrycode)
    {
        $return = '';

        if (substr($phoneno, 0, 1) != '+') {
            // Check if country code is included
            $return = "International phone number prefix missing";
        } else if (strlen($phoneno) < 8) {
            $return = 'Missing/incorrect phone number';
        } else if (!preg_match("/^[0-9]+$/", substr($phoneno, 1))) {
            $return = 'Mobile Number Contains Incorrect Characters (+ and digits only, no spaces, dashes or parantheses)';
        } else {
            if ($countrycode != '0') {
                // Lookup the coutry prefix
                $country = $this->country->get(['countrycode' => $countrycode]);
                $country_prefix = $country['countryprefix'];
                $phone_prefix = substr($phoneno, 0, strlen($country_prefix));
                if ($phone_prefix != $country_prefix) {
                    $return = 'Incorrect Phone Prefix (doesn\'t match selected country)';
                } else {
                    // Check remaining phone number (without int prefix)
                    $phone_stripped = substr($phoneno, strlen($country_prefix), strlen($phoneno) - strlen($country_prefix));
                    if (strlen($phone_stripped) < 6) {
                        $return = 'Missing/incorrect phone number';
                    }
                }
            } else {
                $return = 'Incorrect Phone Prefix (doesn\'t match selected country)';
            }
        }

        return $this->removeEmptySpaces($return);
    }


    //Remove Empty spaces and strip tags in language variable
    function removeEmptySpaces($str)
    {
        $stripped = preg_replace(array('/\s{2,}/', '/[\t\n]/'), ' ', $str);
        $validtext = trim(strip_tags($stripped));
        $validtext = addslashes($validtext);
        return $validtext;
    }

    function validateAsciiCharacter($str)
    {
        $safeChars = array(
            'ƒ', 'Š', 'š', 'Ð', 'Ž', 'ž', 'À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È',
            'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', 'Ù',
            'Ú', 'Û', 'Ü', 'Ý', 'Þ', 'ß', 'à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'ç', 'è',
            'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ð', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø',
            'ù', 'ú', 'û', 'ý', 'ý', 'þ', 'ÿ'
        );
        $chars = preg_split('/(?<!^)(?!$)/u', $str);

        foreach ($chars as $ch) {
            if (preg_match('/[^\x20-\x7f]/', $ch)) {
                $safe = false;
                foreach ($safeChars as $safeChar) {
                    if (strcasecmp($safeChar, $ch) == 0) {
                        $safe = true;
                    }
                }

                if ($safe === false) {
                    return false;
                }
            }
        }
        return true;
    }


    //Username checking
    function validateUsername($username)
    {
        $return = '';
        $reserved_keywords = array('include', 'admin', 'administrative', 'case', 'css', 'javascript', 'images', 'cgi-bin', 'connect',
            'docs', 'functions', 'lang', 'shop', 'webshop', 'master', 'signup', 'support', 'register', 'join', 'joinnow', 'signupnow',
            'registernow', 'management', 'senegal', 'sanmarino', 'slovakia', 'slovenia', 'singapore', 'sweden', 'sudan', 'seychelles',
            'saudiarabia', 'rwanda', 'russia', 'romania', 'qatar', 'paraguay', 'palau', 'portugal', 'puertorico', 'poland',
            'pakistan', 'philippines', 'peru', 'panama', 'oman', 'newzealand', 'nepal', 'norway', 'netherlands', 'nicaragua',
            'nigeria', 'niger', 'namibia', 'micronesia', 'mozambique', 'malaysia', 'mexico', 'maldives', 'mauritius', 'malta',
            'montserrat', 'mauritania', 'martinique', 'macau', 'mongolia', 'myanmar', 'macedonia', 'madagascar', 'moldova',
            'monaco', 'morocco', 'libya', 'latvia', 'luxembourg', 'lithuania', 'liechtenstein', 'lebanon', 'laos', 'kazakhstan',
            'kuwait', 'southkorea', 'nevis', 'kiribati', 'kyrgyzstan', 'kenya', 'japan', 'jordan', 'jamaica', 'jersey', 'ivorycoast',
            'italy', 'iceland', 'iran', 'iraq', 'india', 'israel', 'ireland', 'indonesia', 'hungary', 'haiti', 'honduras',
            'hongkong', 'germany', 'guam', 'guatemala', 'greece', 'gambia', 'greenland', 'gibraltar', 'ghana', 'georgia',
            'grenada', 'gabon', 'greatbritain', 'england', 'uk', 'france', 'fiji', 'finland', 'timor', 'ethiopia', 'eritrea',
            'estonia', 'egypt', 'ecuador', 'dominica', 'denmark', 'croatia', 'chad', 'cambodia', 'czech', 'cyprus', 'cuba',
            'costarica', 'colombia', 'china', 'cameroon', 'chile', 'congo', 'canada', 'belize', 'belarus', 'botswana', 'bhutan',
            'bahamas', 'brazil', 'bolivia', 'brunei', 'bermuda', 'benin', 'burundi', 'bahrain', 'bulgaria', 'belgium', 'bangladesh',
            'barbados', 'bosnia', 'algeria', 'azerbaijan', 'aruba', 'australia', 'austria', 'argentina', 'antarctica', 'angola',
            'albania', 'armenia', 'anguilla', 'afghanistan', 'unitedarabemirate', 'uae', 'andorra', 'southafrica', 'spain',
            'srilanka', 'switzerland', 'thailand', 'tajikistan', 'tokelau', 'turkmenistan', 'tunisia', 'tonga', 'turkey', 'taiwan',
            'tanzania', 'ukraine', 'uganda', 'unitedkingdom', 'unitedstates', 'us', 'usa', 'uruguay', 'uzbekistan', 'venezuela',
            'vietnam', 'vanuatu', 'yemen', 'zambia', 'zaire', 'zimbabwe', 'super', 'kbgold', 'about', 'story', 'ourvision',
            'management', 'ceo', 'news', 'signup', 'kbvision', 'prioritypass', 'distributorkit', 'kbtop', 'opportunity',
            'compensationplan', 'rank', 'incentives', 'networkers', 'products', 'jiitter', 'kbedelmetall', 'travelagent', 'referrer',
            'press', 'kbfeed', 'forgotten', 'kbcard', 'banners', 'callback', 'case', 'charts', 'christmas', 'config', 'connect',
            'cronjobs', 'docs', 'flash', 'forms_front', 'functions', 'images', 'include', 'javascript', 'js', 'kbdownloads', 'm',
            'movies', 'newsletter', 'pdf_templates', 'peel', 'recognition', 'reports', 'scroll', 'support', 'templates', 'terms',
            'uploads', 'xml', 'username', 'new_username');

        if (strlen($username) < 4) {
            $return = "Username Missing/Too Short.";
        } else if (!preg_match("/^[a-zA-Z0-9]+$/", $username)) {
            $return = "Username contains illegal charaters";
        } else if (in_array($username, $reserved_keywords)) {
            $return = 'Reserved Username';
        } else {
            $user = $this->usersInfo->get(['username' => $username]);
            if (!empty($user)) {
                $return = "Username Already In Use";
            }
        }

        return $this->removeEmptySpaces($return);
    }

    public function contactUs($postdata)
    {
        if (!empty($postdata["name"]) && !empty($postdata["email"]) && !empty($postdata["message"])) {
            $name = strip_tags(trim($postdata["name"]));
            $name = str_replace(array("\r", "\n"), array(" ", " "), $name);
            $email = filter_var(trim($postdata["email"]), FILTER_SANITIZE_EMAIL);
            $message = trim($postdata["message"]);

            // Check that data was sent to the mailer.
            if (empty($name) OR empty($message) OR !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                //"Oops! There was a problem with your submission. Please complete the form and try again.";
                return 1;
            }

            // Set the email subject.
            $recipient = 'shafiqruet@gmail.com';
            $subject = "New contact from $name";

            $message_content = '<html><body>';
            $message_content .= '<h4 style="color:#5b5a5a;"> Subject : ' . $subject . '</h4>';
            $message_content .= '<h5 style="color:#5b5a5a;"> Email Address : ' . $email . '</h5>';
            $message_content .= '<p style="color:#5b5a5a;font-size:14px;">Message : ' . $message . '</p>';
            $message_content .= '</body></html>';

            // send contact user email

            $response = sendEMailForUser($recipient, $subject, $message_content);
            // Send the email.
            return $response;

        } else {
            // "There was a problem with your submission, please try again.";
            return 3;
        }
    }

    public function adminLogin($postdata)
    {
        $error = null;
        $remote_ip_address = getRealIP();

        $username = strtolower($postdata['username']);
        $line = $this->adminUsers->get(['username' => $username]);
        $password = $postdata['password'];
        $db_password = $line['password'];
        if (!empty($line) && (password_verify($password, $db_password) == 1)) {
            if (strtolower($postdata['captcha']) == strtolower($_SESSION['vercode'])) {
               // Check for inactive / suspended user
                if ($line['status'] != 1) {
                    $error = 'Sorry, user is not active';
                    return $error;
                }

                $current_time = date("Y-m-d H:i:s");
                if (($line['locked_until'] < $current_time) || !isset($line['locked_until'])) {
                    $_SESSION['admin_uid'] = $line['uid'];
                    $data = [
                        'last_access_time' => date('Y-m-d H:i:s'),
                        'login_attempts' => 0,
                        'locked_until' => null,
                        'last_access_ip' => $remote_ip_address
                    ];
                    $this->adminUsers->saveByPk($data, $_SESSION['admin_uid']);
                } else {
                    $error = 'User Locked, Please contact';
                }
            } else {
                $error = 'Please enter valid captcha';
            }
        } else if(!empty($line) ){
            // Login attempt with incorrect password detected
            $login_attempts = $line['login_attempts'];
            $login_attempts = $login_attempts + 1;

            if ($login_attempts >= 4) {
                $blocked_until = strftime("%Y-%m-%d %H:%M:%S", strtotime("+30 minutes"));

                $where = array();
                $where['username'] = $username;
                $where['status'] = 1;

                $data = array();
                $data['login_attempts'] = 0;
                $data['locked_until'] = $blocked_until;

                $this->adminUsers->save($data, $where);
            } else {
                $where = [
                    'username' => $username,
                    'status' => 1
                ];
                $data = array('login_attempts' => $login_attempts);
                $this->adminUsers->save($data, $where);
            }
            $error = 'Username or password incorrect';
        } else {
            $error = 'Username incorrect';
        }
        return $error;
    }

    public function changeAdminPassword($post_data)
    {
        $password = trim($post_data['password']);
        $admin_id = trim($post_data['adminid']);

        $user_line = $this->usersInfo->getByField('uid', $admin_id);
        if(!empty($user_line)) {
            $db_password = $user_line['password'];
            if (password_verify($password, $db_password) == 1 || $db_password == md5($password)) {
                $_SESSION['IS_PASSWORD_MATCH_PREV'] = true;
                return 0;
            }

            $password_error = $this->validatePassword($post_data['password'], $post_data['confirmpass']);
            $data_object = '';
            if ($password_error != '') {
                $data_object .= $password_error . ',';
            }

            if ($data_object != '') {
                return $data_object;
            }

            $data = [];
            $data['password_updated'] = date('Y-m-d H:i:s');
            if (isset($password) && $password != '') {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $data['password'] = $hash;
            }

            $where = array();
            $where['uid'] = $admin_id;
            $edit_admin = $this->usersInfo->save($data, $where);
        }

        if ($edit_admin) {
            return 1;
        } else {
            return 0;
        }
    }

    public function updateUserFromAdmin($postdata)
    {
        $response = self::FAILED;

        $first_name = $postdata['first_name'];
        $last_name = $postdata['last_name'];
        $phone = $postdata['phone'];
        $user_type = $postdata['user_type'];
        $password = $postdata['password'];
        $country = $postdata['country'];
        $user_id = $postdata['user_id'];

        if($user_id > 0 && !empty($postdata)) {

            $user_line = $this->usersInfo->getByField('uid', $user_id);

            if(!empty($user_line)) {

                $firstname_error = $this->validateFirstName($first_name);
                $lastname_error = $this->validateLastName($last_name);
                $usertype_error = $this->validateUserType($user_type);
                $password_error = '';
                if ($password) {
                    $password_error = $this->validatePassword($postdata['password'], $postdata['confirm_password']);
                }
                if($postdata['service_name']) {
                    $service_name_error = $this->validateServiceName($postdata['service_name']);
                }

                $data_object = '';
                if ($firstname_error != '') {
                    $data_object = $firstname_error . ',';
                }
                if ($lastname_error != '') {
                    $data_object .= $lastname_error . ',';
                }
                if ($usertype_error != '') {
                    $data_object .= $usertype_error . ',';
                }
                if ($password_error != '') {
                    $data_object .= $password_error . ',';
                }
                if($postdata['service_name']) {
                    if ($service_name_error != '') {
                        $data_object .= $service_name_error . ',';
                    }
                }
                if ($data_object != '') {
                    return $data_object;
                }

                $data = [
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                    'phone' => $phone,
                    'user_type' => $user_type,
                    'country' => $country,
                    'useractivated' => $postdata['status']
                ];

                if (isset($password) && $password != '') {
                    $hash = password_hash($password, PASSWORD_DEFAULT);
                    $data['password'] = $hash;
                    $data['password_updated'] = date('Y-m-d H:i:s');;
                }

                $where = [
                    'uid' => $user_id
                ];
                $data_array = $this->usersInfo->save($data, $where);
                if ($data_array) {
                    if($postdata['service_id'] == 0 && $postdata['service_name'] !='' && $user_type == 'service_provider') {
                        $services_data = [
                            'service_name' => $postdata['service_name'],
                            'user_id' => $user_id
                        ];
                        $this->services->save($services_data);
                    }
                    else if($postdata['service_id'] != 0 && $postdata['service_name'] !='' && $user_type == 'service_provider') {
                        $data = ['service_name' => $postdata['service_name']];
                        $where = ['id' => $postdata['service_id']];
                        $this->services->save($data, $where);
                    }
                    else if($postdata['service_id'] != 0 && $user_type != 'service_provider') {
                        $this->services->remove($postdata['service_id'], []);
                    }
                    $response = self::SUCCESS;
                }
            }
        }
        return $response;
    }

}

?>