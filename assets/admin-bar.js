(function () {
    'use strict';

    document.addEventListener('change', function (event) {
        if (!event.target.matches('.wpsts-admin-bar-select')) {
            return;
        }

        if (event.target.value) {
            window.location.assign(event.target.value);
        }
    });

    document.addEventListener('click', function (event) {
        if (event.target.matches('.wpsts-admin-bar-select')) {
            event.stopPropagation();
        }
    });
}());
