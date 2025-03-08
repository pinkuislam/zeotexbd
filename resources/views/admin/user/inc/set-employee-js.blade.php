<script>
    function setEmployee() {
        let employeeNo = $(`#employee_id`).find(':selected').attr('data-code');
        let employeeName = $(`#employee_id`).find(':selected').attr('data-name');
        $(`#employee_no`).val(employeeNo);
        $(`#name`).val(employeeName);
    }
</script>