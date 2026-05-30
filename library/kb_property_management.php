<?php
require_once ABSLPATHROOT . "library/kb_image_resize.php";

require_once ABSLPATHROOT . 'models/properties.php';
require_once ABSLPATHROOT . 'models/property_attachment.php';
require_once ABSLPATHROOT . 'models/comments.php';
require_once ABSLPATHROOT . 'models/services.php';
require_once ABSLPATHROOT . 'models/property_whitelist.php';


class KBPropertyManagement
{
    const SUCCESS = 0;
    const FAILED = 1;
    const INVALID = 2;

    protected $kBImageResize;
    protected $propertyAttachment;
    protected $services;
    protected $watchList;

    public function __construct()
    {
        $this->kBImageResize = new KBImageResize();
        $this->properties = new Properties();
        $this->propertyAttachment = new PropertyAttachment;
        $this->comments = new Comments();
        $this->services = new Services();
        $this->watchList = new PropertyWhitelist();
    }

    public function addProperty($postdata)
    {

        $response = self::FAILED;

        $property_name = $postdata['property_name'];
        $property_description = $postdata['property_description'];
        $property_location = $postdata['property_location'];
        $property_type = $postdata['property_type'];
        $price = $postdata['price'];
        $property_id = $postdata['property_id'];
        $phone = $postdata['phone'];
        $email = $postdata['email'];
        $full_area = $postdata['full_area'];
        $flat_size = $postdata['flat_size'];
        $bedrooms = $postdata['bedrooms'];
        $bathrooms = $postdata['bathrooms'];
        $garages = $postdata['garages'];
        $swimming_pool = $postdata['swimming_pool'];
        $party_rooms = $postdata['party_rooms'];
        $property_status = trim($postdata['property_status']);
        $kitchen = $postdata['kitchen'];
        $ac_rooms = $postdata['ac_rooms'];
        $internet = $postdata['internet'];
        $cable_tv = $postdata['cable_tv'];
        $balcony = $postdata['balcony'];
        $pool = $postdata['pool'];

        $property_name_error = $this->validatePropertyName($property_name);
        $property_description_error = $this->validateDescription($property_description);
        $property_location_error = $this->validateLocation($property_location);
        $property_type_error = $this->validatePropertyType($property_type);
        $price_error = $this->validatePrice($price);
        $property_id_error = $this->validatePropertyId($property_id);
        $phone_error = $this->validatePhoneNumber($phone);
        $email_error = $this->validateEmail($email);
        $full_area_error = $this->validateFullArea($full_area);
        $flat_size_error = $this->validateFlatSize($flat_size);
        $bedrooms_error = $this->validateBedrooms($bedrooms);
        $bathrooms_error = $this->validateBathrooms($bathrooms);

        $data_object = '';
        if ($property_name_error != '') {
            $data_object = $property_name_error . ',';
        }
        if ($property_description_error != '') {
            $data_object .= $property_description_error . ',';
        }
        if ($property_location_error != '') {
            $data_object .= $property_location_error . ',';
        }
        if ($property_type_error != '') {
            $data_object .= $property_type_error . ',';
        }
        if ($price_error != '') {
            $data_object .= $price_error . ',';
        }
        if ($property_id_error != '') {
            $data_object .= $property_id_error . ',';
        }
        if ($phone_error != '') {
            $data_object .= $phone_error . ',';
        }
        if ($email_error != '') {
            $data_object .= $email_error . ',';
        }
        if ($full_area_error != '') {
            $data_object .= $full_area_error . ',';
        }

        if ($flat_size_error != '') {
            $data_object .= $flat_size_error . ',';
        }

        if ($bedrooms_error != '') {
            $data_object .= $bedrooms_error . ',';
        }
        if ($bathrooms_error != '') {
            $data_object .= $bathrooms_error . ',';
        }

        if ($data_object != '') {
            return $data_object;
        }


        $data = [
            'property_name' => $property_name,
            'property_description' => $property_description,
            'property_location' => $property_location,
            'property_type' => $property_type,
            'price' => $price,
            'user_id' => $_SESSION['loggedin_userid'],
            'property_id' => $property_id,
            'phone' => $phone,
            'email' => $email,
            'full_area' => $full_area,
            'flat_size' => $flat_size,
            'bedrooms' => $bedrooms,
            'bathrooms' => $bathrooms,
            'garages' => $garages,
            'swimming_pool' => $swimming_pool,
            'party_rooms' => $party_rooms,
            'kitchen' => $kitchen,
            'ac_rooms' => $ac_rooms,
            'internet' => $internet,
            'cable_tv' => $cable_tv,
            'balcony' => $balcony,
            'pool' => $pool,
            'activated' => 0,
            'status' => $property_status
        ];

        $last_id = $this->properties->save($data);

        /*file upload start */
        $desired_dir = ABSLPATHROOT . "uploads/";
        if (is_dir($desired_dir) == false) {
            mkdir("$desired_dir");
        }

        if (isset($_FILES["files"]) && !empty($last_id)) {

            $current_time = date("Y-m-d H:i:s");
            $files_count = count($_FILES["files"]['name']);
            $files_limit = 5;
            for ($i = 0; $i < $files_limit; $i++) {
                if ($_FILES['files']['name'][$i] != '') {
                    $file_name = $_FILES['files']['name'][$i];
                    $fileTempName = $_FILES['files']['tmp_name'][$i];
                    $started = date('Y-m-d H:i:s', strtotime($_POST['start_date']));

                    $randVal = rand(1000000, 9999999999);
                    $image_result = $this->uploadImage($fileTempName, "pro_" . strtotime($current_time) . '_' . $randVal);
                    list($imageFlag, $newImage) = explode("@", $image_result);

                    if (($imageFlag == 1)) {
                        $data = array();
                        $data['property_id'] = $last_id;
                        $data['upload_date'] = $started;
                        $data['file_name'] = $newImage;
                        $data['original_name'] = $file_name;

                        $this->propertyAttachment->save($data);
                    }
                }
            }
        }
        /* file upload end */

        if ($last_id) {
            $response = self::SUCCESS;
        }

        return $response;
    }

