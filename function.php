<?php

// Mailchimp opt-in checkbox for Elementor Form
add_filter('pre_http_request', function ($preempt, $parsed_args, $url) {
    // Only run this code when an Elementor Pro form has been submitted
    if (!isset($_POST['action']) || $_POST['action'] != 'elementor_pro_forms_send_form') {
        return $preempt;
    }

    // Only run this when trying to contact the Mailchimp API
    if (strpos($url, 'api.mailchimp.com') !== false) {
        $form_fields = $_POST['form_fields'] ?? [];

        if (!is_array($form_fields) || empty($form_fields)) {
            return $preempt;
        }

        // Check if there is an opt-in field defined (hidden field)
        if (array_key_exists('newsletter_optin_field', $form_fields)) {
            $optin_field = $form_fields['newsletter_optin_field'];

            // Check if the user hasn't opted in (hasn't ticked the box)
            if (!array_key_exists($optin_field, $form_fields)) {
                // Short-circuit Mailchimp API call
                return [
                    'headers' => '',
                    'body' => '{"simulation":true}',
                    'response' => [
                        'code' => '200',
                        'message' => 'OK',
                    ],
                    'cookies' => '',
                    'filename' => '',
                ];
            }
        }
    }

    return $preempt;
}, 10, 3);
