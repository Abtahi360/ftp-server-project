function validateEmail(email) {

    email = email.trim();

    var atPos  = email.indexOf('@');
    var dotPos = email.lastIndexOf('.');

    if (atPos <= 0) {
        return false;
    }

    if (dotPos <= atPos + 1) {
        return false;
    }


    if (dotPos >= email.length - 1) {
        return false;
    }
    if (email.indexOf(' ') !== -1) {
        return false;
    }

    return true;
}

function isEmpty(val) {
    return val.trim() === '';
}

function minLength(val, min) {
    return val.length >= min;
}

function allowedExtension(filename, allowed) {
    var ext = filename.split('.').pop().toLowerCase();
    return allowed.indexOf(ext) !== -1;
}

function allowedSize(bytes, maxMB) {
    return bytes <= maxMB * 1024 * 1024;
}

function showError(spanId, msg) {
    var span = document.getElementById(spanId);
    if (span) span.innerText = msg;
}

function clearErrors(spanIds) {
    spanIds.forEach(function(id) {
        var span = document.getElementById(id);
        if (span) span.innerText = '';
    });
}
