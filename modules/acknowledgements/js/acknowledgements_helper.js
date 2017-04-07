function hideFilter(obj) {
    'use strict';

     var heading = $(obj);
     var arrow   = $(obj).children('.arrow');
     if (heading.hasClass('panel-collapsed')) {
            // expand the panel
            heading.parents('.panel').find('.panel-body').slideDown();
            heading.removeClass('panel-collapsed');
            arrow.removeClass('glyphicon-chevron-down').addClass('glyphicon-chevron-up');
        } else {
            // collapse the panel
            heading.parents('.panel').find('.panel-body').slideUp();
            heading.addClass('panel-collapsed');
            arrow.removeClass('glyphicon-chevron-up').addClass('glyphicon-chevron-down');
        }
}

$(function(){
        $('input[name=dob]').datepicker({
            dateFormat: 'yy-mm-dd',
            changeMonth: true,
            changeYear: true
        });
});

function setValue(element, valueToSelect) {
  "use strict";

  $("#" + element).val(valueToSelect);
  $("#" + element + " input").val(valueToSelect);
  $("#" + element + " select").val(valueToSelect);
}
function getValue(element) {
    var a = $("#" + element).val();
    var b = $("#" + element + " input").val();
    var c = $("#" + element + " select").val();
    
    console.log(element, a, b, c);
    if (a != null) {
        return a;
    }
    if (b != null) {
        return b;
    }
    if (c != null) {
        return c;
    }
    return null;
}
//TODO
function editModal() {
  "use strict";

  var id = this.id;
  $("#editModal").modal();

  $.ajax({
    type: "GET",
    url: "/acknowledgements/ajax/fetch.php",
    data: {id: id},
    async: false,
    dataType: "json",
    success: function(data) {
      //Pre-populate the form with the existing values
      setValue("editOrdering", data.ordering);
      setValue("editFullName", data.full_name);
      setValue("editCitationName", data.citation_name);
      setValue("affiliationEdit", data.affiliations);
      setValue("degreeEdit", data.degrees);
      setValue("roleEdit", data.roles);
      setValue("editStartDate", data.start_date);
      setValue("editEndDate", data.end_date);
      setValue("editPresent", data.present);
    }
  });

  $("#postEdit").click(function(e) {
    e.preventDefault();
    putEdit(id);
  });
  $("#cancelEditButton").click(function() {
    $(".dialog-form-edit").dialog("close");
  });

  return false;
}
function putEdit(id) {
  var data = {
    id: id,
    ordering: getValue("editOrdering"),
    full_name: getValue("editFullName"),
    citation_name: getValue("editCitationName"),
    affiliations: getValue("affiliationEdit"),
    degrees: getValue("degreeEdit"),
    roles: getValue("roleEdit"),
    start_date: getValue("editStartDate"),
    end_date: getValue("editEndDate"),
    present: getValue("editPresent"),
  };

  $.ajax({
    type: "PUT",
    url: "/acknowledgements/ajax/update.php",
    data: data,
    success: function() {
      $(".edit-success").show();
      $("#editModal").modal('hide');
      setTimeout(function() {
        location.reload()
      }, 1000);
    },
    error: function() {
    }
  });
}

