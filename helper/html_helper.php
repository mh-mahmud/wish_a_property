<?php

class HtmlHelper
{

    public function __construct()
    {

    }


    static function globalPagingBox($totalResults, $itemPerPage = 10)
    {
        $pagingLink = '';

        $totalPages = ceil($totalResults / $itemPerPage);
        // how many link pages to show
        if ($totalPages >= 10) {
            $numLinks = 10;
        } else {
            $numLinks = $totalPages;
        }

        // create the paging links only if we have more than one page of results
        if ($totalPages > 1) {

            if (isset($_POST['paginatepgno']) && (int)$_POST['paginatepgno'] > 0) {
                $pageNumber = (int)$_POST['paginatepgno'];
            } else {
                $pageNumber = 1;
            }

            if ($pageNumber > 1) {
                $page = $pageNumber - 1;
                if ($page > 1) {
                    $prev = "<li><a style='cursor:pointer;' class=\"next_page page-numbers\" onclick='getpaginatepage($page);'>Prev</a></li>";
                } else {
                    $prev = "<li><a style='cursor:pointer;' class=\"next_page page-numbers\" onclick='getpaginatepage($page);'>Prev</a><li>";
                }

                $page = 1;
                $first = "<li><a style='cursor:pointer;' class=\"next_page page-numbers\" onclick='getpaginatepage($page);'>First</a></li>";
            } else {
                $prev = ''; // we're on page one, don't show 'previous' link
                $first = ''; // nor 'first page' link
            }

            // print 'next' link only if we're not
            // on the last page
            if ($pageNumber < $totalPages) {
                $page = $pageNumber + 1;
                $next = "<li><a style='cursor:pointer;' class=\"next_page page-numbers\" onclick='getpaginatepage($page);'>Next</a></li>";
                $last = "<li><a style='cursor:pointer;' class=\"next_page page-numbers\" onclick='getpaginatepage($totalPages);'>Last</a></li>";
            } else {
                $next = ''; // we're on the last page, don't show 'next' link
                $last = ''; // nor 'last page' link
            }

            $start = $pageNumber - (ceil($numLinks / 2));

            if ($start <= 0) {
                $start = 1;
                $end = $numLinks;
            } else {
                if ($pageNumber == $totalPages) {
                    $start = $totalPages - ($numLinks - 1);
                } else if ($pageNumber > ($totalPages - (ceil($numLinks / 2)))) {
                    $start = $totalPages - ($numLinks - 1);
                }
                $end = $pageNumber + (ceil($numLinks / 2)) - 1;
            }

            $end = min($totalPages, $end);

            $pagingLink = array();
            for ($page = $start; $page <= $end; $page++) {
                if ($page == $pageNumber) {
                    $pagingLink[] = "<li class=\"active\"><span>$page</span></li>";   // no need to create a link to current page
                } else {
                    if ($page == 1) {
                        //$pagingLink[] = " <a href=\"$self?$strGet\" class=\"bluebold\" >$page</a> ";
                        $pagingLink[] = "<li><a style='cursor:pointer;'  class=\"page-numbers\" onclick='getpaginatepage($page);'>$page</a></li>";
                    } else {
                        //$pagingLink[] = " <a href=\"$self?page=$page&$strGet\" class=\"bluebold\">$page</a> ";
                        $pagingLink[] = "<li><a style='cursor:pointer;' class=\"page-numbers\" onclick='getpaginatepage($page);'>$page</a></li>";
                    }
                }
            }
            $pagingLink = implode('', $pagingLink);
            // return the page navigation link
            $pagingLink = '<ul class="pagination">' . $first . $prev . $pagingLink . $next . $last . '</ul>';
        }
        return $pagingLink;
    }

    /*
     * this function process that if content is big then add view more button
     */
    function formatDescriptionText($product_descp, $descp_limit)
    {
        $product_descp__array = explode(" ", $product_descp);
        if (count($product_descp__array) <= $descp_limit) {
            $retval = $product_descp;
        } else {
            array_splice($product_descp__array, $descp_limit);
            $retval = implode(" ", $product_descp__array) . "<label class='view-more'>view more</label>";
            // if close tag missing then auto close tag
            $retval = $this->closedAllUnclosedTags($retval);
        }

        return $retval;
    }

