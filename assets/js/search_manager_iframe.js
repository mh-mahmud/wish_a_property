var modal_init_count = 0;
var modal_available_column = new Array();
var modal_selected_column = modalData.selected_column;
var modal_selected_condition = modalData.selected_condition;
var modal_selected_search = modalData.selected_search;
var selected_modcol_length = modal_selected_column.length;
var modal_row_count = modalData.search_row_number;
var date_rangepicker = new Array();
var old_column ='';


function initializeModal() {

    if(modalData.is_valid_search != true){
        resetAllIFrame('iframe_search_form');
        return;
    }
    modal_available_column = modalData.field_options;
    $.each(modalData.search_column, function (key, value) {
        modal_init_count++;
    });
    if (modal_row_count == 1) {
        for (var r = 1; r <= modalData.default_search_row; r++) {
            addMoreModalRows();
            $(".modal-width-col .fa-remove").hide();
        }
    }

    get_field_options = modal_available_column.filter(function (el) {
        return modal_selected_column.indexOf(el) < 0;
    });

    var op_arr = new Array();
    for (var i = 1; i <= selected_modcol_length; i++) {
        op_arr[i - 1] = new Array();
        op_arr[i - 1] = JSON.parse(JSON.stringify(get_field_options));
        op_arr[i - 1].push(modal_selected_column[i - 1]);
        addNewModalRows(op_arr, i);
        if(i <= modalData.default_search_row){
            $(".modal-width-col .fa-remove").hide();
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

function dateRangePickerOnModal(date_format,current_row,date_val) {

    if(date_val){
        var selected_date = date_val.split(' to ')
        start_date = moment(selected_date[0]);
        end_date = moment(selected_date[1]);
    }else{
        start_date = moment().subtract(30, 'days');
        end_date =moment();
    }

    $('#iframe_addedRows #rep_cls_'+current_row).daterangepicker({
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
            selected_search = start.format('MMM D, YYYY') + " to " + end.format('MMM D, YYYY');
            var date= getFormatedDate(false,date_format,selected_search);
            $('#iframe_addedRows  #search_input_value_' + current_row).val(date);
        });
}

function singleDatePickerModal(date_format,current_row) {
    $('#iframe_rep_cls_'+current_row).daterangepicker({
            singleDatePicker: true,
            showDropdowns: true
        },
        function (start, end, label) {
            selected_search = start.format('MMM D, YYYY');
            var date= getFormatedDate(true,date_format,selected_search);
            $('#iframe_addedRows #search_input_value_' + current_row).val(date);
        });
}

function generateDateInputModal(current_row, column,date_val) {

    var is_single_date_picker = false;
    var date_format = "Y-m-d";
    var selected_search = modalData.selected_keypair[column];
    if(typeof(modalData.search_column[column].is_single_date_picker)  != "undefined" &&  modalData.search_column[column].is_single_date_picker ){
         is_single_date_picker = true;
    }

    var date= getFormatedDate(is_single_date_picker,date_format,selected_search);
    $('#iframe_addedRows #search_input_value_'+current_row).val(date);
    $('#iframe_addedRows #rep_cls_' + current_row).addClass('reportrange');
    $('#iframe_addedRows #search_input_value_' + current_row).addClass('show_daterange');

    if (is_single_date_picker) {
        singleDatePickerModal(date_format,current_row);
    }else{
        if(typeof (selected_search)=='undefined'){
            date_string = moment().subtract(30, 'days').format('MMM D, YYYY') + ' to ' + moment().format('MMM D, YYYY');
            date= getFormatedDate(is_single_date_picker,date_format,date_string);
        }else  if(selected_search==''){
            date_string = moment().subtract(30, 'days').format('MMM D, YYYY') + ' to ' + moment().format('MMM D, YYYY');
            date= getFormatedDate(is_single_date_picker,date_format,date_string);
        }

        dateRangePickerOnModal(date_format,current_row,date);
    }
    if (date_rangepicker.indexOf(current_row) == -1) {
        date_rangepicker.push(current_row);
    }
    $('#iframe_addedRows #conditions_input_' + current_row).selectpicker('refresh');

}

function getRowHtmlModal(dropdown, input_field, con_field, row_count) {
    $('#iframe_addedRows').append('<div id="modalRowCount' + row_count + '" name="removeName">');

    $('#iframe_addedRows #modalRowCount'+row_count).append('<div class="form-group">');
    $('#iframe_addedRows #modalRowCount'+row_count + ' div.form-group').append('<div class="col-sm-3 search-first-part" id="cust-selectpicker">');
    $('#iframe_addedRows #modalRowCount'+row_count + ' div.form-group div.col-sm-3').append(dropdown);

    $('#iframe_addedRows #modalRowCount'+row_count + ' div.form-group').append('<div class="col-sm-2 cust_pdd search-second-part" id="cust_condition_' + row_count + '">');
    $('#iframe_addedRows #modalRowCount'+row_count + ' div.form-group div.col-sm-2').append(con_field);

    $('#iframe_addedRows #modalRowCount'+row_count + ' div.form-group').append('<div id ="rep_cls_' + row_count + '" class="col-sm-4 cust_pdd search-third-part">');
    $('#iframe_addedRows #modalRowCount'+row_count + ' div.form-group div.col-sm-4').append(input_field);

    $('#iframe_addedRows #modalRowCount'+row_count + ' div.form-group').append('<div class="col-sm-1 modal-width-col search-four-part">');
    $('#iframe_addedRows #modalRowCount'+row_count + ' div.form-group div.col-sm-1').append('<ul id="modal_rowcount_' + row_count + '" onclick="removeModalRow(this)"><li class="btn custom_btn tooltip_s" type="button" name="add_new_row" id="add_new_row"><i class="fa fa-2x fa-remove cust_remove"></i></li></ul>');

    $('#iframe_addedRows #modalRowCount'+row_count + ' div.form-group').append('<div id="addMoreModal" class="col-sm-1 modal-width-col search-five-part">');

    if(modal_init_count > row_count){
        $('#iframe_addedRows #modalRowCount'+row_count + ' div.form-group div#addMoreModal').append('<ul id="modalRowCountPlus_' + row_count + '" class="add-plus-modal" onclick="addMoreModalRows();"><li class="btn custom_btn tooltip_s" type="button" name="add_new_row" id="add_new_row"><i class="fa fa-2x fa-plus cust_plus"></i></li></ul>');
    }else{
        $('#iframe_addedRows #modalRowCount'+row_count + ' div.form-group div#addMoreModal').append('<ul id="modalRowCountPlus_' + row_count + '" style="display:none;" class="add-plus-modal" onclick="addMoreModalRows();"><li class="btn custom_btn tooltip_s" type="button" name="add_new_row" id="add_new_row"><i class="fa fa-2x fa-plus cust_plus"></i></li></ul>');
    }
}

function addMoreModalRows() {
    var total_row_count = $('#iframe_addedRows').children('div').length;
    $('.add-plus-modal').hide();

    if (total_row_count < modal_init_count) {
        var dropdown = '';
        var options = '';
        var input_field = '';
        var con_field = '';
        var is_static = false;
        if(typeof (modalData.search_column[modal_available_column[0]].is_static)!= "undefined"){
            if(modalData.search_column[modal_available_column[0]].is_static){
                is_static = true;
            }
        }
        for (var i = 0; i < modal_available_column.length; i++) {
            options += '<option value = "' + modal_available_column[i] + '">' + getModalColCaption(modal_available_column[i]) + '</option>';
        }

        dropdown = getModalColumnHtml(modal_row_count, options,is_static);
        input_field = getModalSInputHtml(modal_available_column[0], modal_row_count);
        con_field = getModalConditionHtml(modal_available_column[0], modal_row_count);
        getRowHtmlModal(dropdown, input_field, con_field, modal_row_count);

        if (typeof(modal_available_column[0]) != "undefined") {
            if (typeof(modalData.search_column[modal_available_column[0]].input_type) != "undefined" && modalData.search_column[modal_available_column[0]].input_type == 2) {
                generateDateInputModal(modal_row_count, modal_available_column[0]);
            }
        }

        if(is_static){
            $(".modal-width-col .fa-remove").hide();
        }

        var selectedValue = $("#iframe_addedRows #selectCount_" + modal_row_count).find("option:selected").val();
        dropdown = '<input class="form-control selectCount_' + modal_row_count + '" type="hidden"  value="' + selectedValue + '" />';
        $('#iframe_addedRows').append(dropdown);

        if (modalData.search_column[modal_available_column[0]].input_type == 1) {
            if (typeof(modalData.search_column[modal_available_column[0]].option_property) != "undefined") {
                if (typeof(modalData.search_column[modal_available_column[0]].option_property.input_property) != "undefined") {
                    if (typeof(modalData.search_column[modal_available_column[0]].option_property.input_property.default_selected) != "undefined") {
                        $("select[name='search_input_value_" + modal_row_count + "']").val(modalData.search_column[modal_available_column[0]].option_property.input_property.default_selected);
                    }
                    if (typeof(modalData.search_column[modal_available_column[0]].option_property.input_property.default_condition) != "undefined") {
                        $("select[name='conditions_input_" + modal_row_count + "']").val(modalData.search_column[modal_available_column[0]].option_property.input_property.default_condition);
                    }
                }
            }
        }
        var option = $('.modal-loop-count option:selected:last').val();
        var id = "selectCount_" + modal_row_count;
        hideSelectedOnchange(option, id);
        checkCallbackModal(modal_available_column[0],modal_row_count);
        modal_row_count++;
    }

    var j = 0;
    $('.modal-loop-count').each(
        function () {
            j++;
            var selectedValue = $(this).find("option:selected").val();
            var index = modal_available_column.indexOf(selectedValue);

            if (index > -1) {
                modal_available_column.splice(index, 1);
            }
        });

    pickerRefreshModal();
}

function removeModalRow(obj) {
    var id = obj.id;
    var removeNum = id.split("_")[(id.split("_")).length - 1]
    var row = $('#iframe_addedRows #modalRowCount' + removeNum);
    var sel = row.find('.modal-loop-count :selected').val();
    var name = row.find('.modal-loop-count :selected').text();

    var index = modal_available_column.indexOf(sel);
    if (index <= -1) {
        modal_available_column.splice(index, 0, sel);
    }
    row.remove();
    var hiddenRow = $('#iframe_addedRows .selectCount_' + removeNum);
    hiddenRow.remove();
    var newId = 1;
    $('.modal-loop-count').each(
        function () {
            myModalList = [];
            var selectCountId = ($(this).attr("id"));
            if (typeof (selectCountId) !== 'undefined') {
                if (selectCountId != '') {
                    $('#iframe_addedRows #' + selectCountId).children('option').each(function () {
                        myModalList.push($(this).val())
                    });
                }
                if (myModalList.indexOf(sel) == -1) {
                    $('<option>').val(sel).text(getModalColCaption(sel)).appendTo('#' + selectCountId);
                }
                $('#iframe_addedRows #' + selectCountId).selectpicker('refresh');
                $('#iframe_addedRows #' + selectCountId).nextAll().eq(1).remove();

                var newSelectCountIdMarge = selectCountId.split("_");
                var oldRowId = newSelectCountIdMarge[1];

                var arrayNewId = ['selectCount_', 'modalRowCount', 'modal_rowcount_', 'rep_cls_', 'modalRowCountPlus_', 'conditions_input_', 'search_input_value_'];

                for (var i = 0; i < arrayNewId.length; i++) {
                    var finalNewId = arrayNewId[i] + newId;

                    if (i == 0) {
                        $('#iframe_addedRows #' + selectCountId).attr('name', finalNewId);
                        $('#iframe_addedRows #' + selectCountId).attr('id', finalNewId);
                        $('#iframe_addedRows .' + selectCountId).attr('class', finalNewId);
                    } else {
                        var selectRowId = arrayNewId[i] + oldRowId;
                        $('#iframe_addedRows #' + selectRowId).attr('name', finalNewId);
                        $('#iframe_addedRows #' + selectRowId).attr('id', finalNewId);
                    }
                }
                newId++;
            }
        });
    modal_row_count--;
    var addPlus = newId - 1;
    $('#iframe_addedRows #modalRowCountPlus_' + addPlus).show();

    checkCallbackModal(sel,index);
}

function removeSelectedModal(obj) {
    var id = obj.id;
    var new_value = obj.value;
    var old_value = $('#iframe_addedRows .' + id).val();
    var splitId = id.split('_');
    old_column =old_value;

    modalData.selected_keypair[old_value]=$("#iframe_addedRows #search_input_value_"+splitId[1]).val();

    $('#iframe_addedRows .' + id).val(new_value);
    if (new_value != old_value) {
        hideSelectedOnchange(new_value, id);
        modal_available_column.splice($.inArray(new_value, modal_available_column), 1);
        if (modal_available_column.indexOf(old_value) >= 0) {
        } else {
            modal_available_column.push(old_value);
        }
        addSelectedModal(old_value, id);
    }
}
function hideSelectedOnchange(option, id) {
    $('.modal-loop-count').each(
        function () {
            var selectCountId = ($(this).attr("id"));
            if (selectCountId != id) {
                $('#iframe_addedRows #' + selectCountId).children('option[value=' + option + ']').remove();
            }
            $('#iframe_addedRows #' + selectCountId).selectpicker('refresh');
            $('#iframe_addedRows #' + selectCountId).nextAll().eq(1).remove();
        });
}

function removeSelectedCondition(obj,row_count) {
    var id = obj.id;
    var new_value = obj.value;
    var selectcount = $('#iframe_addedRows #selectCount_' + (row_count-1)).val();
    $('#iframe_addedRows #' + id).find('option').removeAttr("selected");
    $('#iframe_addedRows #' + id).find("option[value='" + new_value + "']").attr("selected", "selected");
}

function addSelectedModal(old_value, id) {
    $('.modal-loop-count').each(
        function () {
            var selectCountId = ($(this).attr("id"));
            if (selectCountId != id) {
                if (modal_available_column.indexOf(old_value) >= 0) {
                    $('<option>').val(old_value).text(getModalColCaption(old_value)).appendTo('#iframe_addedRows #' + selectCountId);
                }
                $('#iframe_addedRows #' + selectCountId).selectpicker('refresh');
                $('#iframe_addedRows #' + selectCountId).nextAll().eq(1).remove();
            }
        });
}

function checkDateRangePickerModal(rowcount,is_date_type){
    if(is_date_type == 0){
        selected_key =-1;
        $.each(date_rangepicker, function( index, value ) {
            if(rowcount == value){
                selected_key =index;
            }
        });
        if(selected_key != -1){
            $( "#iframe_addedRows .daterangepicker" ).each( function( index, element ){
                if(selected_key == index){
                    $(this).remove();
                    $('#iframe_addedRows #search_input_value_'+rowcount).removeClass('show_daterange');
                    $('#iframe_addedRows #rep_cls_'+rowcount).removeClass('reportrange');
                    date_rangepicker.pop();
                }
            });
        }
    }
}

function checkCallbackModal(column,current_row,type){
   if(typeof(modalData.search_column[column]) != 'undefined' && typeof(modalData.search_column[column].callback) != 'undefined'){
       var callback = modalData.search_column[column].callback;
       if(typeof(callback) != 'undefined'){
           window[callback](column,type);
           pickerRefreshModal();
       }
   }
}

function selectExactModal(obj) {

    var id = obj.id;
    var new_value = obj.value;
    var splitId = id.split('_');
    var text_inptval = $('#iframe_addedRows #search_input_value_' + splitId[1]).val();

    var con_field = getModalConditionHtml(new_value, splitId[1]);
    var input_field = getModalSInputHtml(new_value, splitId[1]);
    $("#iframe_addedRows #cust_condition_" + splitId[1]).empty().append(con_field);
    $('#iframe_addedRows #rep_cls_' + splitId[1]).empty().append(input_field);

    if(modalData.selected_keypair[new_value] != '' && typeof (modalData.selected_keypair[new_value]) != "undefined"){
        $("#iframe_addedRows #search_input_value_"+splitId[1]).val(modalData.selected_keypair[new_value]);
    }else{
        if(modalData.search_column[old_column].input_type == 0){
            $("#iframe_addedRows #search_input_value_"+splitId[1]).val(text_inptval);
        }
    }

    checkCallbackModal(new_value,splitId[1],'column');
    pickerRefreshModal();
    is_date_type =0;
    if (typeof(new_value) != "undefined") {
        if (typeof(modalData.search_column[new_value].input_type) != "undefined" && modalData.search_column[new_value].input_type == 2) {
            generateDateInputModal(splitId[1], new_value);
            is_date_type =1;
        }
    }
    checkDateRangePickerModal(splitId[1],is_date_type);
}

function searchKeyPressModal(e) {
    if (e.keyCode == 13) {
        id = $(e.target).attr("id");
        formid = $('#' + id).closest("form").attr("id");
        updateContentDataIFrame(formid);
        return false;
    }
}

function updateContentDataIFrame(formid) {
    var formPostedData = new Array();
    var is_required = 0;
    var arrIndex = 0;

    $("#iframe_addedRows .field_required").each(function () {
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
        for (var i = 1; i <= 10; i++) {
            if ($('#iframe_addedRows #selectCount_' + i).length) {
                formPostedData[arrIndex++] = $('#iframe_addedRows #selectCount_' + i).val();
                formPostedData[arrIndex++] = $('#iframe_addedRows #conditions_input_' + i).val();
                formPostedData[arrIndex++] = $('#iframe_addedRows #search_input_value_' + i).val();

                $('#iframe_addedRows #selectCount_' + i).attr('value', $('#iframe_addedRows #selectCount_' + i).val());
                $('#iframe_addedRows #conditions_input_' + i).attr('value', $('#iframe_addedRows #conditions_input_' + i).val());
                $('#iframe_addedRows #search_input_value_' + i).attr('value', $('#iframe_addedRows #search_input_value_' + i).val());
            }
        }
    }

    loadAjaxIFrameContent('', 1, formPostedData);
}

function clearAllIFrame() {
    $('#iframe_addedRows .valid-cls').val('');
    $('#iframe_addedRows .selectbox').val('');
    $('#iframe_addedRows .selectbox' ).selectpicker('refresh');
}

function resetAllIFrame() {
    loadAjaxIFrameContent("", 1, "");
}

function addNewModalRows(op_arr, i) {
    var options = '';
    var dropdown = '';
    var con_field = '';
    var recRow = '';
    var is_static = false;
    if(typeof (modalData.search_column[modal_selected_column[i-1]].is_static)!= "undefined"){
        if(modalData.search_column[modal_selected_column[i-1]].is_static){
            is_static = true;
        }
    }

    for (var j = 0; j < op_arr[i - 1].length; j++) {
        options += '<option value = "' + op_arr[i - 1][j] + '">' + getModalColCaption(op_arr[i - 1][j]) + '</option>';
    }

    dropdown = getModalColumnHtml(i, options,is_static);
    con_field = getModalConditionHtml(modal_selected_column[i - 1], i);
    input_field = getModalSInputHtml(modal_selected_column[i - 1], i);
    getRowHtmlModal(dropdown, input_field, con_field, i);

    //2017-09-28 Changed by NTU to fix: plus sign will not show when search item only 1
    if (i == selected_modcol_length && modal_init_count > 1 && modal_init_count != i) {
        $('#iframe_addedRows #modalRowCountPlus_' + i).css("display", "block");
    } else {
        $('#iframe_addedRows #modalRowCountPlus_' + i).css("display", " none");
    }
    if(is_static){
        $("#iframe_addedRows .modal-width-col .fa-remove").hide();
    }else if (i == 1) {
        $("#iframe_addedRows .modal-width-col .fa-remove").hide();
    }
    $('#iframe_addedRows #selectCount_' + i).val(modal_selected_column[i - 1]);
    $('#iframe_addedRows #conditions_input_' + i).val(modal_selected_condition[i - 1]);

    if (modalData.search_column[modal_selected_column[i - 1]].input_type == 1) {
        $("select[name='search_input_value_" + i + "']").val(modal_selected_search[i - 1]);
    } else {
        if (modalData.search_column[modal_selected_column[i - 1]].input_type == 2) {
            generateDateInputModal(i, modal_selected_column[i - 1],modal_selected_search[i - 1]);
        }
        $('#iframe_addedRows #search_input_value_' + i).val(modal_selected_search[i - 1]);
    }
    var hidden = '<input class="form-control selectCount_' + i + '" type="hidden"  value="' + modal_selected_column[i - 1] + '" />';
    $('#iframe_addedRows').append(hidden);
    checkCallbackModal(modal_selected_column[i - 1], i);
    pickerRefreshModal();

}

function getModalColumnHtml(row_count, options, is_static) {
    var childDropdown = '';
    var class_name ='';
    if(is_static){
        class_name = 'disable-dropdown';
    }
    childDropdown += '<select name="selectCount_' + row_count + '" id="selectCount_' + row_count + '"   class="back-color form-control modal-loop-count ' + class_name + '" onchange="removeSelectedModal(this);selectExactModal(this)">';
    childDropdown += options;
    childDropdown += '</select>';
    return childDropdown;
}

function getModalConditionHtml(selectField, row_count) {
    var con_field = "";
    if (typeof(selectField) == "undefined") return con_field;
    con_field = '<select name="conditions_input_' + row_count + '" id="conditions_input_' + row_count + '" onchange="removeSelectedCondition(this,row_count)" class="form-control back-color">';
    if (typeof(modalData.search_column[selectField].criteria) != "undefined") {
        $.each(modalData.search_column[selectField].criteria, function (key, value) {
            con_field += '<option value="' + key + '">' + value + '</option>';
        });
    }
    con_field += '</select>';
    return con_field;
}

function getModalSInputHtml(select_filed, row_count) {
    var input_field = "";
    if (typeof(select_filed) == "undefined") return input_field;
    var search_column = modalData.search_column[select_filed];

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
        if(typeof(search_column.is_requred)!="undefined" && search_column.is_requred ==1){
            class_name +=" field_required";
        }

        input_field = '<select name="search_input_value_' + row_count + '" id="search_input_value_' + row_count + '"  onchange="checkCallbackModal(\''+select_filed+'\','+row_count+',\'input\')" ' + data_live_search + '  class="form-control ' + class_name + ' back-color selectbox ">';
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
        class_name ="";
        if(typeof(search_column.is_requred)!="undefined" && search_column.is_requred ==1){
            class_name ="field_required";
        }
        if (search_column.input_type == 2 && typeof(search_column.input_type) != "undefined") {
            input_field = '<input  autocomplete="off" name="search_input_value_' + row_count + '" id="search_input_value_' + row_count + '" class="form-control valid-cls '+class_name+'"  onkeypress="return searchKeyPressModal(event);" type="text" />';
        }else{
            input_field = '<input name="search_input_value_' + row_count + '"  id="search_input_value_' + row_count + '" class="form-control valid-cls '+class_name+'" onkeypress="return searchKeyPressModal(event);" type="text" />';
        }
    }
    return input_field;
}

function pickerRefreshModal() {
    for (var t = 1; t <= modal_row_count; t++) {
        var picker_item = $('#iframe_addedRows #selectCount_' + t).val();
        if (typeof(picker_item) != "undefined") {

            if ( typeof(modalData.search_column[picker_item].input_type) != "undefined" && modalData.search_column[picker_item].input_type == 1) {
                $("#iframe_addedRows #search_input_value_" + t).selectpicker('refresh');
                $("#iframe_addedRows #search_input_value_" + t).nextAll().eq(1).remove();
            }
        }
        $("#iframe_addedRows #selectCount_" + t).selectpicker('refresh');
        $("#iframe_addedRows #selectCount_" + t).nextAll().eq(1).remove();

        $("#iframe_addedRows #conditions_input_" + t).selectpicker('refresh');
        $("#iframe_addedRows #conditions_input_" + t).nextAll().eq(1).remove();
    }
}

function getModalColCaption(value) {
    var flag = value;
    if (typeof(value) != "undefined") {
        var column = modalData.search_column[value];
        if (typeof(column.caption) != "undefined") {
            flag = modalData.search_column[value].caption;
        }else{
            flag = capitalize(flag);
        }
    }
    return flag;
}

function getsorting(str, sorted_column,sort,page,strget) {


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

    window.location = modalData.root_file + '?' + getdata;
}

function checkAll() {
    $("#check_all").change(function () {
        $("input:checkbox").prop('checked', $(this).prop("checked"));
    });
}

function showmore(column, cell_id,type,heading) {
    if(type == 'inline'){
        $("#min_" + column + "_" + cell_id).hide();
        $("#max_" + column + "_" + cell_id).show();
    }else{
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
function generateExcelReport(){
    alertify.confirm("Do you want to generate CSV report for all records?", function (e) {
        if (e) {
            $("#xls_type").val(1);
        }else{
            $("#xls_type").val(0);
        }
        $('#xls_form').submit();
    });

}
function generatePdfReport(){
    alertify.confirm("Do you want to generate Pdf report for all records?", function (e) {
        if (e) {
            $("#pdf_type").val(1);
        }else{
            $("#pdf_type").val(0);
        }
        $('#pdf_form').submit();
    });

}
