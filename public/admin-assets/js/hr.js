var data_tbl;

function employee_list(){
    console.log(base_url);
    console.log('function calling');

    data_tbl = $("#data_tbl").DataTable({
        "processing": true,
        "serverSide": true,
        "searching": false,
        "bPaginate": true,
        "ajax": {
            "url": base_url + "/hr/employee",
            "type": "GET",
            "dataType": "JSON",
            "data": {}
        },
        "columns": [
            // {
            //     "title": "SL",
            //     "data": null,
            //     render: function(){
            //         return data_tbl.page.info().start + data_tbl.column(0).nodes().length;
            //     }
            // },
            {
                "title": "Emp ID",
                "data": "employee_no",
            },

            {
                "title": "Name",
                "data": "name",
            },
            {
                "title": "Department",
                "data": "department_name",
            },
            {
                "title": "Designation",
                "data": "designation_name",
            },
            // {
            //     "title": "Working Location",
            //     "data": "working_location",
            // },
            // {
            //     "title": "Gender",
            //     "data": "gender",
            // },
            // {
            //     "title": "Religion",
            //     "data": "religion",
            // },
            // {
            //     "title": "Blood Group",
            //     "data": "blood_group",
            // },
            {
                "title": "Joining Date",
                "data": "joining_date",
            },
            {
                "title": "Contact Number",
                "data": "contact_name",
            },
            {
                "title": "Present Address",
                "data": "present_address",
            },
            
            {
                "title": "Salary",
                "data": "gross_salary",
                render: function (data, type, row) {
                    if (data == null) {
                        return 0;
                    } else {
                        return data;
                    }
                }
            },

             {
                "title": "Status",
                "data": "status",
                render: function (data, type, row) {
                    if (data == 1) {
                        return `Active`;
                    } else {
                        return `<b style="color:red"> Deactivated </b>`;
                    }
                }
            }, 
            {
                "title": "Action",
                "data": null,
                render: function(data, type, row){
                    var statusLabel = `Active`;
                    if (data.status == 1) {
                        statusLabel = `Deactivate`;
                    }

                    return `<div class="dropdown">
                        <a class="btn btn-default btn-flat btn-xs dropdown-toggle" type="button" data-toggle="dropdown">Action <span class="caret"></span></a>
                        <ul class="dropdown-menu dropdown-menu-right">
                            <li><a href="${base_url}/hr/employee/${data.id}"><i class="fa fa-eye"></i> Show</a></li>
                            <li><a href="${base_url}/hr/employee/${data.id}/edit"><i class="fa fa-edit"></i> Edit</a></li>
                            <li><a href="${base_url}/hr/employee/salary/assign/${data.id}"><i class="fa fa-user-o"></i> Salary</a></li>
                            <li><a onclick="activity('${base_url}/hr/employee/${data.id}/status')"><i class="fa fa-pencil"></i> ${statusLabel}</a></li>
                            <li><a onclick="deleted('${base_url}/hr/employee/${data.id}')"><i class="fa fa-close"></i> Delete</a></li>
                        </ul>
                    </div>`;
                }
            },
        ]
    });
}

function searchEmployeeData(){
    let department_id = document.getElementById('department_id').value;
    let designation_id = document.getElementById('designation_id').value;
    let status = document.getElementById('status').value;

    $("#data_tbl").dataTable().fnSettings().ajax.data.department_id = department_id;
    $("#data_tbl").dataTable().fnSettings().ajax.data.designation_id = designation_id;
    $("#data_tbl").dataTable().fnSettings().ajax.data.status = status;

    data_tbl.ajax.reload();
}

var row_id = $("#row_id").val() || 1;

function add_education(){
    let output = '';

    let degree = document.getElementById('degree').value;
    let institute = document.getElementById('institute').value;
    let university_name = document.getElementById('university_name').value;
    let major = document.getElementById('major').value;
    let result = document.getElementById('result').value;
    let passing_year = document.getElementById('passing_year').value;

    // if nothing added then don't add new row
    if(degree == '' || institute == '' || university_name == '' || major == '' || result == '' || passing_year == ''){ return; }

    output += '<tr id="row'+row_id+'">';

    output += '<td><input type="hidden" name="education_id[]" value="" /><input type="text" class="form-control" name="degree[]" value="'+degree+'" /></td>';

    output += '<td><input type="text" class="form-control" name="institute[]" value="'+institute+'" /></td>';

    output += '<td><input type="text" class="form-control" name="university_name[]" value="'+university_name+'" /></td>';

    output += '<td><input type="text" class="form-control" name="major[]" value="'+major+'" /></td>';

    output += '<td><input type="text" class="form-control" name="result[]" value="'+result+'" /></td>';

    output += '<td><input type="text" class="form-control" name="passing_year[]" value="'+passing_year+'" /></td>';

    output += '<td><button class="btn btn-sm btn-danger" onclick="removeEducation('+row_id+')">-</button></td>';

    output += '</tr>';

    $("#output").append(output);

    row_id++;

    // reset
    resetEducation();

}

function resetEducation(){
    document.getElementById('degree').value = '';
    document.getElementById('institute').value = '';
    document.getElementById('university_name').value = '';
    document.getElementById('major').value = '';
    document.getElementById('result').value = '';
    document.getElementById('passing_year').value = '';
}

function removeEducation(index){
    $("#row"+index).remove();
}