    /*
     * this function check that any unclosed tag found in this content
     * if found unclosed tag then this function closed it
     */
    function closedAllUnclosedTags($html)
    {
        preg_match_all('#<(?!meta|img|br|hr|input\b)\b([a-z]+)(?: .*)?(?<![/|/ ])>#iU', $html, $result);
        $openedtags = $result[1];
        preg_match_all('#</([a-z]+)>#iU', $html, $result);
        $closedtags = $result[1];
        $len_opened = count($openedtags);
        if (count($closedtags) == $len_opened) {
            return $html;
        }
        $openedtags = array_reverse($openedtags);
        for ($i = 0; $i < $len_opened; $i++) {
            if (!in_array($openedtags[$i], $closedtags)) {
                $html .= '</' . $openedtags[$i] . '>';
            } else {
                unset($closedtags[array_search($openedtags[$i], $closedtags)]);
            }
        }
        return $html;
    }

    function prepareSql($search_input)
    {

        require_once ABSLPATHROOT . 'models/properties.php';
        $obj_property = new Properties();

        $sql = "SELECT * from properties where activated = 1";
        if (!empty($search_input)) {
            if (!empty($search_input['location'])) {
                $sql .= " AND property_location LIKE  '" . $search_input['location'] . "'";
            }

            if (!empty($search_input['bathrooms'])) {
                $sql .= " AND bathrooms = " . $search_input['bathrooms'] . "";
            }

            if (!empty($search_input['bedrooms'])) {
                $sql .= " AND bedrooms = " . $search_input['bedrooms'] . "";
            }

            if (!empty($search_input['property_type'])) {
                $sql .= " AND property_type = '" . $search_input['property_type'] . "'";
            }

            if (!empty($search_input['price_min'])) {
                $price_min_input = str_replace(' ', '', $search_input['price_min']);
                $price_min_input_format = str_replace('$', '', $price_min_input);
                $price_min = explode('-', $price_min_input_format);
                $sql .= " AND price between " . $price_min[0] . " AND " . $price_min[1] . "";
            }

            if (!empty($search_input['min_area'])) {
                $sql .= " AND flat_size >= " . $search_input['min_area'] . "";
            }

            if (!empty($search_input['max_area'])) {
                $sql .= " AND flat_size <= " . $search_input['max_area'] . "";
            }

        }

        //pr($search_input);

        $rowsPerPage = 10;

        if (isset($_POST['paginatepgno']) && (int)$_POST['paginatepgno'] > 0) {
            $page = (int)$_POST['paginatepgno'];
        } else {
            $page = 1;
        }

        //echo $sql;
        // start fetching from this row number
        $offset = ($page - 1) * $rowsPerPage;

        // total entry
        $total_property = count($obj_property->fetchAll($sql));
        $pagingLink = HtmlHelper::globalPagingBox($total_property, $rowsPerPage);
        $final_sql = $sql . " LIMIT " . $offset . ", " . $rowsPerPage;
        $my_property = $obj_property->fetchAll($final_sql);

        $property_found = count($my_property);

        return $search_result = [
            'property_found' => $property_found,
            'pagingLink' => $pagingLink,
            'my_property' => $my_property,
            'total_property' => $total_property,
            'search_type' => $search_input['property_type']
        ];
    }