    public function addService($postdata)
    {

        $response = self::FAILED;

        $service_name = $postdata['service_name'];
        $user_id = $postdata['user_id'];

        $service_name_error = $this->validateServiceName($service_name);

        $data_object = '';
        if ($service_name_error != '') {
            $data_object = $service_name_error . ',';
        }

        if ($data_object != '') {
            return $data_object;
        }

        $data = [
            'service_name' => $service_name,
            'user_id' => $user_id
        ];
        $last_id = $this->services->save($data);

        if ($last_id) {
            $response = self::SUCCESS;
        }

        return $response;
    }

    public function editService($postdata)
    {

        $response = self::FAILED;

        $service_name = $postdata['service_name'];
        $id = $postdata['id'];

        $service_name_error = $this->validateServiceName($service_name);

        $data_object = '';
        if ($service_name_error != '') {
            $data_object = $service_name_error . ',';
        }

        if ($data_object != '') {
            return $data_object;
        }

        $data = [
            'service_name' => $service_name
        ];
        $where = ['id'=>$id];
        $last_id = $this->services->save($data, $where);

        if ($last_id) {
            $response = self::SUCCESS;
        }

        return $response;
    }

    public function deleteService($getdata)
    {

        $response = self::FAILED;
        $service_id = $getdata['service_id'];

        if ($this->services->remove($service_id)) {
            $response = self::SUCCESS;
        }

        return $response;
    }

