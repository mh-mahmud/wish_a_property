var init_count = 0;
var available_column = new Array();
var selected_column = data.selected_column;
var selected_condition = data.selected_condition;
var selected_search = data.selected_search;
var selected_column_length = selected_column.length;
var row_count = data.search_row_number;
var date_rangepicker = new Array();
var old_column = '';


function initialization() {

    if (data.is_valid_search != true) {
        resetAll('search_form');
        return;
    }
    available_column = data.field_options;
    $.each(data.search_column, function (key, value) {
        init_count++;
    });
    if (row_count == 1) {
        for (var r = 1; r <= data.default_search_row; r++) {
            addMoreRows();
            $(".width-col .fa-remove").hide();
        }
    }

    get_field_options = available_column.filter(function (el) {
        return selected_column.indexOf(el) < 0;
    });

    var op_arr = new Array();
    for (var i = 1; i <= selected_column_length; i++) {
        op_arr[i - 1] = new Array();
        op_arr[i - 1] = JSON.parse(JSON.stringify(get_field_options));
        op_arr[i - 1].push(selected_column[i - 1]);
        addNewRows(op_arr, i);
        if (i <= data.default_search_row) {
            $(".width-col .fa-remove").hide();
        }
    }

    if ($.browser.chrome) {
        $(".var-btn").addClass("reset_allc").removeClass("reset_allm");
    } else if ($.browser.mozilla) {
        $(".var-btn").addClass("reset_allm").removeClass("reset_allc");
    } else {
        $(".var-btn").addClass("reset_allc").removeClass("reset_allm");
    }

}
function changeDisplaylimit(pagelimit, page, totalcount, strget) {
    var getdata = strget;
    var allowedpages = Math.ceil(totalcount / pagelimit);

    if (page != '') {
        getdata = strget.replace("&page_no=" + page, "");
        if (page > allowedpages) {
            getdata = getdata + "&page_no=" + allowedpages;
        } else {
            getdata = getdata + "&page_no=" + page;
        }
    }
    if (pagelimit != '') {
        getdata = getdata + "&per_page=" + pagelimit;
    }
    window.location = data.root_file + "?" + getdata;
}
function redirect(url) {
    window.open(url, '_blank');
}

