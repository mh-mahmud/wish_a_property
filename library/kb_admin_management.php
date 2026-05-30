<?php
require_once ABSLPATHROOT . 'models/slider.php';
require_once ABSLPATHROOT . 'models/admin_users.php';
require_once ABSLPATHROOT . 'models/agents.php';
require_once ABSLPATHROOT . 'models/latest_news.php';
require_once ABSLPATHROOT . 'models/newsticker.php';

Class KBAdminManamement
{
    const SUCCESS = 0;
    const FAILED = 1;

    protected $homeSliderSetting;
    protected $adminInfo;
    protected $agents;
    protected $latestNews;
    protected $newsticker;

    public function __construct()
    {
        $this->homeSliderSetting = new Slider();
        $this->adminInfo = new AdminUsers();
        $this->agents= new Agents();
        $this->latestNews = new LatestNews();
        $this->newsticker = new Newsticker();
    }

    function addSlider($post_data)
    {
        $response = self::FAILED;
        if (!empty($post_data) && !empty($post_data['title'])) {
            $slider_image = basename($_FILES['slider_file']['name']);

            $slider_title = $post_data['title'];
            $slider_subtitle = $post_data['slider_subtitle'];
            $button_text = $post_data['button_text'];
            $target_link = trim($post_data['target_link']);
            $fileName_extension = substr(strrchr($slider_image, '.'), 1);
            $fileTypes = array('png', 'PNG', 'jpg', 'JPG');
            if (in_array($fileName_extension, $fileTypes)) {
                $uploaddir = ABSLPATHROOT . KBConstant::UPLOAD_FILE_PATH . 'home_slider/';
                if (!file_exists($uploaddir)) {
                    mkdir($uploaddir, 0777, true);
                }

                //get next listorder
                $where = array(
                    'id' => array(0, '!=')
                );

                $line = $this->homeSliderSetting->get($where, "id, listorder", "listorder desc");
                $max_list_order = $line['listorder'] + 1;

                // Upload image
                $fileName = 'home_slide' . $max_list_order . '.' . $fileName_extension;
                $uploadfile = $uploaddir . $fileName;
                move_uploaded_file($_FILES['slider_file']['tmp_name'], $uploadfile);

                $active = ($post_data['active'] == 1) ? 1 : 0;
                $data = array(
                    'slider_image' => $fileName,
                    'create_date' => date('Y-m-d H:i:s'),
                    'status' => $active,
                    'target_link' => $target_link,
                    'listorder' => $max_list_order
                );

                $last_id = $this->homeSliderSetting->save($data);
                // update news constant later
                $data = array(
                    'slider_title' => $slider_title,
                    'slider_subtitle' => $slider_subtitle,
                    'button_text' => $button_text
                );
                $this->homeSliderSetting->saveByPk($data, $last_id);

                $response = self::SUCCESS;
            }
        }

        return $response;
    }

    function addNews($post_data)
    {
        $response = self::FAILED;

        if (!empty($post_data['news_title']) && !empty($post_data['news_description'])) {
            $news_title = $post_data['news_title'];
            $news_description = $post_data['news_description'];
            $published_date = $post_data['published_date'];
            $active = ($post_data['active'] == 1) ? 1 : 0;

            //get next listorder
            $where = array(
                'id' => array(0, '!=')
            );
            $line = $this->latestNews->get($where, "id, listorder", "listorder desc");
            $max_list_order = $line['listorder'] + 1;
            $data = array(
                'news_title' => $news_title,
                'news_description' => $news_description,
                'status' => $active,
                'listorder' => $max_list_order,
                'published_date' => $published_date,
                'created_date' => date('Y-m-d H:i:s')
            );

            $last_id = $this->latestNews->save($data);
            if($last_id) {
                $response = self::SUCCESS;
            }
        }

        return $response;
    }

    function addNewstickers($post_data)
    {
        $response = self::FAILED;
        if (!empty($post_data['news_title'])) {

            $news_title = $post_data['news_title'];
            $active = ($post_data['active'] == 1) ? 1 : 0;
            $data = array(
                'news_title' => $news_title,
                'status' => $active,
                'created_date' => date('Y-m-d H:i:s')
            );

            $last_id = $this->newsticker->save($data);
            if($last_id) {
                $response = self::SUCCESS;
            }
        }

        return $response;
    }

    function editNewstickers($post_data)
    {
        $response = self::FAILED;

        if (!empty($post_data) && isset($post_data['news_id'])) {
            $news_info = $this->newsticker->get($post_data['news_id']);
            if(!empty($news_info)) {

                $news_title = $post_data['news_title'];
                $active = ($post_data['active'] == 1) ? 1 : 0;

                $data = array(
                    'news_title' => $news_title,
                    'status' => $active,
                );

                $where = array(
                    'id' => $post_data['news_id']
                );
                $res = $this->newsticker->save($data, $where);

                if ($res) {
                    $response = self::SUCCESS;
                }
            }
        }

        return $response;
    }

    function editNews($post_data)
    {
        $response = self::FAILED;

        if (!empty($post_data) && isset($post_data['news_id'])) {
            $news_info = $this->latestNews->get($post_data['news_id']);
            if(!empty($news_info)) {

                $news_title = $post_data['news_title'];
                $news_description = $post_data['news_description'];
                $published_date = $post_data['published_date'];
                $active = ($post_data['active'] == 1) ? 1 : 0;

                $data = array(
                    'news_title' => $news_title,
                    'news_description' => $news_description,
                    'status' => $active,
                    'published_date' => $published_date
                );

                $where = array(
                    'id' => $post_data['news_id']
                );
                $res = $this->latestNews->save($data, $where);

                if ($res) {
                    $response = self::SUCCESS;
                }
            }
        }

        return $response;
    }

    function editSilder($post_data)
    {
        $response = self::FAILED;

        if (!empty($post_data) && isset($post_data['slider_id'])) {
            $slider_info = $this->homeSliderSetting->get($post_data['slider_id']);
            if(!empty($slider_info)) {
                $log_data_old['home_slider_setting'] = $slider_info;
                $active = ($post_data['active'] == 1) ? 1 : 0;
                $data = array();
                if ($slider_info['active'] != $post_data['active']) {
                    $data['status'] = $active;
                }
                $target_link = trim($post_data['target_link']);
                if (!empty($target_link)) {
                    $data['target_link'] = $target_link;
                }

                $title = trim($post_data['title']);
                $slider_subtitle = trim($post_data['slider_subtitle']);
                $button_text = trim($post_data['button_text']);
                if (!empty($title)) {
                    $data['slider_title'] = $title;
                    $data['slider_subtitle'] = $slider_subtitle;
                    $data['button_text'] = $button_text;
                }

                if (!empty($_FILES['slider_file']['name'])) {
                    $slider_image = basename($_FILES['slider_file']['name']);
                    $fileName_extension = substr(strrchr($slider_image, '.'), 1);
                    $fileTypes = array('png', 'PNG', 'jpg', 'JPG');
                    if (in_array($fileName_extension, $fileTypes)) {
                        $uploaddir = ABSLPATHROOT . KBConstant::UPLOAD_FILE_PATH . 'home_slider/';

                        if (file_exists($uploaddir . $slider_info['slider_image'])) {
                            unlink($uploaddir . $slider_info['slider_image']);
                        }

                        $ex_image = explode('.', $slider_info['slider_image']);
                        $new_file_name = $ex_image[0] . '.' . $fileName_extension;
                        $uploadfile = $uploaddir . $new_file_name;
                        move_uploaded_file($_FILES['slider_file']['tmp_name'], $uploadfile);

                        $data['slider_image'] = $new_file_name;
                    }
                }

                $where = array(
                    'id' => $post_data['slider_id']
                );
                $res = $this->homeSliderSetting->save($data, $where);

                if ($res) {
                    $response = self::SUCCESS;
                }
            }
        }

        return $response;
    }

    function removeHomeSlider($post_data)
    {
        $slider_id = $post_data['slider_id'];
        $listorder = $post_data['listorder'];
        $field_value = 'listorder';

        if ($slider_id > 0 && !empty($post_data)) {
            // remove from mail template table
            $remove = $this->homeSliderSetting->remove($slider_id);
            if ($remove) {
                if ($listorder != 0) {
                    $this->homeSliderSetting->decrement($field_value, 1, $field_value, $listorder, '>');
                }

                // for setting new listorder without refresh page
                $result = $this->homeSliderSetting->getAll('', "id, {$field_value}");
                foreach ($result as $line) {
                    $result_array[$line['id']] = $line[$field_value];
                }
            }

            $uploaddir = ABSLPATHROOT . KBConstant::UPLOAD_FILE_PATH . 'home_slider/';
            $file_name = $post_data['image_name'];
            unlink($uploaddir . $file_name);

            $result_array['success'] = 1;

        } else {
            $result_array['success'] = 0;
        }

        return $result_array;
    }

    function removeLatestNews($post_data)
    {
        $news_id = $post_data['news_id'];
        $listorder = $post_data['listorder'];
        $field_value = 'listorder';

        if ($news_id > 0 && !empty($post_data)) {
            // remove from mail template table
            $remove = $this->latestNews->remove($news_id);
            if ($remove) {
                if ($listorder != 0) {
                    $this->latestNews->decrement($field_value, 1, $field_value, $listorder, '>');
                }

                // for setting new listorder without refresh page
                $result = $this->latestNews->getAll('', "id, {$field_value}");
                foreach ($result as $line) {
                    $result_array[$line['id']] = $line[$field_value];
                }
            }

            $result_array['success'] = 1;

        } else {
            $result_array['success'] = 0;
        }

        return $result_array;
    }

    function removeNewsticker($post_data)
    {
        $news_id = $post_data['news_id'];
        $result_array['success'] = 0;

        if ($news_id > 0 && !empty($post_data)) {
            // remove from mail template table
            $remove = $this->newsticker->remove($news_id);
            if ($remove) {
                $result_array['success'] = 1;
            }

        } else {
            $result_array['success'] = 0;
        }

        return $result_array;
    }

    function updateSliderListorder($post_value)
    {
        $response = self::FAILED;
        if (!empty($post_value)) {
            $rearrangeValue = $post_value['listOrder'];
            $splitValue = explode('_', $rearrangeValue);

            $update_listorder = $splitValue[0];
            $update_id = $splitValue[1];

            $where = [
                'id' => $update_id
            ];

            $field_value = 'listorder';

            $update_row = $this->homeSliderSetting->get($where);
            $old_list_value = $update_row[$field_value];

            $data = [
                $field_value => $update_listorder
            ];
            $where = [
                'id' => $update_id
            ];

            $update = $this->homeSliderSetting->save($data, $where);
            if ($update) {
                $this->homeSliderSetting->save([$field_value => $old_list_value], [$field_value => $update_listorder, 'id' => [$update_id, '!=']]);
                $response = self::SUCCESS;
            }
        }

        return $response;
    }

    function updateNewsListorder($post_value)
    {
        $response = self::FAILED;
        if (!empty($post_value)) {
            $rearrangeValue = $post_value['listOrder'];
            $splitValue = explode('_', $rearrangeValue);

            $update_listorder = $splitValue[0];
            $update_id = $splitValue[1];

            $where = [
                'id' => $update_id
            ];

            $field_value = 'listorder';

            $update_row = $this->latestNews->get($where);
            $old_list_value = $update_row[$field_value];

            $data = [
                $field_value => $update_listorder
            ];
            $where = [
                'id' => $update_id
            ];

            $update = $this->latestNews->save($data, $where);
            if ($update) {
                $this->latestNews->save([$field_value => $old_list_value], [$field_value => $update_listorder, 'id' => [$update_id, '!=']]);
                $response = self::SUCCESS;
            }
        }

        return $response;
    }

    public function addAdminUser($postdata)
    {
        $response = self::FAILED;

        if(!empty($postdata)) {

            $fullname = $postdata['fullname'];
            $username = $postdata['username'];
            $phone = $postdata['phone'];
            $user_type = $postdata['user_type'];
            $email = $postdata['email'];
            $password = $postdata['password'];

            $fullname_error = $this->validateFullName($fullname);
            $email_error = $this->validateEmail($email);
            $username_error = $this->validateUsername($username);
            $usertype_error = $this->validateUserType($user_type);
            $password_error = $this->validatePassword($postdata['password'], $postdata['confirm_password']);

            $data_object = '';
            if ($fullname_error != '') {
                $data_object = $fullname_error . ',';
            }
            if ($email_error != '') {
                $data_object .= $email_error . ',';
            }
            if ($username_error != '') {
                $data_object .= $username_error . ',';
            }
            if ($usertype_error != '') {
                $data_object .= $usertype_error . ',';
            }
            if ($password_error != '') {
                $data_object .= $password_error . ',';
            }
            if ($data_object != '') {
                return $data_object;
            }

            $hash = password_hash($password, PASSWORD_DEFAULT);
            $data = [
                'username' => $username,
                'fullname' => $fullname,
                'password' => $hash,
                'email' => $email,
                'phone' => $phone,
                'user_type' => $user_type,
                'status' => $postdata['status']
            ];

            $data_array = $this->adminInfo->save($data);
            if ($data_array) {
                $response = self::SUCCESS;
            }
        }
        return $response;
    }

    public function addAgent($postdata)
    {
        $response = self::FAILED;

        if(!empty($postdata)) {

            $agent_name = $postdata['agent_name'];
            $agent_title = $postdata['agent_title'];
            $agent_phone = $postdata['agent_phone'];
            $facebook_link = $postdata['facebook_link'];
            $twitter_link = $postdata['twitter_link'];
            $linkedin_link = $postdata['linkedin_link'];
            $vimeo_link = $postdata['vimeo_link'];
            $agent_image = basename($_FILES['agent_file']['name']);
            $fileName_extension = substr(strrchr($agent_image, '.'), 1);
            $fileTypes = array('png', 'PNG', 'jpg', 'JPG');

            $agent_name_error = $this->validateString($agent_name, 'agent name');
            $agent_title_error = $this->validateString($agent_title, 'agent title');
            $agent_phone_error = $this->validateString($agent_phone, 'agent phone');
            $facebook_link_error = $this->validateString($facebook_link, 'facebook link');
            $twitter_link_error = $this->validateString($twitter_link, 'twitter link');
            $linkedin_link_error = $this->validateString($linkedin_link, 'linkedin link');
            $vimeo_link_error = $this->validateString($vimeo_link, 'vimeo link');

            $data_object = '';
            if ($agent_name_error != '') {
                $data_object .= $agent_name_error . ',';
            }
            if ($agent_title_error != '') {
                $data_object .= $agent_title_error . ',';
            }
            if ($agent_phone_error != '') {
                $data_object .= $agent_phone_error . ',';
            }
            if ($facebook_link_error != '') {
                $data_object .= $facebook_link_error . ',';
            }
            if ($twitter_link_error != '') {
                $data_object .= $twitter_link_error . ',';
            }
            if ($linkedin_link_error != '') {
                $data_object .= $linkedin_link_error . ',';
            }
            if ($vimeo_link_error != '') {
                $data_object .= $vimeo_link_error . ',';
            }

            if ($data_object != '') {
                return $data_object;
            }

            $fileName = '';
            if (in_array($fileName_extension, $fileTypes)) {
                $uploaddir = ABSLPATHROOT . KBConstant::UPLOAD_FILE_PATH . 'agents/';
                if (!file_exists($uploaddir)) {
                    mkdir($uploaddir, 0777, true);
                }

                // Upload image
                $fileName = 'agent_' . $agent_image . '.' . $fileName_extension;
                $uploadfile = $uploaddir . $fileName;
                move_uploaded_file($_FILES['agent_file']['tmp_name'], $uploadfile);
            }

            $data = [
                'agent_name' => $agent_name,
                'agent_title' => $agent_title,
                'agent_phone' => $agent_phone,
                'facebook_link' => $facebook_link,
                'twitter_link' => $twitter_link,
                'linkedin_link' => $linkedin_link,
                'vimeo_link' => $vimeo_link,
                'agent_image' => $fileName
            ];

            $data_array = $this->agents->save($data);
            if ($data_array) {
                $response = self::SUCCESS;
            }
        }
        return $response;
    }

    public function editAgent($postdata)
    {
        $response = self::FAILED;
        if(!empty($postdata)) {

            $agent_name = $postdata['agent_name'];
            $agent_title = $postdata['agent_title'];
            $agent_phone = $postdata['agent_phone'];
            $facebook_link = $postdata['facebook_link'];
            $twitter_link = $postdata['twitter_link'];
            $linkedin_link = $postdata['linkedin_link'];
            $vimeo_link = $postdata['vimeo_link'];
            $agent_id = $postdata['agent_id'];

            $agent_name_error = $this->validateString($agent_name, 'agent name');
            $agent_title_error = $this->validateString($agent_title, 'agent title');
            $agent_phone_error = $this->validateString($agent_phone, 'agent phone');
            $facebook_link_error = $this->validateString($facebook_link, 'facebook link');
            $twitter_link_error = $this->validateString($twitter_link, 'twitter link');
            $linkedin_link_error = $this->validateString($linkedin_link, 'linkedin link');
            $vimeo_link_error = $this->validateString($vimeo_link, 'vimeo link');

            $data_object = '';
            if ($agent_name_error != '') {
                $data_object = $agent_name_error . ',';
            }
            if ($agent_title_error != '') {
                $data_object = $agent_title_error . ',';
            }
            if ($agent_phone_error != '') {
                $data_object = $agent_phone_error . ',';
            }
            if ($facebook_link_error != '') {
                $data_object = $facebook_link_error . ',';
            }
            if ($twitter_link_error != '') {
                $data_object = $twitter_link_error . ',';
            }
            if ($linkedin_link_error != '') {
                $data_object = $linkedin_link_error . ',';
            }
            if ($vimeo_link_error != '') {
                $data_object = $vimeo_link_error . ',';
            }



            if ($data_object != '') {
                return $data_object;
            }

            if($_FILES['agent_file']['name']) {
                $agent_image = basename($_FILES['agent_file']['name']);
                $fileName_extension = substr(strrchr($agent_image, '.'), 1);
                $fileTypes = array('png', 'PNG', 'jpg', 'JPG');

                if (in_array($fileName_extension, $fileTypes)) {
                    $uploaddir = ABSLPATHROOT . KBConstant::UPLOAD_FILE_PATH . 'agents/';
                    if (!file_exists($uploaddir)) {
                        mkdir($uploaddir, 0777, true);
                    }

                    // Upload image
                    $fileName = 'agent_' . $agent_image . '.' . $fileName_extension;
                    $uploadfile = $uploaddir . $fileName;
                    move_uploaded_file($_FILES['agent_file']['tmp_name'], $uploadfile);
                }
            }

            $data = [
                'agent_name' => $agent_name,
                'agent_title' => $agent_title,
                'agent_phone' => $agent_phone,
                'facebook_link' => $facebook_link,
                'twitter_link' => $twitter_link,
                'linkedin_link' => $linkedin_link,
                'vimeo_link' => $vimeo_link,
            ];
            if($_FILES['agent_file']['name']) {
                $data['agent_image'] = $fileName;
            }

            $where = [
                'id' => $agent_id
            ];
            $data_array = $this->agents->save($data, $where);
            if ($data_array) {
                $response = self::SUCCESS;
            }
        }
        return $response;
    }

    public function editAdminUser($postdata)
    {
        $response = self::FAILED;
        if(!empty($postdata)) {

            $user_id = $postdata['user_id'];
            $fullname = $postdata['fullname'];
            $phone = $postdata['phone'];
            $user_type = $postdata['user_type'];
            $email = $postdata['email'];
            $password = $postdata['password'];

            $fullname_error = $this->validateFullName($fullname);
            $email_error = $this->validateDuplicateEmail($email, $user_id);
            $usertype_error = $this->validateUserType($user_type);
            $password_error = '';
            if ($password) {
                $password_error = $this->validatePassword($postdata['password'], $postdata['confirm_password']);
            }

            $data_object = '';
            if ($fullname_error != '') {
                $data_object = $fullname_error . ',';
            }
            if ($email_error != '') {
                $data_object .= $email_error . ',';
            }
            if ($usertype_error != '') {
                $data_object .= $usertype_error . ',';
            }
            if ($password_error != '') {
                $data_object .= $password_error . ',';
            }
            if ($data_object != '') {
                return $data_object;
            }

            $data = [
                'fullname' => $fullname,
                'email' => $email,
                'phone' => $phone,
                'user_type' => $user_type,
                'status' => $postdata['status']
            ];

            if (isset($password) && $password != '') {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $data['password'] = $hash;
                $data['password_updated'] = date('Y-m-d H:i:s');;
            }

            $where = [
                'uid' => $user_id
            ];
            $data_array = $this->adminInfo->save($data, $where);
            if ($data_array) {
                $response = self::SUCCESS;
            }
        }
        return $response;
    }

    function validateFullName($name)
    {
        $return = '';
        if (strlen($name) == 0) {
            $return = 'Full name is required';
        }

        if (!$this->validateAsciiCharacter($name)) {
            $return = 'Invalid character on fullname';
        }

        return $this->removeEmptySpaces($return);
    }

    function validateString($var, $str)
    {
        $return = '';
        if (strlen($var) == 0) {
            $return = 'This '.$str.' is required';
        }

        if (!$this->validateAsciiCharacter($var)) {
            $return = 'Invalid character on this string';
        }

        return $this->removeEmptySpaces($return);
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

    function removeEmptySpaces($str)
    {
        $stripped = preg_replace(array('/\s{2,}/', '/[\t\n]/'), ' ', $str);
        $validtext = trim(strip_tags($stripped));
        $validtext = addslashes($validtext);
        return $validtext;
    }

    //Email checking
    function validateEmail($email)
    {
        $return = '';

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $return = "Please enter valid email";
        } else {
            $all_user_email = $this->adminInfo->getAll(['email' => $email]);
            if (is_array($all_user_email) && count($all_user_email) > 0) {
                $return = "E-mail address already registered in database";
            }
        }

        return $this->removeEmptySpaces($return);
    }

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
            $user = $this->adminInfo->get(['username' => $username]);
            if (!empty($user)) {
                $return = "Username Already In Use";
            }
        }

        return $this->removeEmptySpaces($return);
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

    // duplicate Email checking when updating profile
    function validateDuplicateEmail($email, $uid)
    {
        $return = '';
        $where = array(
            'email' => $email,
            'uid' => array($uid, '!=')
        );
        $user = $this->adminInfo->getAll($where);
        if (count($user) > 0) {
            $return = "E-mail address already registered in database";
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

}

?>