    public function editProperty($postdata)
    {

        $response = self::FAILED;

        $property_name = $postdata['property_name'];
        $property_description = $postdata['property_description'];
        $property_location = $postdata['property_location'];
        $property_type = $postdata['property_type'];
        $price = $postdata['price'];
        $property_id = $postdata['property_id'];
        $phone = $postdata['phone'];
        $email = $postdata['email'];
        $full_area = $postdata['full_area'];
        $flat_size = $postdata['flat_size'];
        $bedrooms = $postdata['bedrooms'];
        $bathrooms = $postdata['bathrooms'];
        $garages = $postdata['garages'];
        $swimming_pool = $postdata['swimming_pool'];
        $party_rooms = $postdata['party_rooms'];
        $property_status = trim($postdata['property_status']);
        $kitchen = $postdata['kitchen'];
        $ac_rooms = $postdata['ac_rooms'];
        $internet = $postdata['internet'];
        $cable_tv = $postdata['cable_tv'];
        $balcony = $postdata['balcony'];
        $pool = $postdata['pool'];
        $old_property_status = $postdata['old_property_status'];

        $property_name_error = $this->validatePropertyName($property_name);
        $property_description_error = $this->validateDescription($property_description);
        $property_location_error = $this->validateLocation($property_location);
        $property_type_error = $this->validatePropertyType($property_type);
        $price_error = $this->validatePrice($price);
        $property_id_error = $this->validatePropertyId($property_id);
        $phone_error = $this->validatePhoneNumber($phone);
        $email_error = $this->validateEmail($email);
        $full_area_error = $this->validateFullArea($full_area);
        $flat_size_error = $this->validateFlatSize($flat_size);
        $bedrooms_error = $this->validateBedrooms($bedrooms);
        $bathrooms_error = $this->validateBathrooms($bathrooms);

        $data_object = '';
        if ($property_name_error != '') {
            $data_object = $property_name_error . ',';
        }
        if ($property_description_error != '') {
            $data_object .= $property_description_error . ',';
        }
        if ($property_location_error != '') {
            $data_object .= $property_location_error . ',';
        }
        if ($property_type_error != '') {
            $data_object .= $property_type_error . ',';
        }
        if ($price_error != '') {
            $data_object .= $price_error . ',';
        }
        if ($property_id_error != '') {
            $data_object .= $property_id_error . ',';
        }
        if ($phone_error != '') {
            $data_object .= $phone_error . ',';
        }
        if ($email_error != '') {
            $data_object .= $email_error . ',';
        }
        if ($full_area_error != '') {
            $data_object .= $full_area_error . ',';
        }

        if ($flat_size_error != '') {
            $data_object .= $flat_size_error . ',';
        }

        if ($bedrooms_error != '') {
            $data_object .= $bedrooms_error . ',';
        }
        if ($bathrooms_error != '') {
            $data_object .= $bathrooms_error . ',';
        }

        if ($data_object != '') {
            return $data_object;
        }


        $data = [
            'property_name' => $property_name,
            'property_description' => $property_description,
            'property_location' => $property_location,
            'property_type' => $property_type,
            'price' => $price,
            'user_id' => $_SESSION['loggedin_userid'],
            'property_id' => $property_id,
            'phone' => $phone,
            'email' => $email,
            'full_area' => $full_area,
            'flat_size' => $flat_size,
            'bedrooms' => $bedrooms,
            'bathrooms' => $bathrooms,
            'garages' => $garages,
            'swimming_pool' => $swimming_pool,
            'party_rooms' => $party_rooms,
            'status' => $property_status,
            'kitchen' => $kitchen,
            'ac_rooms' => $ac_rooms,
            'internet' => $internet,
            'cable_tv' => $cable_tv,
            'balcony' => $balcony,
            'pool' => $pool
        ];

        $where = [
            'id' => $postdata['id']
        ];
        $isSaved = $this->properties->save($data, $where);


        $where = array('property_id' => $postdata['id']);
        $productImagesDetails = $this->propertyAttachment->getAll($where);
        //pr($productImagesDetails);
        // die();
        $deleted_images = array();
        if (!empty($postdata['deleted_images'])) {
            $deleted_images = explode(",", $postdata['deleted_images']);
        }

        foreach ($productImagesDetails as $result) {
            $current_time = date('Y-m-d h:i:s');
            if (in_array($result['file_name'], $deleted_images)) {
                $this->propertyAttachment->removeByField('id', $result['id']);
                if (file_exists(ABSLPATHROOT . 'uploads/property/Icon/' . $result['file_name'])) {
                    unlink(ABSLPATHROOT . 'uploads/property/Icon/' . $result['file_name']);
                    unlink(ABSLPATHROOT . 'uploads/property/Small/' . $result['file_name']);
                    unlink(ABSLPATHROOT . 'uploads/property/Thumb/' . $result['file_name']);
                    unlink(ABSLPATHROOT . 'uploads/property/' . $result['file_name']);
                }
            }
        }

        if (isset($_FILES["files"]) && !empty($postdata['id'])) {

            $current_time = date("Y-m-d H:i:s");
            $files_count = count($_FILES["files"]['name']);
            $files_limit = 5;
            for ($i = 0; $i < $files_limit; $i++) {
                if ($_FILES['files']['name'][$i] != '') {
                    $file_name = $_FILES['files']['name'][$i];
                    $fileTempName = $_FILES['files']['tmp_name'][$i];
                    $started = date('Y-m-d H:i:s', strtotime($_POST['start_date']));

                    $randVal = rand(1000000, 9999999999);
                    $image_result = $this->uploadImage($fileTempName, "pro_" . strtotime($current_time) . '_' . $randVal);
                    list($imageFlag, $newImage) = explode("@", $image_result);

                    if (($imageFlag == 1)) {
                        $data = array();
                        $data['property_id'] = $postdata['id'];
                        $data['upload_date'] = $started;
                        $data['file_name'] = $newImage;
                        $data['original_name'] = $file_name;

                        $this->propertyAttachment->save($data);
                    }
                }
            }
        }

        /* file upload end */

        if ($isSaved) {
            // -- check watchlist data
            if($property_status != $old_property_status) {
                // when property status change then notify all watchlist users
                $this->notificationForWatchListPropertyUsers($postdata['id'], $property_name, $property_status);
            }

            $response = self::SUCCESS;
        }

        return $response;
    }

