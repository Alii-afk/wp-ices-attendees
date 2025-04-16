<?php
function export_attendees_csv() {
    // Check if the export button was clicked
    if (isset($_POST['export_attendees_csv'])) {
        global $wpdb;

        // Get search filters from POST data
        $search = isset($_POST['search_search']) ? esc_attr($_POST['search_search']) : '';
        $country = isset($_POST['search_country']) ? esc_attr($_POST['search_country']) : '';
        $organization = isset($_POST['search_organization']) ? esc_attr($_POST['search_organization']) : '';

        // Clear all output buffering
        if (ob_get_length()) {
            ob_end_clean();
        }

        // Specify the table and columns
        $table_name = $wpdb->prefix . 'ices_attendees';
        $columns = [
            'order_id',
            'product_id',
            'attendee_first_name',
            'attendee_last_name',
            'attendee_email',
            'attendee_organization',
            'attendee_dietary',
            'attendee_country'
        ];

        // Start the base query
        $query = sprintf(
            "SELECT %s FROM %s WHERE 1=1",
            implode(', ', $columns),
            $table_name
        );

        // Append conditions based on filters
        if ($search) {
            $query .= $wpdb->prepare(
                " AND (attendee_last_name LIKE %s OR attendee_email LIKE %s)",
                "%$search%",
                "%$search%"
            );
        }
        if ($country) {
            $query .= $wpdb->prepare(" AND attendee_country LIKE %s", "%$country%");
        }
        if ($organization) {
            $query .= $wpdb->prepare(" AND attendee_organization LIKE %s", "%$organization%");
        }
    		$query .= " ORDER BY id DESC"; // Order by descending ID

        // Fetch the results
        $results = $wpdb->get_results($query, ARRAY_A);

        if (!empty($results)) {
            // Set headers for CSV file download
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="attendees.csv"');
        
            // Open PHP output stream as a file
            $output = fopen('php://output', 'w');
        
            // Custom column headers
            $custom_headers = [
                'Order Id',
                'Product Id',
                'First Name',
                'Last Name',
                'Email',
                'Organization',
                'Dietary',
                'Country'
            ];
        
            // Write custom headers to CSV
            fputcsv($output, $custom_headers);
        
            // Write rows to CSV
            foreach ($results as $row) {
 					$order_id = $row['order_id'];
                    $order = wc_get_order($order_id);
					if (!$order || in_array($order->get_status(), ['trash', 'cancelled'])) {
							continue;
					}
                fputcsv($output, $row);
            }
        
            fclose($output);
        
            // Stop further script execution
            exit;
        } else {
            echo '<div class="notice notice-warning"><p>No records found to export.</p></div>';
        }
    }
}

add_action('init', function () {
    if (isset($_POST['export_attendees_csv'])) {
        remove_all_actions('shutdown');
    }
});


?>