jQuery(document).ready(function() {
    oTable = {};
    window.withdrawalStatusFilter = '';
    initiateDataTable();

    $(document).on('click', '.status-filter', function() {
        $('.status-filter').removeClass('active');
        $(this).addClass('active');
        window.withdrawalStatusFilter = $(this).attr('data-status') || '';
        oTable.ajax.reload();
    });
});

const withrawalAddress = (address) => {
    if (!address) {
        return '';
    }
    return address.substring(0, 5) + '...' + address.substring(address.length - 4, address.length);
};

function incomeLabel(w_type) {
    var labels = (PHP2JS.data.income_labels || {});
    if (labels[w_type] !== undefined) {
        return labels[w_type];
    }
    return parseInt(w_type, 10) === 10 ? 'Bonus Income' : 'Other Incomes';
}

function statusBadge(status) {
    // 0,1 = Pending | 2 = Approved | 3 = Rejected
    var code = parseInt(status, 10);
    if (code === 2) {
        return '<span class="badge bg-success">Approved</span>';
    }
    if (code === 3) {
        return '<span class="badge bg-danger">Rejected</span>';
    }
    return '<span class="badge bg-warning text-dark">Pending</span>';
}

function initiateDataTable() {
    oTable = $('#tableList').DataTable({
        "responsive": true,
        "processing": true,
        "serverSide": true,
        "searchHighlight": true,
        "search": {
            "caseInsensitive": true
        },
        "ajax": {
            "url": BASEPATH + "/get-withdrawal-request",
            "data": function(d) {
                d.status_filter = window.withdrawalStatusFilter || '';
            }
        },
        "order": [
            [1, "desc"]
        ],
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
                data: 'created_at',
                name: 'created_at',
                render: function(data, type, full, meta) {
                    return formatDate(data);
                },
                searchable: true
            },
            {
                data: 'mode',
                name: 'mode',
                render: function(data, type, full, meta) {
                    if (data == 0) {
                        var label = incomeLabel(full.w_type);
                        var fee = (parseFloat(full.charge_percent) <= 0)
                            ? '<br><small class="text-success">0% fee</small>'
                            : '<br><small>Charge: ' + full.charge_percent + '%</small>';
                        return '<b>' + label + '</b>' + fee;
                    }
                    if (data == 2) {
                        return '<b>Capital</b>';
                    }
                    return '<b>Instant</b>';
                },
                searchable: true
            },
            {
                data: 'amount',
                name: 'amount',
                render: function(data, type, full, meta) {
                    return '$' + data;
                },
                searchable: true
            },
            {
                data: 'admin',
                name: 'admin',
                render: function(data, type, full, meta) {
                    return '$' + data;
                },
                searchable: true
            },
            {
                data: 'net',
                name: 'net',
                render: function(data, type, full, meta) {
                    return '$' + data;
                },
                searchable: true
            },
            {
                data: 'address',
                name: 'address',
                render: function(data, type, full, meta) {
                    return withrawalAddress(data);
                },
                searchable: true
            },
            {
                data: 'hash',
                name: 'hash',
                render: function(data, type, full, meta) {
                    return data == null ? '' : '<a href="' + PHP2JS.data.txn_hash_url + '/' + data + '" target="_blank">View Hash</a>';
                },
                searchable: true
            },
            {
                data: 'status',
                name: 'status',
                render: function(data, type, full, meta) {
                    return statusBadge(data);
                },
                searchable: true
            },
        ]
    });
}