    //    -- validation start
    function validatePropertyName($text)
    {
        $return = (strlen($text) == 0) ? 'Property name is required' : '';
        $return = (!$this->validateAsciiCharacter($text)) ? 'Invalid character on property name' : $return;
        return $this->removeEmptySpaces($return);
    }

    function validateServiceName($text)
    {
        $return = (strlen($text) == 0) ? 'Service name is required' : '';
        $return = (!$this->validateAsciiCharacter($text)) ? 'Invalid character on service name' : $return;
        return $this->removeEmptySpaces($return);
    }

    function validateName($text)
    {
        $return = (strlen($text) == 0) ? 'Name is required' : '';
        $return = (!$this->validateAsciiCharacter($text)) ? 'Invalid character on name field' : $return;
        return $this->removeEmptySpaces($return);
    }

    function validateMessage($text)
    {
        $return = (strlen($text) == 0) ? 'Message is required' : '';
        $return = (!$this->validateAsciiCharacter($text)) ? 'Invalid character on message field' : $return;
        return $this->removeEmptySpaces($return);
    }

    function validateDescription($text)
    {
        $return = (strlen($text) == 0) ? 'Property description is required' : '';
        $return = (!$this->validateAsciiCharacter($text)) ? 'Invalid character on property description' : $return;
        return $this->removeEmptySpaces($return);
    }

    function validateLocation($text)
    {
        $return = (strlen($text) == 0) ? 'Property location is required' : '';
        $return = (!$this->validateAsciiCharacter($text)) ? 'Invalid character on property location' : $return;
        return $this->removeEmptySpaces($return);
    }