    function prepareSqlForProperty($search_input, $MEMBERS)
    {

        require_once ABSLPATHROOT . 'models/properties.php';
        $obj_property = new Properties();

        $sql = "SELECT * from properties where activated = 1";
        if (!empty($search_input)) {
            if (!empty($search_input['location'])) {
                $sql .= " AND property_location LIKE  '" . $search_input['location'] . "'";
            }

            if (!empty($search_input['bedrooms'])) {
                $sql .= " AND bedrooms = " . $search_input['bedrooms'] . "";
            }

            if (!empty($search_input['bathrooms'])) {
                $sql .= " AND bathrooms = " . $search_input['bathrooms'] . "";
            }

            if (!empty($search_input['property_type'])) {
                if ($search_input['property_type'] == 'sold') {
                    $sql .= " AND status = 1";
                } else {
                    $sql .= " AND property_type = '" . $search_input['property_type'] . "'";
                }
            } elseif ($MEMBERS['user_type'] == 'sellers'){
                $sql .= " AND status = 1 OR property_type = 'sale'";
            }

            if (!empty($search_input['price_min'])) {
                $price_min_input = str_replace(' ', '', $search_input['price_min']);
                $price_min_input_format = str_replace('$', '', $price_min_input);
                $price_min = explode('-', $price_min_input_format);
                $sql .= " AND price between " . $price_min[0] . " AND " . $price_min[1] . "";
            }

            if (!empty($search_input['min_area'])) {
                $sql .= " AND flat_size >= " . $search_input['min_area'] . "";
            }

            if (!empty($search_input['max_area'])) {
                $sql .= " AND flat_size <= " . $search_input['max_area'] . "";
            }

            if (!empty($search_input['property_type']) && $search_input['property_type'] == 'agents') {
                $sql = "SELECT * from users where useractivated = 1 AND user_type = 'agents'";
            }

            if (!empty($search_input['property_type']) && $search_input['property_type'] == 'service_provider') {
                $sql = "SELECT * from users where useractivated = 1 AND user_type = 'service_provider'";
            }
            /*if (!empty($search_input['location'])) {
                $sql .= " AND property_location LIKE  '" . $search_input['location'] . "'";
            }*/

        }

        $rowsPerPage = 10;

        if (isset($_POST['paginatepgno']) && (int)$_POST['paginatepgno'] > 0) {
            $page = (int)$_POST['paginatepgno'];
        } else {
            $page = 1;
        }

        //cho $sql;
        //die();
        // start fetching from this row number
        $offset = ($page - 1) * $rowsPerPage;

        // total entry
        $total_property = count($obj_property->fetchAll($sql));
        $pagingLink = HtmlHelper::globalPagingBox($total_property, $rowsPerPage);
        $final_sql = $sql . " LIMIT " . $offset . ", " . $rowsPerPage;
        $my_property = $obj_property->fetchAll($final_sql);

        $property_found = count($my_property);

        return $search_result = [
            'property_found' => $property_found,
            'pagingLink' => $pagingLink,
            'my_property' => $my_property,
            'total_property' => $total_property,
            'search_type' => $search_input['property_type']
        ];
    }

    function prepareSqlForAgents($search_input)
    {
        global $userModel;

        if (!empty($search_input)) {
            if (!empty($search_input['agent_name'])) {
                $sql = "SELECT * from users where useractivated = 1 AND user_type = 'agents' AND  (first_name LIKE  '%" . $search_input['agent_name'] . "%' OR last_name LIKE  '%" . $search_input['agent_name'] . "%')";
            }
        }

        $rowsPerPage = 10;

        if (isset($_POST['paginatepgno']) && (int)$_POST['paginatepgno'] > 0) {
            $page = (int)$_POST['paginatepgno'];
        } else {
            $page = 1;
        }

        // start fetching from this row number
        $offset = ($page - 1) * $rowsPerPage;

        // total entry
        $total_agents = count($userModel->fetchAll($sql));
        $pagingLink = HtmlHelper::globalPagingBox($total_agents, $rowsPerPage);
        $final_sql = $sql . " LIMIT " . $offset . ", " . $rowsPerPage;
        $all_agents = $userModel->fetchAll($final_sql);

        $agent_found = count($all_agents);

        return $search_result = [
            'agent_found' => $agent_found,
            'pagingLink' => $pagingLink,
            'all_agents' => $all_agents,
            'total_agents' => $total_agents,
            'search_type' => $search_input['agent_type']
        ];
    }

    function prepareSqlForServiceProvider($search_input)
    {

        require_once ABSLPATHROOT . 'models/services.php';
        $services = new Services();
        if (!empty($search_input)) {
            if (!empty($search_input['service_name'])) {
                $sql = "SELECT u.*,s.service_name from services s, users u WHERE s.user_id = u.uid  AND service_name LIKE  '%" . $search_input['service_name'] . "%'";
            }
        }

        $rowsPerPage = 10;

        if (isset($_POST['paginatepgno']) && (int)$_POST['paginatepgno'] > 0) {
            $page = (int)$_POST['paginatepgno'];
        } else {
            $page = 1;
        }

        // start fetching from this row number
        $offset = ($page - 1) * $rowsPerPage;

        // total entry
        $total_provider = count($services->fetchAll($sql));
        $pagingLink = HtmlHelper::globalPagingBox($total_provider, $rowsPerPage);
        $final_sql = $sql . " LIMIT " . $offset . ", " . $rowsPerPage;
        $all_provider = $services->fetchAll($final_sql);

        $provider_found = count($all_provider);

        return $search_result = [
            'provider_found' => $provider_found,
            'pagingLink' => $pagingLink,
            'all_provider' => $all_provider,
            'total_provider' => $total_provider,
            'search_type' => $search_input['user_type']
        ];
    }
}