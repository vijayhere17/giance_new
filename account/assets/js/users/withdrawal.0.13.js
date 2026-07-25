jQuery(document).ready(function() {
    $(".balance").text(PHP2JS.data.balance);

    if (PHP2JS.data.reactivation && PHP2JS.data.reactivation.required) {
        $(".btn-submit").prop('disabled', true).addClass('disabled');
        $("#amount, #income_type").prop('disabled', true);
    }

    document.getElementById('with_wallet').readOnly = true;
    document.getElementById('admin_charge').readOnly = true;
    document.getElementById('net_amount').readOnly = true;
    document.getElementById('coin_rate').readOnly = true;
    document.getElementById('usd_amount').readOnly = true;

    $("#with_wallet").val(PHP2JS.data.wallet_addr);
    $("#coin_rate").val(PHP2JS.data.coin_rate);

    $("#admin_charge").val('0.0000');
    $("#net_amount").val('0.0000');
    $("#usd_amount").val('0.0000');

    refreshIncomeHint();

    $("#income_type").on("change", function() {
        refreshIncomeHint();
        recalculate();
    });

    $("#amount").on("keyup change", function() {
        recalculate();
    });
});

function getSelectedIncome() {
    var $opt = $("#income_type option:selected");
    var id = $("#income_type").val();
    if (!id) {
        return null;
    }
    return {
        id: parseInt(id, 10),
        balance: parseFloat($opt.attr('data-balance') || 0),
        zero_fee: parseInt($opt.attr('data-zero-fee') || 0, 10) === 1
    };
}

function currentChargePercent() {
    var selected = getSelectedIncome();
    if (!selected) {
        return parseFloat(PHP2JS.data.charge_percent || 0);
    }
    if (selected.zero_fee) {
        return 0;
    }
    return parseFloat(PHP2JS.data.charge_percent || 0);
}

function refreshIncomeHint() {
    var selected = getSelectedIncome();
    if (!selected) {
        $(".income-balance-box").hide();
        return;
    }

    var pct = currentChargePercent();
    $(".type-balance").text(parseFloat(selected.balance).toFixed(4));
    if (selected.zero_fee || pct <= 0) {
        $(".charge-label").text('No Deduction (0%)');
    } else {
        $(".charge-label").text(pct + '% Admin Charge');
    }
    $(".income-balance-box").show();
}

function recalculate() {
    var rate = PHP2JS.data.coin_rate;
    var amount = parseFloat($("#amount").val());
    if (isNaN(amount) || amount < 0) {
        amount = 0;
    }

    var pct = currentChargePercent();
    var charge = (amount * pct) / 100;
    var net = amount - charge;
    var usd_amount = parseFloat(net / rate).toFixed(8);

    $("#admin_charge").val(parseFloat(charge).toFixed(4));
    $("#net_amount").val(parseFloat(net).toFixed(4));
    $("#usd_amount").val(usd_amount);
}

jQuery('.btn-otp-submit').bind('click', function(e) {
    e.preventDefault();
    if (validate(false)) {
        processWithdrawal(false);
    }
});

jQuery('.btn-submit').bind('click', function(e) {
    e.preventDefault();
    if (validate(true)) {
        processWithdrawal(true);
    }
});

function validate(otpstatus) {
    if (PHP2JS.data.reactivation && PHP2JS.data.reactivation.required) {
        erroralert(PHP2JS.data.reactivation.message || 'Reactivation required before withdrawal.');
        return false;
    }

    var selected = getSelectedIncome();
    var amount = $("#amount").val();
    var wallet = $("#with_wallet").val();

    if (!selected) {
        erroralert('Please select Bonus Income or Other Incomes.');
        return false;
    }

    if (parseFloat(selected.balance) <= 0) {
        erroralert('Selected income balance is $0');
        return false;
    }

    if (amount === '' || isNaN(parseFloat(amount))) {
        erroralert('Please enter a amount.');
        return false;
    }

    if (parseFloat(amount) <= 0) {
        erroralert('Please enter a valid amount.');
        return false;
    }

    var minAmount = parseFloat(PHP2JS.data.min_amount || 10);
    if (parseFloat(amount) < minAmount) {
        erroralert('Minimum withdrawal $' + minAmount);
        return false;
    }

    if (parseFloat(amount) > parseFloat(selected.balance)) {
        erroralert('Insufficient selected income balance.');
        return false;
    }

    if (parseFloat(amount) > parseFloat($(".balance").text())) {
        erroralert('Insufficient account balance.');
        return false;
    }

    if (wallet == '') {
        erroralert('Please update a withdrawal wallet address.');
        return false;
    }

    return true;
}

function processWithdrawal(status) {
    var amount = $("#amount").val();
    var wallet = $("#with_wallet").val();
    var otp = '346789';
    var income_type = $("#income_type").val();

    var reqObj = {
        _token: token,
        amount: amount,
        wallet: wallet,
        otp: otp,
        income_type: income_type,
        status: true
    };

    blockui();

    $.ajax({
        type: 'POST',
        url: BASEPATH + "/process-withdrawal-request",
        data: reqObj,
        dataType: 'json',
        success: function(result) {
            if (result.success) {
                if (status) {
                    successalert('Withdrawal request submited successfully!');
                    resetformdata(result.balance, result.income_options || []);
                } else {
                    successalert('OTP send your register email id!');
                    $(".btn-otp-submit").hide();
                    $(".btn-submit").show();
                }
            } else {
                erroralert(result.error);
            }
            unblockui();
        }
    });
}

function resetformdata(balance, income_options) {
    $("#amount").val('');
    $("#otp").val('');
    $("#admin_charge").val('0.0000');
    $("#net_amount").val('0.0000');
    $("#usd_amount").val('0.0000');
    $(".balance").text(balance);

    var $select = $("#income_type");
    $select.empty();
    $select.append('<option value="">-- Select --</option>');

    (income_options || []).forEach(function(opt) {
        var feeNote = opt.zero_fee ? ' — No Deduction' : ' — With Deduction';
        $select.append(
            $('<option></option>')
                .attr('value', opt.id)
                .attr('data-balance', opt.balance)
                .attr('data-zero-fee', opt.zero_fee ? 1 : 0)
                .text(opt.name + ' (Avail: $' + parseFloat(opt.balance).toFixed(4) + ')' + feeNote)
        );
    });

    refreshIncomeHint();
}