    function validateFullArea($text)
    {
        $return = (strlen($text) == 0) ? 'Property area is required' : '';
        $return = (!$this->validateAsciiCharacter($text)) ? 'Invalid character on property area' : $return;
        return $this->removeEmptySpaces($return);
    }

    function validateFlatSize($text)
    {
        $return = (strlen($text) == 0) ? 'Flat size is required' : '';
        $return = (!$this->validateAsciiCharacter($text)) ? 'Invalid character on flat size' : $return;
        return $this->removeEmptySpaces($return);
    }

    function validateBedrooms($text)
    {
        $return = (strlen($text) == 0) ? 'Bed rooms is required' : '';
        $return = (!$this->validateAsciiCharacter($text)) ? 'Invalid character on bed rooms' : $return;
        return $this->removeEmptySpaces($return);
    }

    function validateBathrooms($text)
    {
        $return = (strlen($text) == 0) ? 'Bathrooms is required' : '';
        $return = (!$this->validateAsciiCharacter($text)) ? 'Invalid character on bathrooms' : $return;
        return $this->removeEmptySpaces($return);
    }

    function validateGarages($text)
    {
        $return = (strlen($text) == 0) ? 'Garage is required' : '';
        $return = (!$this->validateAsciiCharacter($text)) ? 'Invalid character on garage' : $return;
        return $this->removeEmptySpaces($return);
    }

    function validatePropertyId($text)
    {
        $return = (strlen($text) == 0) ? 'Property ID is required' : '';
        $return = (!$this->validateAsciiCharacter($text)) ? 'Invalid character on property id' : $return;
        return $this->removeEmptySpaces($return);
    }

    function validatePrice($text)
    {
        $return = (strlen($text) == 0) ? 'Property price is required' : '';
        $return = (!$this->validateAsciiCharacter($text)) ? 'Invalid character on property price' : $return;
        return $this->removeEmptySpaces($return);
    }

    function validatePropertyType($data)
    {
        $return = '';
        if ($data == '' || strlen($data) < 1) {
            $return = 'Please select property type';
        }
        return $this->removeEmptySpaces($return);
    }

    function validateKitchen($text)
    {
        $return = (strlen($text) == 0) ? 'Kitchen is required' : '';
        $return = (!$this->validateAsciiCharacter($text)) ? 'Invalid character on kitchen' : $return;
        return $this->removeEmptySpaces($return);
    }

    function validatePropertyStatus($text)
    {
        $return = (strlen($text) == 0) ? 'Property status is required' : '';
        $return = (!$this->validateAsciiCharacter($text)) ? 'Invalid character on property status' : $return;
        return $this->removeEmptySpaces($return);
    }

    function validateAcRooms($text)
    {
        $return = (strlen($text) == 0) ? 'AC Rooms is required' : '';
        $return = (!$this->validateAsciiCharacter($text)) ? 'Invalid character on ac rooms' : $return;
        return $this->removeEmptySpaces($return);
    }

    function validateInternet($data)
    {
        $return = '';
        if ($data == '0' || strlen($data) < 1) {
            $return = 'Please select internet';
        }
        return $this->removeEmptySpaces($return);
    }

    function validateCableTV($data)
    {
        $return = '';
        if ($data == '0' || strlen($data) < 1) {
            $return = 'Please select cable tv';
        }
        return $this->removeEmptySpaces($return);
    }

    function validateBalcony($data)
    {
        $return = '';
        if ($data == '0' || strlen($data) < 1) {
            $return = 'Please select balcony';
        }
        return $this->removeEmptySpaces($return);
    }

    function validatePool($data)
    {
        $return = '';
        if ($data == '0' || strlen($data) < 1) {
            $return = 'Please select pool';
        }
        return $this->removeEmptySpaces($return);
    }

    function validateSwimmigPool($data)
    {
        $return = '';
        if ($data == '0' || strlen($data) < 1) {
            $return = 'Please select swimming pool';
        }
        return $this->removeEmptySpaces($return);
    }

