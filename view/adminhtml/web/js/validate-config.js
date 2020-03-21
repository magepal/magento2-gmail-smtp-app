/**
 * Copyright © MagePal LLC. All rights reserved.
 * See COPYING.txt for license details.
 * http://www.magepal.com | support@magepal.com
 */

define([
    'jquery',
    'Magento_Ui/js/modal/alert'
], function ($, alert) {

    var formSubmit = function (config) {
        var postData = {
            form_key: FORM_KEY
        };

        /** global var configForm **/
        configForm.find('[id^=system_gmailsmtpapp]').find(':input').serializeArray().map(function (field) {
            var name = field.name.match(/groups\[gmailsmtpapp\]?(\[groups\]\[debug\])?\[fields\]\[(.*)\]\[value]/);

            /**
             * groups[gmailsmtpapp][groups][debug][fields][email][value]
             * groups[gmailsmtpapp][fields][password][value]
             */

            if (name && name.length === 3) {
                postData[name[2]] = field.value;
            }
        });

        $.ajax({
            url: config.postUrl,
            type: 'post',
            dataType: 'html',
            data: postData,
            showLoader: true
        }).done(function (response) {
            if (typeof response === 'object') {
                if (response.error) {
                    alert({ title: 'Error', content: response.message });
                } else if (response.ajaxExpired) {
                    window.location.href = response.ajaxRedirect;
                }
            } else {
                alert({
                    title:'',
                    content:response,
                    buttons: []
                });
            }
        });
    };

    return function (config, element) {
        $(element).on('click', function () {
            formSubmit(config);
        });
    }
});