function dateRangePickerOnSearch(date_format, current_row, date_val) {

    if (date_val) {
        var selected_date = date_val.split(' to ')
        start_date = moment(selected_date[0]);
        end_date = moment(selected_date[1]);
    } else {
        start_date = moment().subtract(30, 'days');
        end_date = moment();
    }

    $('#rep_cls_' + current_row).daterangepicker({
            format: 'YYYY-MM-DD',
            startDate: start_date,
            endDate: end_date,
            showDropdowns: true,
            "opens": "center",
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                'Last 3 Month': [moment().subtract(3, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            }
        },
        function (start, end, label) {
            is_single_date_picker = false;
            selected_search = start.format('MMM D, YYYY') + " to " + end.format('MMM D, YYYY');
            var date = getFormatedDate(is_single_date_picker, date_format, selected_search);
            $('#search_input_value_' + current_row).val(date);
        });
}

function singleDatePicker(date_format, current_row) {
    $('#rep_cls_' + current_row).daterangepicker({
            singleDatePicker: true,
            showDropdowns: true
        },
        function (start, end, label) {
            is_single_date_picker = true;
            selected_search = start.format('MMM D, YYYY');
            var date = getFormatedDate(is_single_date_picker, date_format, selected_search);
            $('#search_input_value_' + current_row).val(date);
        });
}

function getFormatedDate(is_single_date_piker, date_format, selected_search) {

    var format = "";
    if (date_format == 'ym' || date_format == "Y-m-01") {
        format = "MMM, YYYY";
    } else if (date_format == "Y-01-01") {
        format = "YYYY";
    } else {
        format = "MMM D, YYYY";
    }

    if (is_single_date_piker) {
        if (typeof(selected_search) != "undefined" && selected_search != "" && selected_search != null) {
            formated_date = moment(selected_search, format).format(format);
        } else {
            formated_date = moment().format(format);
        }
    } else {
        if (typeof(selected_search) != "undefined" && selected_search != "" && selected_search != null) {
            var dates = selected_search.split(' to ')
            formated_date = moment(dates[0], format).format(format) + ' to ' + moment(dates[1], format).format(format);
        } else {
            formated_date = moment().subtract(30, 'days').format(format) + ' to ' + moment().format(format);
        }
    }
    return formated_date;
}

function generateDateInput(current_row, column, date_val) {
    var is_single_date_picker = false;
    var date_format = "Y-m-d";
    var selected_search = data.selected_keypair[column];
    if (typeof(data.search_column[column].is_single_date_picker) != "undefined" && data.search_column[column].is_single_date_picker) {
        is_single_date_picker = true;
    }

    var date = getFormatedDate(is_single_date_picker, date_format, selected_search);
    $('#search_input_value_' + current_row).val(date);
    $('#rep_cls_' + current_row).addClass('reportrange');
    $('#search_input_value_' + current_row).addClass('show_daterange');

    if (is_single_date_picker) {
        singleDatePicker(date_format, current_row);
    } else {
        if (typeof (selected_search) == 'undefined') {
            date_string = moment().subtract(30, 'days').format('MMM D, YYYY') + ' to ' + moment().format('MMM D, YYYY');
            date = getFormatedDate(is_single_date_picker, date_format, date_string);
        } else if (selected_search == '') {
            date_string = moment().subtract(30, 'days').format('MMM D, YYYY') + ' to ' + moment().format('MMM D, YYYY');
            date = getFormatedDate(is_single_date_picker, date_format, date_string);
        }

        dateRangePickerOnSearch(date_format, current_row, date);
    }
    if (date_rangepicker.indexOf(current_row) == -1) {
        date_rangepicker.push(current_row);
    }
    $('#conditions_input_' + current_row).selectpicker('refresh');

}

function getRowHtml(dropdown, input_field, con_field, row_count) {
    $('#addedRows').append('<div id="rowCount' + row_count + '" name="removeName">');
    $('#rowCount' + row_count).append('<div class="form-group">');
    $('#rowCount' + row_count + ' div.form-group').append('<div class="col-sm-3 search-first-part" id="cust-selectpicker">');
    $('#rowCount' + row_count + ' div.form-group div.col-sm-3').append(dropdown);

    $('#rowCount' + row_count + ' div.form-group').append('<div class="col-sm-2 cust_pdd search-second-part" id="cust_condition_' + row_count + '">');
    $('#rowCount' + row_count + ' div.form-group div.col-sm-2').append(con_field);

    $('#rowCount' + row_count + ' div.form-group').append('<div id ="rep_cls_' + row_count + '" class="col-sm-4 cust_pdd search-third-part">');
    $('#rowCount' + row_count + ' div.form-group div.col-sm-4').append(input_field);

    $('#rowCount' + row_count + ' div.form-group').append('<div class="col-sm-1 width-col search-four-part">');
    $('#rowCount' + row_count + ' div.form-group div.col-sm-1').append('<ul id="rowcount_' + row_count + '" class ="update-row-count" onclick="removeRow(this)"><li class="btn custom_btn tooltip_s" type="button" name="add_new_row" id="add_new_row"><i class="fa fa-2x fa-remove cust_remove"></i></li></ul>');

    $('#rowCount' + row_count + ' div.form-group').append('<div id="addmore" class="col-sm-1 width-col search-five-part">');
    if (init_count > row_count) {
        $('#rowCount' + row_count + ' div.form-group div#addmore').append('<ul id="rowcountPlus_' + row_count + '" class ="update-row-count add-plus-icon" onclick="addMoreRows();"><li class="btn custom_btn tooltip_s" type="button" name="add_new_row" id="add_new_row"><i class="fa fa-2x fa-plus cust_plus"></i></li></ul>');
    } else {
        $('#rowCount' + row_count + ' div.form-group div#addmore').append('<ul id="rowcountPlus_' + row_count + '" style="display:none;" class ="update-row-count add-plus-icon" onclick="addMoreRows();"><li class="btn custom_btn tooltip_s" type="button" name="add_new_row" id="add_new_row"><i class="fa fa-2x fa-plus cust_plus"></i></li></ul>');
    }
}

function addMoreRows() {
    var total_row_count = $('#addedRows').children('div').length;
    $('.add-plus-icon').hide();
    if (total_row_count < init_count) {
        var dropdown = '';
        var options = '';
        var input_field = '';
        var con_field = '';
        var is_static = false;
        if (typeof (data.search_column[available_column[0]].is_static) != "undefined") {
            if (data.search_column[available_column[0]].is_static) {
                is_static = true;
            }
        }
        for (var i = 0; i < available_column.length; i++) {
            options += '<option value = "' + available_column[i] + '">' + getColumnCaption(available_column[i]) + '</option>';
        }

        dropdown = getColumnHtml(row_count, options, is_static);
        input_field = getSearchInputHtml(available_column[0], row_count);
        con_field = getConditionHtml(available_column[0], row_count);
        getRowHtml(dropdown, input_field, con_field, row_count);

        if (typeof(available_column[0]) != "undefined") {
            if (typeof(data.search_column[available_column[0]].input_type) != "undefined" && data.search_column[available_column[0]].input_type == 2) {
                generateDateInput(row_count, available_column[0]);
            }
        }

        if (is_static) {
            $(".width-col .fa-remove").hide();
        }

        var selectedValue = $("#selectCount_" + row_count).find("option:selected").val();
        dropdown = '<input class="form-control selectCount_' + row_count + '" type="hidden"  value="' + selectedValue + '" />';
        $('#addedRows').append(dropdown);

        if (data.search_column[available_column[0]].input_type == 1) {
            if (typeof(data.search_column[available_column[0]].option_property) != "undefined") {
                if (typeof(data.search_column[available_column[0]].option_property.input_property) != "undefined") {
                    if (typeof(data.search_column[available_column[0]].option_property.input_property.default_selected) != "undefined") {
                        $("select[name='search_input_value_" + row_count + "']").val(data.search_column[available_column[0]].option_property.input_property.default_selected);
                    }
                    if (typeof(data.search_column[available_column[0]].option_property.input_property.default_condition) != "undefined") {
                        $("select[name='conditions_input_" + row_count + "']").val(data.search_column[available_column[0]].option_property.input_property.default_condition);
                    }
                }
            }
        }
        var option = $('.loop-count option:selected:last').val();
        var id = "selectCount_" + row_count;
        hideSelectedOnchange(option, id);
        checkCallback(available_column[0], row_count);
        row_count++;
    }

    var j = 0;
    $('.loop-count').each(
        function () {
            j++;
            var selectedValue = $(this).find("option:selected").val();
            var index = available_column.indexOf(selectedValue);

            if (index > -1) {
                available_column.splice(index, 1);
            }
        });

    pickerRefresh();
}

function removeRow(obj) {
    var id = obj.id;
    var removeNum = id.split("_")[(id.split("_")).length - 1]
    var row = $('#rowCount' + removeNum);
    var sel = row.find('.loop-count :selected').val();
    var name = row.find('.loop-count :selected').text();
    var index = available_column.indexOf(sel);

    if (index <= -1) {
        available_column.splice(index, 0, sel);
    }
    row.remove();
    var hiddenRow = $('.selectCount_' + removeNum);
    hiddenRow.remove();
    var newId = 1;

    $('.loop-count').each(function () {
        myList = [];
        var selectCountId = ($(this).attr("id"));
        if (typeof (selectCountId) !== 'undefined') {
            if (selectCountId != '') {
                $('#' + selectCountId).children('option').each(function () {
                    myList.push($(this).val())
                });
            }

            if (myList.indexOf(sel) == -1) {
                $('<option>').val(sel).text(getColumnCaption(sel)).appendTo('#' + selectCountId);
            }
            $('#' + selectCountId).selectpicker('refresh');
            $('#' + selectCountId).nextAll().eq(1).remove();

            var newSelectCountIdMarge = selectCountId.split("_");
            var oldRowId = newSelectCountIdMarge[1];

            var arrayNewId = ['selectCount_', 'rowCount', 'rowcount_', 'rep_cls_', 'rowcountPlus_', 'conditions_input_', 'search_input_value_'];

            for (var i = 0; i < arrayNewId.length; i++) {
                var finalNewId = arrayNewId[i] + newId;

                if (i == 0) {
                    $('#' + selectCountId).attr('name', finalNewId);
                    $('#' + selectCountId).attr('id', finalNewId);
                    $('.' + selectCountId).attr('class', finalNewId);
                } else {
                    var selectRowId = arrayNewId[i] + oldRowId;
                    $('#' + selectRowId).attr('name', finalNewId);
                    $('#' + selectRowId).attr('id', finalNewId);
                }
            }
            newId++;
        }
    });

    row_count--;
    var addPlus = newId - 1;
    $('#rowcountPlus_' + addPlus).show();

    checkCallback(sel, index);
}

function removeSelected(obj) {
    var id = obj.id;
    var new_value = obj.value;
    var old_value = $('.' + id).val();
    var splitId = id.split('_');
    old_column = old_value;

    data.selected_keypair[old_value] = $("#search_input_value_" + splitId[1]).val();

    $('.' + id).val(new_value);
    if (new_value != old_value) {
        hideSelectedOnchange(new_value, id);
        available_column.splice($.inArray(new_value, available_column), 1);
        if (available_column.indexOf(old_value) >= 0) {
        } else {
            available_column.push(old_value);
        }
        addSelected(old_value, id);
    }
}
function hideSelectedOnchange(option, id) {
    $('.loop-count').each(
        function () {
            var selectCountId = ($(this).attr("id"));
            if (selectCountId != id) {
                $('#' + selectCountId).children('option[value=' + option + ']').remove();
            }
            $('#' + selectCountId).selectpicker('refresh');
            $('#' + selectCountId).nextAll().eq(1).remove();
        });
}

function removeSelectedCondition(obj, row_count) {
    var id = obj.id;
    var new_value = obj.value;
    var selectcount = $('#selectCount_' + (row_count - 1)).val();
    $('#' + id).find('option').removeAttr("selected");
    $('#' + id).find("option[value='" + new_value + "']").attr("selected", "selected");
}

function addSelected(old_value, id) {
    $('.loop-count').each(
        function () {
            var selectCountId = ($(this).attr("id"));
            if (selectCountId != id) {
                if (available_column.indexOf(old_value) >= 0) {
                    $('<option>').val(old_value).text(getColumnCaption(old_value)).appendTo('#' + selectCountId);
                }
                $('#' + selectCountId).selectpicker('refresh');
                $('#' + selectCountId).nextAll().eq(1).remove();
            }
        });
}

function checkDateRangePicker(rowcount, is_date_type) {
    if (is_date_type == 0) {
        selected_key = -1;
        $.each(date_rangepicker, function (index, value) {
            if (rowcount == value) {
                selected_key = index;
            }
        });
        if (selected_key != -1) {
            $(".daterangepicker").each(function (index, element) {
                if (selected_key == index) {
                    $(this).remove();
                    $('#search_input_value_' + rowcount).removeClass('show_daterange');
                    $('#rep_cls_' + rowcount).removeClass('reportrange');
                    date_rangepicker.pop();
                }
            });
        }
    }
}

function checkCallback(column, current_row, type) {
    if (typeof(data.search_column[column].callback) != 'undefined') {
        var callback = data.search_column[column].callback;
        if (typeof(callback) != 'undefined') {
            window[callback](column, type);
            pickerRefresh();
        }
    }
}

function selectExact(obj) {

    var id = obj.id;
    var new_value = obj.value;
    var splitId = id.split('_');
    var text_inptval = $('#search_input_value_' + splitId[1]).val();

    var con_field = getConditionHtml(new_value, splitId[1]);
    var input_field = getSearchInputHtml(new_value, splitId[1]);
    $("#cust_condition_" + splitId[1]).empty().append(con_field);
    $('#rep_cls_' + splitId[1]).empty().append(input_field);

    if (data.selected_keypair[new_value] != '' && typeof (data.selected_keypair[new_value]) != "undefined") {
        $("#search_input_value_" + splitId[1]).val(data.selected_keypair[new_value]);
    } else {

        if (data.search_column[old_column].input_type == 0) {
            $("#search_input_value_" + splitId[1]).val(text_inptval);
        }
    }

    checkCallback(new_value, splitId[1], 'column');
    pickerRefresh();
    is_date_type = 0;
    if (typeof(new_value) != "undefined") {
        if (typeof(data.search_column[new_value].input_type) != "undefined" && data.search_column[new_value].input_type == 2) {
            generateDateInput(splitId[1], new_value);
            is_date_type = 1;
        }
    }
    checkDateRangePicker(splitId[1], is_date_type);
}

function searchKeyPress(e) {
    if (e.keyCode == 13) {
        id = $(e.target).attr("id");
        formid = $('#' + id).closest("form").attr("id");
        updateContentdata(formid);
        return false;
    }
}

function updateContentdata(formid) {
    var is_required = 0;
    $(".field_required").each(function () {
        if ($(this).hasClass("selectbox")) {
            if (this.id != "") {
                if (this.value.trim() == '') {
                    is_required = 1;
                    this.focus();
                }
            }

        } else {
            if (this.value.trim() == '') {
                is_required = 1;
                this.focus();
            }
        }
    });

    if (is_required == 0) {
        $('#' + formid).submit();
    }
}

function clearAll() {
    $('.valid-cls').val('');
    $('.selectbox').val('');
    $('.selectbox').selectpicker('refresh');
}

function resetAll() {
    $.ajax({
        type: "POST",
        url: rootPath + '/administrative/route.php',
        data: {
            todo: data.todo,
            action: 'unset',
            ajax_page: 'ajax_manager'
        },
        success: function (response) {
            $('#addedRows').empty();
            window.location.href = data.root_file + '?' + data['todo_string'];
        },
        error: function (error) {
            console.log(error)
        }
    });
}

function addNewRows(op_arr, i) {

    var options = '';
    var dropdown = '';
    var con_field = '';
    var recRow = '';
    var is_static = false;
    if (typeof (data.search_column[selected_column[i - 1]].is_static) != "undefined") {
        if (data.search_column[selected_column[i - 1]].is_static) {
            is_static = true;
        }
    }

    for (var j = 0; j < op_arr[i - 1].length; j++) {
        options += '<option value = "' + op_arr[i - 1][j] + '">' + getColumnCaption(op_arr[i - 1][j]) + '</option>';
    }

    dropdown = getColumnHtml(i, options, is_static);
    con_field = getConditionHtml(selected_column[i - 1], i);
    input_field = getSearchInputHtml(selected_column[i - 1], i);
    getRowHtml(dropdown, input_field, con_field, i);

    //2017-09-28 Changed by NTU to fix: plus sign will not show when search item only 1
    if (i == selected_column_length && init_count > 1 && init_count != i) {
        $('#rowcountPlus_' + i).css("display", "block");
    } else {
        $('#rowcountPlus_' + i).css("display", " none");
    }
    if (is_static) {
        $(".width-col .fa-remove").hide();
    } else if (i == 1) {
        $(".width-col .fa-remove").hide();
    }
    $('#selectCount_' + i).val(selected_column[i - 1]);
    $('#conditions_input_' + i).val(selected_condition[i - 1]);

    if (data.search_column[selected_column[i - 1]].input_type == 1) {
        $("select[name='search_input_value_" + i + "']").val(selected_search[i - 1]);
    } else {
        if (data.search_column[selected_column[i - 1]].input_type == 2) {
            generateDateInput(i, selected_column[i - 1], selected_search[i - 1]);
        }
        $('#search_input_value_' + i).val(selected_search[i - 1]);
    }
    var hidden = '<input class="form-control selectCount_' + i + '" type="hidden"  value="' + selected_column[i - 1] + '" />';
    $('#addedRows').append(hidden);
    checkCallback(selected_column[i - 1], i);
    pickerRefresh();

}

function getColumnHtml(row_count, options, is_static) {
    var childDropdown = '';
    var class_name = '';
    if (is_static) {
        class_name = 'disable-dropdown';
    }
    childDropdown += '<select name="selectCount_' + row_count + '" id="selectCount_' + row_count + '"   class="back-color form-control loop-count ' + class_name + '" onchange="removeSelected(this);selectExact(this)">';
    childDropdown += options;
    childDropdown += '</select>';
    return childDropdown;
}

function getConditionHtml(selectField, row_count) {
    var con_field = "";
    if (typeof(selectField) == "undefined") return con_field;
    con_field = '<select name="conditions_input_' + row_count + '" id="conditions_input_' + row_count + '" onchange="removeSelectedCondition(this,row_count)" class="form-control back-color">';
    if (typeof(data.search_column[selectField].criteria) != "undefined") {
        $.each(data.search_column[selectField].criteria, function (key, value) {
            con_field += '<option value="' + key + '">' + value + '</option>';
        });
    }
    con_field += '</select>';
    return con_field;
}

function getSearchInputHtml(select_filed, row_count) {
    var input_field = "";
    if (typeof(select_filed) == "undefined") return input_field;
    var search_column = data.search_column[select_filed];

    if (search_column.input_type == 1 && typeof(search_column.option_property) != "undefined") {
        var search_option = search_column.option_property;
        var data_live_search = '';
        var class_name = '';
        if (typeof(search_option.input_property.is_search_allow) != "undefined") {
            if (search_option.input_property.is_search_allow == 1) {
                data_live_search = ' data-live-search="true"';
            }
        }
        if (typeof(search_option.input_property.class_name) != "undefined") {
            class_name = search_option.input_property.class_name;
        }
        if (typeof(search_column.is_requred) != "undefined" && search_column.is_requred == 1) {
            class_name += " field_required";
        }

        input_field = '<select name="search_input_value_' + row_count + '" id="search_input_value_' + row_count + '"  onchange="checkCallback(\'' + select_filed + '\',' + row_count + ',\'input\')" ' + data_live_search + '  class="form-control ' + class_name + ' back-color selectbox ">';
        if (typeof(search_option.input_property.default_options) != "undefined") {
            $.each(search_option.input_property.default_options, function (key, value) {
                input_field += '<option value="' + key + '">' + value + '</option>';
            });
        }
        if (typeof(search_option.input_option) != "undefined") {
            $.each(search_option.input_option, function (key, value) {
                input_field += '<option value="' + value.key + '">' + value.value + '</option>';
            });
        }
        input_field += '</select>';
    } else {
        class_name = "";
        if (typeof(search_column.is_requred) != "undefined" && search_column.is_requred == 1) {
            class_name = "field_required";
        }
        if (search_column.input_type == 2 && typeof(search_column.input_type) != "undefined") {
            input_field = '<input  autocomplete="off" name="search_input_value_' + row_count + '" id="search_input_value_' + row_count + '" class="form-control valid-cls ' + class_name + '"  onkeypress="return searchKeyPress(event);" type="text" />';
        } else {
            input_field = '<input name="search_input_value_' + row_count + '"  id="search_input_value_' + row_count + '" class="form-control valid-cls ' + class_name + '" onkeypress="return searchKeyPress(event);" type="text" />';
        }
    }
    return input_field;
}

function pickerRefresh() {
    for (var t = 1; t <= row_count; t++) {
        var picker_item = $('#selectCount_' + t).val();
        if (typeof(picker_item) != "undefined") {

            if (typeof(data.search_column[picker_item].input_type) != "undefined" && data.search_column[picker_item].input_type == 1) {
                $("#search_input_value_" + t).selectpicker('refresh');
                $("#search_input_value_" + t).nextAll().eq(1).remove();
            }
        }
        $("#selectCount_" + t).selectpicker('refresh');
        $("#selectCount_" + t).nextAll().eq(1).remove();

        $("#conditions_input_" + t).selectpicker('refresh');
        $("#conditions_input_" + t).nextAll().eq(1).remove();
    }
}

function getColumnCaption(value) {
    var flag = value;
    if (typeof(value) != "undefined") {
        var column = data.search_column[value];

        if (typeof(column.caption) != "undefined") {
            flag = data.search_column[value].caption;
        } else {
            flag = capitalize(flag);
        }
    }
    return flag;
}

function capitalize(string) {
    var splitStr = string.split(/[_-]+/);
    var fullStr = '';
    $.each(splitStr, function (index) {
        var currentSplit = splitStr[index].charAt(0).toUpperCase() + splitStr[index].slice(1);
        fullStr += currentSplit + " "
    });
    return fullStr;
}

function getsorting(str, sorted_column, sort, page, strget) {
    var sort_type = str[0][1];
    var getdata = strget;

    if (page != '') {
        getdata = getdata.replace("&page_no=" + page, "");
        getdata = getdata + "&page_no=" + page;
    } else {
        getdata = getdata.replace("&page_no=" + page, "");
    }

    if (sort == '' || typeof(sort) == 'undefined') {
        getdata = getdata + "&sort=" + sorted_column + "_order_" + sort_type;
    } else {
        getdata = getdata.replace("&sort=" + sort, "&sort=" + sorted_column + "_order_" + sort_type);
    }

    $("#search_loader").show();

    window.location = data.root_file + '?' + getdata;
}

function checkAll() {
    $("#check_all").change(function () {
        $("input:checkbox").prop('checked', $(this).prop("checked"));
    });
}

function showmore(column, cell_id, type, heading) {
    if (type == 'inline') {
        $("#min_" + column + "_" + cell_id).hide();
        $("#max_" + column + "_" + cell_id).show();
    } else {
        $("#modal-1").modal();
        $(".modal-title").html(heading);
        $(".modal-body").html($("#max_" + column + "_" + cell_id).html());
        $("#modal-1").removeClass('modal-loader');
    }
}

function showless(column, cell_id) {
    $("#min_" + column + "_" + cell_id).show();
    $("#max_" + column + "_" + cell_id).hide();
}

function upadate_remove_row_pagination_text() {

    var pagination_start_count = $("#pagination_start_count").val();
    var pagination_end_count = $("#pagination_end_count").val();
    var pagination_total = $("#pagination_total").val();

    str = "Displaying " + pagination_start_count + " to " + (pagination_end_count - 1) + " out of " + (pagination_total - 1) + " entries";
    $("#pagination_sumery").html(str);

    $("#pagination_end_count").val(pagination_end_count - 1);
    $("#pagination_total").val(pagination_total - 1);
}

function updateSwitchBox(id, flag) {
    if (flag == 1) {
        $("#" + id).removeClass('fa-toggle-off').addClass('fa-toggle-on');
        $("#" + id).parent().attr('data-original-title', 'On')
    } else {
        $("#" + id).removeClass('fa-toggle-on').addClass('fa-toggle-off');
        $("#" + id).parent().attr('data-original-title', 'Off')
    }
}
function generateExcelReport() {
    alertify.confirm("Do you want to generate CSV report for all records?", function (e) {
        if (e) {
            $("#xls_type").val(1);
        } else {
            $("#xls_type").val(0);
        }
        $('#xls_form').submit();
    });

}
function generatePdfReport() {
    alertify.confirm("Do you want to generate Pdf report for all records?", function (e) {
        if (e) {
            $("#pdf_type").val(1);
        } else {
            $("#pdf_type").val(0);
        }
        $('#pdf_form').submit();
    });

}

function loadAjaxIFrameContent(pageTitle, pageNo, searchData) {
    var pageURL = rootPath + '/administrative/route.php';
    var isFirstLoad = 1;
    var searchBoxHeight = 0;

    if ($('#iframe_savelistdata').length) {
        searchBoxHeight = $('#iframe_savelistdata').height();
    }

    if (typeof pageNo != 'undefined' && parseInt(pageNo) > 1 ) {
        pageURL += '?page_no='+pageNo;
    }else {
        pageURL += '?page_no=1';
    }
    if (typeof pageTitle === 'undefined' || pageTitle == "" || pageTitle == null) {
        pageTitle = $(".modal-header h3").html();
        isFirstLoad = 0;
    }

    if (typeof searchData !== 'undefined' && searchData != "" && searchData != null) {
        if (searchData == "reform") {
            var searchData = new Array();
            var arrIndex = 0;
            for (var i = 1; i <= 10; i++){
                if ($('#iframe_addedRows #selectCount_' + i).length) {
                    searchData[arrIndex++] = $('#iframe_addedRows #selectCount_' + i).val();
                    searchData[arrIndex++] = $('#iframe_addedRows #conditions_input_' + i).val();
                    searchData[arrIndex++] = $('#iframe_addedRows #search_input_value_' + i).val();

                    $('#iframe_addedRows #selectCount_' + i).attr('value', $('#iframe_addedRows #selectCount_' + i).val());
                    $('#iframe_addedRows #conditions_input_' + i).attr('value', $('#iframe_addedRows #conditions_input_' + i).val());
                    $('#iframe_addedRows #search_input_value_' + i).attr('value', $('#iframe_addedRows #search_input_value_' + i).val());
                }
            }
        }
    }

    var searchURL = "";
    if (searchData.length >= 3) {
        var inputIndex = 1;
        var arrIndex = 0;
        for (var i = 0; i < searchData.length; i++){
            arrIndex = (i % 3) + 1;
            if (arrIndex % 3 == 0) {
                searchURL += "&search_input_value_"+inputIndex+"="+searchData[i];
                inputIndex++;
            } else if (arrIndex % 2 == 0) {
                searchURL += "&conditions_input_"+inputIndex+"="+searchData[i];
            } else {
                searchURL += "&selectCount_"+inputIndex+"="+searchData[i];
            }
        }
        pageURL += searchURL;
    }

    $(".modal-header h3").html(pageTitle);
    $(".modal").fadeIn(1200);
    $('.modal-content').attr('style', 'width: 1000px !important');

    $.ajax({
        url: pageURL,
        data: viewDBConfig,
        dataType: 'text',
        type: 'POST',
        success: function (data) {
            if (data != '') {
                if (isFirstLoad == 1) {
                    $(".modal-body").html('');
                }

                $('.modal-body').html(data);

                if (searchBoxHeight > 0) {
                    searchBoxHeight = searchBoxHeight + 15;
                    $("#iframe_savelistdata").css({"height":searchBoxHeight+"px"});
                }
            }
        },
        error: function (XMLHttpRequest, textStatus, errorThrown) {
            console.log(XMLHttpRequest.responseText);
        }
    });
}