    function validatePartyRooms($data)
    {
        $return = '';
        if ($data == '0' || strlen($data) < 1) {
            $return = 'Please select party rooms';
        }
        return $this->removeEmptySpaces($return);
    }


    function validateCaptcha($captcha)
    {
        $return = '';
        if (strtolower($captcha) != strtolower($_SESSION['vercode'])) {
            $return = "Captcha entry mismatch";
        }
        return $this->removeEmptySpaces($return);
    }

    function validatePhoneNumber($phoneno)
    {
        $return = '';
        if (strlen($phoneno) < 8) {
            $return = 'Missing/incorrect phone number';
        }
        /*else if (substr($phoneno, 0, 1) != '+') {
            $return = "International phone number prefix missing";
        }  else if (!preg_match("/^[0-9]+$/", substr($phoneno, 1))) {
            $return = 'Mobile Number Contains Incorrect Characters (+ and digits only, no spaces, dashes or parantheses)';
        }*/

        return $this->removeEmptySpaces($return);
    }

    function validateEmail($email)
    {
        $return = '';

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $return = "Please enter valid email";
        }

        return $this->removeEmptySpaces($return);
    }

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

    //Upload image
    function uploadImage($Imageval, $mid)
    {
        $UserPhotoImageSizes = array(
            "Small" => array("Width" => 660, "Height" => 440, "Resize" => true, "Folder" => "Small"),
            "Thumb" => array("Width" => 190, "Height" => 140, "Resize" => true, "Folder" => "Thumb"),
            "Icon" => array("Width" => 65, "Height" => 55, "Resize" => true, "Folder" => "Icon"),
        );
        $imgresult = 0;
        $destination_path = ABSLPATHROOT . 'uploads/property/';

        $imgInfo = getimagesize($Imageval);

        $objType = explode("/", $imgInfo['mime']);
        if (strtolower($objType[0]) == "image") {
            $extension = $objType[1];
        }

        $newimage = $mid . "." . $extension;
        $target_path = $destination_path . $newimage;
        copy($Imageval, $target_path);


        foreach ($UserPhotoImageSizes as $Key => $Value) {
            $strOriginalPath = $target_path;
            $destination_newpath = $destination_path . $Value["Folder"] . '/';
            $strDestinationPath = $destination_newpath . $newimage;

            if (!file_exists($destination_newpath)) {
                mkdir($destination_newpath, 0777, true);
            }
            $this->kBImageResize->imageResize($strOriginalPath);
            $this->kBImageResize->resizeToBestFit($Value["Width"], $Value["Height"]);
            $this->kBImageResize->save($strDestinationPath);

            $imgresult = 1;
        }
        return $imgresult . '@' . $newimage;
    }

    function addComment($postdata) {
        $response = self::FAILED;
        $name = $postdata['name'];
        $email = $postdata['email'];
        $message = $postdata['message'];

        $name_error = $this->validateName($name);
        $email_error = $this->validateEmail($email);
        $message_error = $this->validateMessage($message);

        $data_object = '';
        if ($name_error != '') {
            $data_object = $name_error . ',';
        }
        if ($message_error != '') {
            $data_object .= $message_error . ',';
        }
        if ($email_error != '') {
            $data_object .= $email_error . ',';
        }
        if ($data_object != '') {
            return $data_object;
        }


        $data = [
            'property_id' => $postdata['property_id'],
            'name' => $name,
            'email' => $email,
            'message' => $message
        ];

        $last_id = $this->comments->save($data);
        if ($last_id) {
            $response = self::SUCCESS;
        }

        return $response;

    }

    public function updateUserProperty($postdata)
    {

        $response = self::FAILED;

        $property_name = $postdata['property_name'];
        $property_description = $postdata['property_description'];
        $property_location = $postdata['property_location'];
        $property_type = $postdata['property_type'];
        $price = $postdata['price'];
        $property_id = $postdata['property_id'];
        $phone = $postdata['phone'];
        $email = $postdata['email'];
        $full_area = $postdata['full_area'];
        $flat_size = $postdata['flat_size'];
        $bedrooms = $postdata['bedrooms'];
        $bathrooms = $postdata['bathrooms'];
        $garages = $postdata['garages'];
        $swimming_pool = $postdata['swimming_pool'];
        $party_rooms = $postdata['party_rooms'];
        $property_status = trim($postdata['property_status']);
        $kitchen = $postdata['kitchen'];
        $ac_rooms = $postdata['ac_rooms'];
        $internet = $postdata['internet'];
        $cable_tv = $postdata['cable_tv'];
        $balcony = $postdata['balcony'];
        $pool = $postdata['pool'];
        $old_property_status = $postdata['old_property_status'];

        $property_name_error = $this->validatePropertyName($property_name);
        $property_description_error = $this->validateDescription($property_description);
        $property_location_error = $this->validateLocation($property_location);
        $property_type_error = $this->validatePropertyType($property_type);
        $price_error = $this->validatePrice($price);
        $property_id_error = $this->validatePropertyId($property_id);
        $phone_error = $this->validatePhoneNumber($phone);
        $email_error = $this->validateEmail($email);
        $full_area_error = $this->validateFullArea($full_area);
        $flat_size_error = $this->validateFlatSize($flat_size);
        $bedrooms_error = $this->validateBedrooms($bedrooms);
        $bathrooms_error = $this->validateBathrooms($bathrooms);

        $data_object = '';
        if ($property_name_error != '') {
            $data_object = $property_name_error . ',';
        }
        if ($property_description_error != '') {
            $data_object .= $property_description_error . ',';
        }
        if ($property_location_error != '') {
            $data_object .= $property_location_error . ',';
        }
        if ($property_type_error != '') {
            $data_object .= $property_type_error . ',';
        }
        if ($price_error != '') {
            $data_object .= $price_error . ',';
        }
        if ($property_id_error != '') {
            $data_object .= $property_id_error . ',';
        }
        if ($phone_error != '') {
            $data_object .= $phone_error . ',';
        }
        if ($email_error != '') {
            $data_object .= $email_error . ',';
        }
        if ($full_area_error != '') {
            $data_object .= $full_area_error . ',';
        }

        if ($flat_size_error != '') {
            $data_object .= $flat_size_error . ',';
        }

        if ($bedrooms_error != '') {
            $data_object .= $bedrooms_error . ',';
        }
        if ($bathrooms_error != '') {
            $data_object .= $bathrooms_error . ',';
        }

        if ($data_object != '') {
            return $data_object;
        }


        $data = [
            'property_name' => $property_name,
            'property_description' => $property_description,
            'property_location' => $property_location,
            'property_type' => $property_type,
            'price' => $price,
            'property_id' => $property_id,
            'phone' => $phone,
            'email' => $email,
            'full_area' => $full_area,
            'flat_size' => $flat_size,
            'bedrooms' => $bedrooms,
            'bathrooms' => $bathrooms,
            'garages' => $garages,
            'swimming_pool' => $swimming_pool,
            'party_rooms' => $party_rooms,
            'status' => $property_status,
            'kitchen' => $kitchen,
            'ac_rooms' => $ac_rooms,
            'internet' => $internet,
            'cable_tv' => $cable_tv,
            'balcony' => $balcony,
            'pool' => $pool,
            'activated' => $postdata['activated'],
            'business_type' => $postdata['business_type']
        ];

        $isSaved = $this->properties->save($data, ['id' => $postdata['id']]);


        $where = array('property_id' => $postdata['id']);
        $productImagesDetails = $this->propertyAttachment->getAll($where);

        $deleted_images = array();
        if (!empty($postdata['deleted_images'])) {
            $deleted_images = explode(",", $postdata['deleted_images']);
        }

        foreach ($productImagesDetails as $result) {
            $current_time = date('Y-m-d h:i:s');
            if (in_array($result['file_name'], $deleted_images)) {
                $this->propertyAttachment->removeByField('id', $result['id']);
                if (file_exists(ABSLPATHROOT . 'uploads/property/Icon/' . $result['file_name'])) {
                    unlink(ABSLPATHROOT . 'uploads/property/Icon/' . $result['file_name']);
                    unlink(ABSLPATHROOT . 'uploads/property/Small/' . $result['file_name']);
                    unlink(ABSLPATHROOT . 'uploads/property/Thumb/' . $result['file_name']);
                    unlink(ABSLPATHROOT . 'uploads/property/' . $result['file_name']);
                }
            }
        }

        if (isset($_FILES["files"]) && !empty($postdata['id'])) {

            $current_time = date("Y-m-d H:i:s");
            $files_count = count($_FILES["files"]['name']);
            $files_limit = 5;
            for ($i = 0; $i < $files_limit; $i++) {
                if ($_FILES['files']['name'][$i] != '') {
                    $file_name = $_FILES['files']['name'][$i];
                    $fileTempName = $_FILES['files']['tmp_name'][$i];
                    $started = date('Y-m-d H:i:s', strtotime($_POST['start_date']));

                    $randVal = rand(1000000, 9999999999);
                    $image_result = $this->uploadImage($fileTempName, "pro_" . strtotime($current_time) . '_' . $randVal);
                    list($imageFlag, $newImage) = explode("@", $image_result);

                    if (($imageFlag == 1)) {
                        $data = array();
                        $data['property_id'] = $postdata['id'];
                        $data['upload_date'] = $started;
                        $data['file_name'] = $newImage;
                        $data['original_name'] = $file_name;

                        $this->propertyAttachment->save($data);
                    }
                }
            }
        }

        /* file upload end */

        if ($isSaved) {
            // -- check watchlist data
            if($property_status != $old_property_status) {
                // when property status change then notify all watchlist users
                $this->notificationForWatchListPropertyUsers($postdata['id'], $property_name, $property_status);
            }

            $response = self::SUCCESS;
        }
        return $response;
    }

    function getPropertyStatus($status){
        if ($status == 1) {
            $status_name = "Sold";
        } else if ($status == 2) {
            $status_name = "Under Construction";
        } else if ($status == 3) {
            $status_name = "Under Demolition";
        } else if ($status == 4) {
            $status_name = "Under Renovation";
        }  else {
            $status_name = "Available";
        }

        return $status_name;
    }

    function notificationForWatchListPropertyUsers($property_id, $property_name, $property_status){
        $joinTables = [
            'property_whitelist' => [
                'alias' => 'pw',
                'fields' => ['user_id'],
                'where' => ['property_id' => $property_id]
            ],
            'users' => [
                'alias' => 'u',
                'fields' => ['first_name','last_name','email'],
                'join_on' => ['u.uid', ' = ', 'pw.user_id']
            ]
        ];
        $watchListLine = $this->watchList->getJoinData($joinTables);
        if( !empty($watchListLine) ) {
            $property_status = $this->getPropertyStatus($property_status);
            foreach($watchListLine as $user_data) {
                $first_name = $user_data['first_name'];
                $last_name = $user_data['last_name'];
                $email = $user_data['email'];

                // -- send mail to users
                $recipient = $email;
                $subject = "Your Wishaproperty Watchlist is " . $property_status;

                $message_content = '<html><body>';
                $message_content .= '<h4 style="color:#5b5a5a;"> Hello : ' . $first_name . ' ' . $last_name . '</h4>';
                $message_content .= '<p> Your selected watchlist property:  ' . $property_name . ' status have changed, Current Status is: ' . $property_status ;
                $message_content .= '<p>&nbsp;</p><p>Kind Regards,</p>
                        <p>Wishaproperty Staff</p>
                        <p>info@wishaproperty.com</p>';
                $message_content .= '</body></html>';
                // send contact user email
                sendEMailForUser($recipient, $subject, $message_content);

            }
        }
    }
}

?>
