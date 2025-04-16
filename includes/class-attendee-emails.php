<?php

add_action('woocommerce_email_order_details', 'customize_order_email_with_attendees', 20, 4);
function customize_order_email_with_attendees($order, $sent_to_admin, $plain_text, $email)
{
    // Ensure this only runs for the customer order confirmation email
    if (!$sent_to_admin && $email->id == 'customer_processing_order') {

        echo '<h2>' . __('Attendee Details', 'textdomain') . '</h2>';
        
        // Loop through each item in the order
        foreach ($order->get_items() as $item_id => $item) {
            // Check if the product has attendee management enabled
            if ($item->get_product()->get_meta('manage_attendees')) {
                // Loop through each attendee (based on quantity)
                for ($i = 1; $i <= $item->get_quantity(); $i++) {
                    echo '<p><strong>Attendee ' . $i . ':</strong></p>';
                    echo '<ul style="margin: 0; padding: 0 0 0 15px;">';
                    echo '<li><strong>Name:</strong> ' . esc_html($item->get_meta('Attendee ' . $i . ' First Name')) . ' ' . esc_html($item->get_meta('Attendee ' . $i . ' Last Name')) . '</li>';
                    echo '<li><strong>Email:</strong> ' . esc_html($item->get_meta('Attendee ' . $i . ' Email')) . '</li>';
                    echo '<li><strong>Agency:</strong> ' . esc_html($item->get_meta('Attendee ' . $i . ' Organization')) . '</li>';
                    echo '<li><strong>Dietary Restrictions:</strong> ' . esc_html($item->get_meta('Attendee ' . $i . ' Dietary')) . '</li>';
                    echo '<li><strong>Country:</strong> ' . esc_html($item->get_meta('Attendee ' . $i . ' Country')) . '</li>';
                    echo '</ul>';
                }
            }
        }
    }
}


add_action('woocommerce_thankyou', 'send_attendee_emails', 10, 1);
function send_attendee_emails($order_id)
{
    $order = wc_get_order($order_id);
        error_log('Attendee details customization triggered for Order #' . $order->get_id());

    foreach ($order->get_items() as $item_id => $item) {
        if ($item->get_product()->get_meta('manage_attendees')) {
            for ($i = 1; $i <= $item->get_quantity(); $i++) {
                // Fetch attendee details
                $first_name = $item->get_meta('Attendee ' . $i . ' First Name');
                $last_name = $item->get_meta('Attendee ' . $i . ' Last Name');
                $email = $item->get_meta('Attendee ' . $i . ' Email');
                $organization = $item->get_meta('Attendee ' . $i . ' Organization');
                $dietary = $item->get_meta('Attendee ' . $i . ' Dietary');
                $country = $item->get_meta('Attendee ' . $i . ' Country');
                $product_name = $item->get_name();

                // Construct email subject and body
                $subject = 'Your Event Registration Details';
                $body = '<p>Hello, ' . $first_name . '. You\'ve been registered for the ' . $product_name . '.</p>';
                $body .= '<p><strong>Product:</strong> ' . $product_name . '</p>';
                $body .= '<p><strong>Attendee Information:</strong></p>';
                $body .= '<ul>';
                $body .= '<li><strong>Name:</strong> ' . $first_name . ' ' . $last_name . '</li>';
                $body .= '<li><strong>Email:</strong> ' . $email . '</li>';
                $body .= '<li><strong>Agency:</strong> ' . $organization . '</li>';
                $body .= '<li><strong>Dietary Restrictions:</strong> ' . $dietary . '</li>';
                $body .= '<li><strong>Country:</strong> ' . $country . '</li>';
                $body .= '</ul>';
                $body .= '<p>Thank you for registering!</p>';

                // Ensure email is sent as HTML
                add_filter('wp_mail_content_type', function () {
                    return 'text/html';
                });

                // Send the email
                $email_sent = wp_mail($email, $subject, $body);

                // Log email status
                if (!$email_sent) {
                    error_log('Failed to send email to: ' . $email);
                } else {
                    error_log('Email sent successfully to: ' . $email);
                }

                // Reset the content type to avoid conflicts with other emails
                remove_filter('wp_mail_content_type', function () {
                    return 'text/html';
                });
            }
        }
    }
}

function send_bulk_emails($subject, $body)
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'ices_attendees';
    $search = isset($_POST['search_search']) ? esc_attr($_POST['search_search']) : '';
    $country = isset($_POST['search_country']) ? esc_attr($_POST['search_country']) : '';
    $Organization = isset($_POST['search_organization']) ? esc_attr($_POST['search_organization']) : '';
    // Prepare query with the same filters
    $query = "SELECT * FROM $table_name WHERE 1=1";
    if ($search) {
        $query .= $wpdb->prepare(" AND (attendee_last_name LIKE %s OR attendee_email LIKE %s)", "%$search%", "%$search%");
    }
    if ($country) {
        $query .= $wpdb->prepare(" AND attendee_country LIKE %s", "%$country%");
    }
    if ($Organization) {
        $query .= $wpdb->prepare(" AND attendee_organization LIKE %s", "%$Organization%");
    }
     $query .= " ORDER BY id DESC"; // Order by descending ID

    $attendees = $wpdb->get_results($query);
    // Send emails
    foreach ($attendees as $attendee) {

        $order_id = $attendee->order_id;
                 $order = wc_get_order($order_id);
		if (!$order || in_array($order->get_status(), ['trash', 'cancelled'])) {
				continue;
			}
        $email_sent =  wp_mail($attendee->attendee_email, $subject, $body);
                if (!$email_sent) {
                    error_log('Failed to send email to: ' . $attendee->attendee_email);
                } else {
                    error_log('Email sent successfully to: ' . $attendee->attendee_email);
                }
    }
}

?>