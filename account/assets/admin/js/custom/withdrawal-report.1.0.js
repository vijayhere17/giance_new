$('document').ready(function(){
	oTable = {};
	initiallize();

	$('#status_filter').on('change', function() {
		oTable.ajax.reload();
	});
});

function initiallize() {
    oTable = $('#tblData').DataTable({
        "responsive": true,
        "processing": true,
        "serverSide": true,
        "searchHighlight": true,
        "search": {
            "smart": true
        },
        "dom": 'Blfrtip',
        "lengthMenu": [
            [10, 25, 50, 100, 250, 500],
            ['10 rows', '25 rows', '50 rows', '100 rows', '250 rows', '500 rows']
        ],
        "buttons": [
            'excel', 'pageLength',
        ],
        "ajax": {
            "url": BASEPATH + "/admin/get-withdrawal-report",
            "data": function(d) {
                d.status_filter = $('#status_filter').val() || '';
            }
        },
        "columns": [
            {
                data: 'id',
                name: 'id',
                render: function(data, type, full, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                },
                searchable: false
            },
            {
                data: 'request_on',
                name: 'request_on',
                searchable: true
            },
            {
                data: 'w_mode',
                name: 'w_mode',
                searchable: true
            },
            {
                data: 'username',
                name: 'username',
                searchable: true
            },
            {
                data: 'name',
                name: 'name',
                searchable: true
            },
            {
                data: 'amount',
                name: 'amount',
                searchable: true
            },
            {
                data: 'admin',
                name: 'admin',
                searchable: true
            },
            {
                data: 'net',
                name: 'net',
                searchable: true
            },
            {
                data: 'payable',
                name: 'payable',
                searchable: true
            },
            {
                data: 'wallet',
                name: 'wallet',
                searchable: true
            },
            {
                data: 'txn_hash',
                name: 'txn_hash',
                render: function(data, type, full, meta) {
                    if (parseInt(full.status, 10) === 3) {
                        return full.remark || '';
                    }
                    return data == null ? '' : '<a href="https://bscscan.com/tx/' + data + '" target="_blank">View Hash</a>';
                },
                searchable: false
            },
            {
                data: 'status_label',
                name: 'status_label',
                searchable: false
            },
            {
                data: 'id',
                name: 'id',
                render: function(data, type, full, meta) {
                    var status = parseInt(full.status, 10);
                    if (status === 0 || status === 1) {
                        return '<a href="javascript:actionwithdrawalreq(' + data + ', 5, `approve`)" class="btn btn-success btn-sm"><i class="entypo-check"></i> Approve</a>&nbsp;' +
                               '<a href="javascript:approvedtxn(' + data + ')" class="btn btn-info btn-sm">Approve + Hash</a>&nbsp;' +
                               '<a href="javascript:actionwithdrawalreq(' + data + ', 3, `reject`)" class="btn btn-danger btn-sm"><i class="entypo-cancel"></i> Reject</a>';
                    }
                    if (status === 3) {
                        return '<a href="javascript:actionwithdrawalreq(' + data + ', 4, `reopen`)" class="btn btn-warning btn-sm">Reopen Pending</a>';
                    }
                    return '';
                },
                searchable: false
            }
        ]
    });
}

function actionwithdrawalreq(withdrawid, status, context)
{
	if (confirm('Are you sure you want to ' + context + ' this withdrawal request?'))
	{
		var reqObj = {
			_token : $("#token").val(),
			withdrawid : withdrawid,
			status : status
		};

		showMask();

		$.ajax({
			type: 'POST',
			url: BASEPATH + "/admin/process-withdrawal-request",
			data: reqObj,
			dataType: 'json',
			success: function(result){
				if (result.success) {
					showSuccess('Withdrawal request ' + context + ' successful!');
					oTable.draw();
				} else {
					showError(Errors[result.error_code]);
				}
				hideMask();
			},
			statusCode: {
				500: function() {
					showError("An error occurred. Please try later.");
					hideMask();
				}
			}
		});
	}
}

function approvedtxn(id){
    $("#hdnwithid").val(id);
    $('#myModal').modal({backdrop: 'static', keyboard: false});
}

function closeapprovedtxnModel(){
    $("#hdnwithid").val(0);
    $("#hash").val('');
    $('#myModal').modal('hide');
}

function getSubmitTxnHash(){
    var id = $("#hdnwithid").val();
    var hash = $("#hash").val();

    if (id == '0') {
        showToasterError('Invalid approve request.');
        return false;
    }

    if (hash == '') {
        showToasterError('Please enter a transaction hash');
        return false;
    }

    actionRequest(id, 2, hash);
}

function actionRequest(id, status, hash) {
    showMask();

    var reqObj = {
        _token: $("#token").val(),
        id: id,
        status: status,
        hash: hash
    };

    $.ajax({
        type: 'POST',
        url: BASEPATH + "/admin/process-manual-withdrawal-request",
        data: reqObj,
        dataType: 'json',
        success: function(result) {
            if (result.success) {
                toastr.success("Withdrawal request updated successfully!", "Success");
                closeapprovedtxnModel();
                oTable.draw();
            } else {
                showError(Errors[result.error_code]);
            }
            hideMask();
        },
        statusCode: {
            500: function() {
                showError("An error occurred. Please try later.");
                hideMask();
            }
        }
    });
}
