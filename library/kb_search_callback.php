<?php
if (!defined('ABSLPATHROOT')) exit('No direct script access allowed');

require_once ABSLPATHROOT . 'models/users.php';
require_once ABSLPATHROOT . 'models/country.php';


Class KBSearchCallback
{

    protected $users;
    protected $country;

    public static $editurl;
    public static $todo;
    public $datasets;
    public $current_key;

    public function __construct()
    {

        $this->users = new Users();
        $this->country = new Country();

    }

    public function globalNotifications()
    {
        $response['status'] = 0;
        $message = "";
        if (!empty($_SESSION['global_message_notification'])) {
            foreach ($_SESSION['global_message_notification'] as $value) {
                $msg_type = $value[0];
                $circle_type = $value[1];
                $msg_text = $value[2];
                $message .= '<div class="alert alert-' . $msg_type . ' fade in">
                      <button data-dismiss="alert" class="close" type="button">×</button>
                        <i class="' . $circle_type . '"></i> ' . $msg_text . '
                     </div>';
                $response['status'] = 1;
            }
            unset($_SESSION['global_message_notification']);
        }

        if ($response['status'] == 1) {
            $response['message'] = '<div id="errorMessageContent">' . $message . '</div>';
        }

        return $response;
    }

    function getNumberFormattedData($data, $number_format = 2)
    {
        $formatted_data = $data;
        if (!empty($data) && $number_format >= 0) {
            $formatted_data = number_format($data, $number_format);
        }

        return $formatted_data;
    }

    function formatNormalView($param)
    {
        $language_text = strip_tags($param['lang_us']);
        return $language_text;
    }

    public function showMultiplePropertyImages($param)
    {
        //pr($param);
        global $HOMEPAGE_ROOT;
        $smallimagepath = $HOMEPAGE_ROOT . '/uploads/property/Icon/';
        $smallimagepathlarge = $HOMEPAGE_ROOT . '/uploads/property/';
        require_once ABSLPATHROOT . 'models/property_attachment.php';
        $propertyImages = new PropertyAttachment();
        $property_id = $param['id'];
        $imgcount = 0;
        $property_name = $param['property_name'];
        $groupimg = '';
        $html = '';
        $images = $propertyImages->getAll(array('property_id' => $property_id));
        if ($images != false) {
            foreach ($images as $img) {
                $imgcount++;
                if ($imgcount == 1) {
                    $html .= '<span class="imgltr">
								 <a href="' . $smallimagepathlarge . $img['file_name'] . '"
                                    data-lightbox="example-set' . $property_id . '"
                                    id="imagelink' . $property_id . '"
                                    title="' . $property_name . '">
                                     <img src="' . $smallimagepath . $img['file_name'] . '" border="0"/>
                                 </a>
							</span>';
                } else {
                    $groupimg .= "<a href='" . $smallimagepathlarge . $img['file_name'] . "' data-lightbox='example-set" . $property_id . "'><img src='" . $smallimagepath . $img['file_name'] . "' title='" . $property_name . "'></a>";
                    $html .= '<div style="display:none;" id="subproducts">' . $groupimg . '</div>';
                }
            }
        }
        return $html;
    }

    function generateSliderListorderSelect($param)
    {
        $order_count = $param['cnt'];
        $list_order = $param['listorder'];
        $id = $param['id'];

        $html = '<select class="form-control selectpicker" style="width: auto;" name="rearrange" id="rearrange_' . $id . '" onchange="return insOrderlevel(this.value);">';
        $html .= '<option value="0">Order</option>';

        for ($i = 1; $i <= $order_count; $i++) {
            if ($i == $list_order) {
                $html .= '<option value="' . $i . '_' . $id . '" selected="selected">' . $i . '</option>';
            } else {
                $html .= '<option value="' . $i . '_' . $id . '" >' . $i . '</option>';
            }
        }
        $html .= '</select>';

        return $html;
    }

}

?>