$(document).ready(function () {
    $.ajax({
        type: "GET",
        url : "/acknowledgements/ajax/fetch_all_viewable_center.php",
        dataType: "json",
        success: function (data) {
            for (i in data.arr) {
                $("#select-center").append(
                    $("<option>")
                        .val(data.arr[i].id)
                        .append(document.createTextNode(data.arr[i].name))
                );
            }
            $("#select-center-prompt").attr("disabled", true);
            const qs = QueryString.get();
            if (qs.center_id) {
                $("#select-center").val(qs.center_id);
            }
        }
    });
});
function arrayOnKey (arr, key) {
    if (arr == null) {
        return null;
    }
    var result = [];
    for (var i=0; i<arr.length; ++i) {
        result.push(arr[i][key]);
    }
    return result;
}
function joinOnKey (arr, key, separator) {
    if (arr == null) {
        return "";
    }
    if (arr.length == 0) {
        return "";
    }
    var result = arr[0][key];
    for (var i=1; i<arr.length; ++i) {
        result += separator + arr[i][key];
    }
    return result;
}
function showAcknowledgementForm (args) {
    $("#acknowledgement-form-dialog-title").html(args.title);
    $("#acknowledgement-form-submit")
        .html(args.submit_text)
        .data("args", args);
    
    var data = args.data;
    
    var disabled = (args.disabled == null) ?
        false : args.disabled;
    
    $("#ack-id").val(data.id);
    $("#ack-center-id").val(data.center_id);
    $("#ack-full-name").val(data.full_name).prop("disabled", disabled);
    $("#ack-citation-name").val(data.citation_name).prop("disabled", disabled);
    $("#ack-affiliation-arr").val(arrayOnKey(data.affiliation_arr, "id")).prop("disabled", disabled);
    $("#ack-degree-arr").val(arrayOnKey(data.degree_arr, "id")).prop("disabled", disabled);
    $("#ack-role-arr").val(arrayOnKey(data.role_arr, "id")).prop("disabled", disabled);
    $("#ack-start-date").val(data.start_date).prop("disabled", disabled);
    $("#ack-end-date").val(data.end_date).prop("disabled", disabled);
    
    var in_study_at_present = "";
    if (data.in_study_at_present != null) {
        in_study_at_present = data.in_study_at_present ?
            "1" : "0";
    }
    $("#ack-in-study-at-present").val(in_study_at_present).prop("disabled", disabled);
    
    $("#acknowledgement-form-dialog").modal();
}
function loadOptions (select_id, url, center_id) {
    $.ajax({
        type: "GET",
        url : url,
        data: {
            "center_id":center_id
        },
        dataType: "json",
        success: function (data) {
            for (i in data.arr) {
                const cur = data.arr[i];
                $("#"+select_id).append(
                    $("<option>", {
                        text:cur.title
                    })
                        .val(cur.id)
                )
            }
        }
    });
}
function loadAcknowledgementForm (center_id) {
    loadOptions("ack-affiliation-arr", "/acknowledgements/ajax/fetch_all_affiliation_of_center.php", center_id);
    loadOptions("ack-degree-arr", "/acknowledgements/ajax/fetch_all_degree_of_center.php", center_id);
    loadOptions("ack-role-arr", "/acknowledgements/ajax/fetch_all_role_of_center.php", center_id);
    
    $("#acknowledgement-form-submit").click(function (e) {
        e.preventDefault();
        
        const args = $(this).data("args");
        console.log(args);
        
        const form_data = {
            "id":$("#ack-id").val(),
            "center_id":$("#ack-center-id").val(),
            "full_name":$("#ack-full-name").val(),
            "citation_name":$("#ack-citation-name").val(),
            
            "affiliation_arr":$("#ack-affiliation-arr").val(),
            "degree_arr":$("#ack-degree-arr").val(),
            "role_arr":$("#ack-role-arr").val(),
            
            "start_date":$("#ack-start-date").val(),
            "end_date":$("#ack-end-date").val(),
            "in_study_at_present":$("#ack-in-study-at-present").val()
        };
        
        if (form_data.start_date === "") {
            delete form_data.start_date;
        }
        if (form_data.end_date === "") {
            delete form_data.end_date;
        }
        if (form_data.in_study_at_present === "") {
            delete form_data.in_study_at_present;
        }
        
        if (form_data.affiliation_arr == null) {
            delete form_data.affiliation_arr;
        }
        if (form_data.degree_arr == null) {
            delete form_data.degree_arr;
        }
        if (form_data.role_arr == null) {
            delete form_data.role_arr;
        }
        
        console.log(form_data);
        console.log(args.method);
        console.log(args.action);
        $.ajax({
            method: args.method,
            url : args.action,
            contentType: "application/x-www-form-urlencoded",
            data: form_data,
            dataType: "json",
            success: function (data) {
                console.log("Success");
                if (args.callback) {
                    args.callback();
                }
            },
            error: function (error) {
                console.log(error);
            }
        });
    });
}
function setRowValues (tr, data) {
    const arr = $(tr).find("td");
    $(arr[0]).empty().append(document.createTextNode(data.full_name));
    $(arr[1]).empty().append(document.createTextNode(data.citation_name));
}/*
$(document).ready(function () {
    const qs = QueryString.get();
    if (!qs.center_id) {
        return;
    }
    $("#btn-add-acknowledgement").click(function () {
        showAcknowledgementForm({
            method:"POST",
            action:"/acknowledgements/ajax/insert.php",
            submit_text: "Add",
            title: "Add Acknowledgement",
            data: {
                center_id: qs.center_id
            }
        });
    }).css("display", "");
    loadAcknowledgementForm(qs.center_id);
    $.ajax({
        type: "GET",
        url : "/acknowledgements/ajax/fetch_all_of_center.php",
        data: qs,
        dataType: "json",
        success: function (data) {
            console.log(data);
            for (i in data.arr) {
                const cur = data.arr[i];
                let   in_study_at_present = "Unknown";
                
                if (cur.in_study_at_present != null) {
                    in_study_at_present = cur.in_study_at_present ?
                        "Yes" : "No";
                }
                
                const tr = $("<tr>");
                tr
                    .append($("<td>", {
                        text:cur.full_name
                    }))
                    .append($("<td>", {
                        text:cur.citation_name
                    }))
                    .append($("<td>", {
                        text:joinOnKey(cur.affiliation_arr, "title", ", ")
                    }))
                    .append($("<td>", {
                        text:joinOnKey(cur.degree_arr, "title", ", ")
                    }))
                    .append($("<td>", {
                        text:joinOnKey(cur.role_arr, "title", ", ")
                    }))
                    .append($("<td>", {
                        text:cur.start_date
                    }))
                    .append($("<td>", {
                        text:cur.end_date
                    }))
                    .append($("<td>", {
                        text:in_study_at_present
                    }))
                    .append($("<td>")
                        .append(
                            $("<a>", {
                                text:"Edit"
                            })
                                .attr("href", "#/")
                                .click(function () {
                                    showAcknowledgementForm({
                                        method:"PUT",
                                        action:"/acknowledgements/ajax/update.php?id="+encodeURIComponent(this.id),
                                        submit_text: "Edit",
                                        title: "Edit Acknowledgement",
                                        data: this,
                                        callback: function (data) {
                                            setRowValues(tr, data);
                                        }
                                    });
                                }.bind(cur))
                        )
                    )
                    .append($("<td>")
                        .append(
                            $("<a>", {
                                text:"Delete"
                            })
                                .attr("href", "#/")
                                .click(function () {
                                    showAcknowledgementForm({
                                        disabled: true,
                                        method:"DELETE",
                                        action:"/acknowledgements/ajax/delete.php?id="+encodeURIComponent(this.id),
                                        submit_text: "Delete",
                                        title: "Delete Acknowledgement",
                                        data: this,
                                        callback: function () {
                                            tr.remove();
                                        }
                                    });
                                }.bind(cur))
                        )    
                    );

                $("#acknowledgement-tbody").append(tr);
            }
            $("#acknowledgement-table").css("display", "");
        }
    });
});*/