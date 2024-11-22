/*
 * Copyright CWSPS154. All rights reserved.
 * @auth CWSPS154
 * @link  https://github.com/CWSPS154
 */

require(
    [
        'Magento_Ui/js/lib/validation/validator',
        'jquery',
        'mage/url',
        'mage/translate'
    ], function (validator, $,urlBuilder) {
        validator.addRule(
            'unique-priority-schedule',
            function (value) {
                let entity_id = $('input[name="entity_id"]').val();
                let priority = $('input[name="priority"]').val();
                let start_date = $('input[name="start_date"]').val();
                let end_date = $('input[name="end_date"]').val();
                console.log(priority, start_date, end_date);
                let ajaxUrl = urlBuilder.build('/admin/promotional_banner/index/validate');
                $.ajax({
                    url: ajaxUrl,
                    type: 'GET',
                    data: {
                        entity_id: entity_id,
                        priority: priority,
                        start_date: start_date,
                        end_date: end_date,
                        form_key: FORM_KEY,
                    },
                    dataType: 'json',

                    success: function (response) {
                        // Code to handle a successful response
                        console.log(response);
                    },

                    error: function (xhr, status, error) {
                        // Code to handle an error
                        // console.error('Error:', error);
                    },
                });
            },
            $.mage.__('Store Url can contain dash (-) and alphanumric values only.')
        );
    });


