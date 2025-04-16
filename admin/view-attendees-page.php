<?php 

function display_ices_attendees_page()
{
    if (!empty($_POST)) {
        // Handle form submissions
        if (isset($_POST['bulk_email_subject']) && isset($_POST['bulk_email_body'])) {
            send_bulk_emails($_POST['bulk_email_subject'], $_POST['bulk_email_body']);
        }
        if (isset($_POST['export_attendees_csv'])) {
            export_attendees_csv();
        }
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'ices_attendees';

    $search = isset($_POST['search']) ? esc_attr($_POST['search']) : '';
    $country = isset($_POST['filter_country']) ? esc_attr($_POST['filter_country']) : '';
    $organization = isset($_POST['filter_organization']) ? esc_attr($_POST['filter_country']) : '';

    $query = "SELECT * FROM $table_name WHERE 1=1";
    if ($search) {
        $query .= $wpdb->prepare(" AND (attendee_first_name LIKE %s OR attendee_last_name LIKE %s OR attendee_email LIKE %s)", "%$search%", "%$search%", "%$search%");
    }
    if ($country) {
        $query .= $wpdb->prepare(" AND attendee_country LIKE %s", "%$country%");
    }
    if ($organization) {
        $query .= $wpdb->prepare(" AND attendee_organization LIKE %s", "%$organization%");
    }

    $query .= " ORDER BY id DESC"; // Order by descending ID
    $attendees = $wpdb->get_results($query);

    ?>
    <div class="wrap">
        <form method="post" action="">
            <h2><?php _e('Bulk Email', 'textdomain'); ?></h2>
<button id="flush-attendees" class="button" style=" color: red; border-color: red; float: right; width: 180px; height: 50px; font-size: medium;">Flush Attendees Data</button>
            <input type="text" name="bulk_email_subject" placeholder="Email Subject" style="width: 50%; height: 60px; font-size: medium;" required /><br><br>
            <textarea name="bulk_email_body" rows="5" placeholder="Enter email body here" style="width: 50%; height: 160px; font-size: medium;" required></textarea><br><br>
            <input type="hidden" name="search_search" value="<?php echo $search; ?>">
            <input type="hidden" name="search_country" value="<?php echo $country; ?>">
            <input type="hidden" name="search_organization" value="<?php echo $organization; ?>">
            <input type="submit" value="Send Bulk Emails" class="button" style="width: 150px; height: 50px; font-size: medium;">

			<script>
				jQuery(document).ready(function ($) {
					$('#flush-attendees').on('click', function () {
						if (!confirm('Are you sure you want to flush the attendees table this will remove all the record except orders data?')) {
							return;
						}

						var button = $(this);
						button.prop('disabled', true).text('Flushing...');

						$.post(ajaxurl, { action: 'flush_attendees' }, function (response) {
							if (response.success) {
								$('#flush-response').html('<p style="color: green;">' + response.data.message + '</p>');
								// Refresh the page after a short delay
								setTimeout(function () {
									location.reload();
								}, 1500); // 1.5 seconds delay for better user experience
							} else {
								$('#flush-response').html('<p style="color: red;">' + response.data.message + '</p>');
								button.prop('disabled', false).text('Flush Table');
							}
						});
					});
				});
			</script>
        </form>

        <form method="post" action="">
            <input type="hidden" name="export_attendees_csv" value="1" />
            <input type="hidden" name="search_search" value="<?php echo $search; ?>">
            <input type="hidden" name="search_country" value="<?php echo $country; ?>">
            <input type="hidden" name="search_organization" value="<?php echo $organization; ?>">
            <input type="submit" value="Export CSV" class="button" style="color: green; border-color: green; float: right; width: 150px; height: 50px; font-size: medium;">
			
        </form>
        <h2><?php _e('Attendees', 'textdomain'); ?></h2>
        <form method="post" action="" style="margin-bottom: 10px;">
            <input type="text" name="search" placeholder="Search attendees By last Name/Email" value="<?php echo esc_attr($search); ?>" style="height: 50px; font-size: medium; width:280px;" />
            <select name="filter_country" placeholder="Country" style="height: 50px; font-size: medium; margin-top: -9px;">
                <option value=""><?php _e('Select Country', 'textdomain'); ?></option>
                <option value="Afghanistan" <?php echo ($country === 'Afghanistan') ? 'selected' : ''; ?>><?php _e('Afghanistan', 'textdomain'); ?></option>
                <option value="Åland Islands" <?php echo ($country === 'Åland Islands') ? 'selected' : ''; ?>><?php _e('Åland Islands', 'textdomain'); ?></option>
                <option value="Albania" <?php echo ($country === 'Albania') ? 'selected' : ''; ?>><?php _e('Albania', 'textdomain'); ?></option>
                <option value="Algeria" <?php echo ($country === 'Algeria') ? 'selected' : ''; ?>><?php _e('Algeria', 'textdomain'); ?></option>
                <option value="American Samoa" <?php echo ($country === 'American Samoa') ? 'selected' : ''; ?>><?php _e('American Samoa', 'textdomain'); ?></option>
                <option value="Andorra" <?php echo ($country === 'Andorra') ? 'selected' : ''; ?>><?php _e('Andorra', 'textdomain'); ?></option>
                <option value="Angola" <?php echo ($country === 'Angola') ? 'selected' : ''; ?>><?php _e('Angola', 'textdomain'); ?></option>
                <option value="Anguilla" <?php echo ($country === 'Anguilla') ? 'selected' : ''; ?>><?php _e('Anguilla', 'textdomain'); ?></option>
                <option value="Antarctica" <?php echo ($country === 'Antarctica') ? 'selected' : ''; ?>><?php _e('Antarctica', 'textdomain'); ?></option>
                <option value="Antigua and Barbuda" <?php echo ($country === 'Antigua and Barbuda') ? 'selected' : ''; ?>><?php _e('Antigua and Barbuda', 'textdomain'); ?></option>
                <option value="Argentina" <?php echo ($country === 'Argentina') ? 'selected' : ''; ?>><?php _e('Argentina', 'textdomain'); ?></option>
                <option value="Armenia" <?php echo ($country === 'Armenia') ? 'selected' : ''; ?>><?php _e('Armenia', 'textdomain'); ?></option>
                <option value="Aruba" <?php echo ($country === 'Aruba') ? 'selected' : ''; ?>><?php _e('Aruba', 'textdomain'); ?></option>
                <option value="Australia" <?php echo ($country === 'Australia') ? 'selected' : ''; ?>><?php _e('Australia', 'textdomain'); ?></option>
                <option value="Austria" <?php echo ($country === 'Austria') ? 'selected' : ''; ?>><?php _e('Austria', 'textdomain'); ?></option>
                <option value="Azerbaijan" <?php echo ($country === 'Azerbaijan') ? 'selected' : ''; ?>><?php _e('Azerbaijan', 'textdomain'); ?></option>
                <option value="Bahamas" <?php echo ($country === 'Bahamas') ? 'selected' : ''; ?>><?php _e('Bahamas', 'textdomain'); ?></option>
                <option value="Bahrain" <?php echo ($country === 'Bahrain') ? 'selected' : ''; ?>><?php _e('Bahrain', 'textdomain'); ?></option>
                <option value="Bangladesh" <?php echo ($country === 'Bangladesh') ? 'selected' : ''; ?>><?php _e('Bangladesh', 'textdomain'); ?></option>
                <option value="Barbados" <?php echo ($country === 'Barbados') ? 'selected' : ''; ?>><?php _e('Barbados', 'textdomain'); ?></option>
                <option value="Belarus" <?php echo ($country === 'Belarus') ? 'selected' : ''; ?>><?php _e('Belarus', 'textdomain'); ?></option>
                <option value="Belgium" <?php echo ($country === 'Belgium') ? 'selected' : ''; ?>><?php _e('Belgium', 'textdomain'); ?></option>
                <option value="Belize" <?php echo ($country === 'Belize') ? 'selected' : ''; ?>><?php _e('Belize', 'textdomain'); ?></option>
                <option value="Benin" <?php echo ($country === 'Benin') ? 'selected' : ''; ?>><?php _e('Benin', 'textdomain'); ?></option>
                <option value="Bermuda" <?php echo ($country === 'Bermuda') ? 'selected' : ''; ?>><?php _e('Bermuda', 'textdomain'); ?></option>
                <option value="Bhutan" <?php echo ($country === 'Bhutan') ? 'selected' : ''; ?>><?php _e('Bhutan', 'textdomain'); ?></option>
                <option value="Bolivia" <?php echo ($country === 'Bolivia') ? 'selected' : ''; ?>><?php _e('Bolivia', 'textdomain'); ?></option>
                <option value="Bosnia and Herzegovina" <?php echo ($country === 'Bosnia and Herzegovina') ? 'selected' : ''; ?>><?php _e('Bosnia and Herzegovina', 'textdomain'); ?></option>
                <option value="Botswana" <?php echo ($country === 'Botswana') ? 'selected' : ''; ?>><?php _e('Botswana', 'textdomain'); ?></option>
                <option value="Bouvet Island" <?php echo ($country === 'Bouvet Island') ? 'selected' : ''; ?>><?php _e('Bouvet Island', 'textdomain'); ?></option>
                <option value="Brazil" <?php echo ($country === 'Brazil') ? 'selected' : ''; ?>><?php _e('Brazil', 'textdomain'); ?></option>
                <option value="British Indian Ocean Territory" <?php echo ($country === 'British Indian Ocean Territory') ? 'selected' : ''; ?>><?php _e('British Indian Ocean Territory', 'textdomain'); ?></option>
                <option value="Brunei Darussalam" <?php echo ($country === 'Brunei Darussalam') ? 'selected' : ''; ?>><?php _e('Brunei Darussalam', 'textdomain'); ?></option>
                <option value="Bulgaria" <?php echo ($country === 'Bulgaria') ? 'selected' : ''; ?>><?php _e('Bulgaria', 'textdomain'); ?></option>
                <option value="Burkina Faso" <?php echo ($country === 'Burkina Faso') ? 'selected' : ''; ?>><?php _e('Burkina Faso', 'textdomain'); ?></option>
                <option value="Burundi" <?php echo ($country === 'Burundi') ? 'selected' : ''; ?>><?php _e('Burundi', 'textdomain'); ?></option>
                <option value="Cambodia" <?php echo ($country === 'Cambodia') ? 'selected' : ''; ?>><?php _e('Cambodia', 'textdomain'); ?></option>
                <option value="Cameroon" <?php echo ($country === 'Cameroon') ? 'selected' : ''; ?>><?php _e('Cameroon', 'textdomain'); ?></option>
                <option value="Canada" <?php echo ($country === 'Canada') ? 'selected' : ''; ?>><?php _e('Canada', 'textdomain'); ?></option>
                <option value="Cape Verde" <?php echo ($country === 'Cape Verde') ? 'selected' : ''; ?>><?php _e('Cape Verde', 'textdomain'); ?></option>
                <option value="Cayman Islands" <?php echo ($country === 'Cayman Islands') ? 'selected' : ''; ?>><?php _e('Cayman Islands', 'textdomain'); ?></option>
                <option value="Central African Republic" <?php echo ($country === 'Central African Republic') ? 'selected' : ''; ?>><?php _e('Central African Republic', 'textdomain'); ?></option>
                <option value="Chad" <?php echo ($country === 'Chad') ? 'selected' : ''; ?>><?php _e('Chad', 'textdomain'); ?></option>
                <option value="Chile" <?php echo ($country === 'Chile') ? 'selected' : ''; ?>><?php _e('Chile', 'textdomain'); ?></option>
                <option value="China" <?php echo ($country === 'China') ? 'selected' : ''; ?>><?php _e('China', 'textdomain'); ?></option>
                <option value="Christmas Island" <?php echo ($country === 'Christmas Island') ? 'selected' : ''; ?>><?php _e('Christmas Island', 'textdomain'); ?></option>
                <option value="Cocos (Keeling) Islands" <?php echo ($country === 'Cocos (Keeling) Islands') ? 'selected' : ''; ?>><?php _e('Cocos (Keeling) Islands', 'textdomain'); ?></option>
                <option value="Colombia" <?php echo ($country === 'Colombia') ? 'selected' : ''; ?>><?php _e('Colombia', 'textdomain'); ?></option>
                <option value="Comoros" <?php echo ($country === 'Comoros') ? 'selected' : ''; ?>><?php _e('Comoros', 'textdomain'); ?></option>
                <option value="Congo" <?php echo ($country === 'Congo') ? 'selected' : ''; ?>><?php _e('Congo', 'textdomain'); ?></option>
                <option value="Congo, The Democratic Republic of The" <?php echo ($country === 'Congo, The Democratic Republic of The') ? 'selected' : ''; ?>><?php _e('Congo, The Democratic Republic of The', 'textdomain'); ?></option>
                <option value="Cook Islands" <?php echo ($country === 'Cook Islands') ? 'selected' : ''; ?>><?php _e('Cook Islands', 'textdomain'); ?></option>
                <option value="Costa Rica" <?php echo ($country === 'Costa Rica') ? 'selected' : ''; ?>><?php _e('Costa Rica', 'textdomain'); ?></option>
                <option value="Cote D'ivoire" <?php echo ($country === "Cote D'ivoire") ? 'selected' : ''; ?>><?php _e("Cote D'ivoire", 'textdomain'); ?></option>
                <option value="Croatia" <?php echo ($country === 'Croatia') ? 'selected' : ''; ?>><?php _e('Croatia', 'textdomain'); ?></option>
                <option value="Cuba" <?php echo ($country === 'Cuba') ? 'selected' : ''; ?>><?php _e('Cuba', 'textdomain'); ?></option>
                <option value="Cyprus" <?php echo ($country === 'Cyprus') ? 'selected' : ''; ?>><?php _e('Cyprus', 'textdomain'); ?></option>
                <option value="Czech Republic" <?php echo ($country === 'Czech Republic') ? 'selected' : ''; ?>><?php _e('Czech Republic', 'textdomain'); ?></option>
                <option value="Denmark" <?php echo ($country === 'Denmark') ? 'selected' : ''; ?>><?php _e('Denmark', 'textdomain'); ?></option>
                <option value="Djibouti" <?php echo ($country === 'Djibouti') ? 'selected' : ''; ?>><?php _e('Djibouti', 'textdomain'); ?></option>
                <option value="Dominica" <?php echo ($country === 'Dominica') ? 'selected' : ''; ?>><?php _e('Dominica', 'textdomain'); ?></option>
                <option value="Dominican Republic" <?php echo ($country === 'Dominican Republic') ? 'selected' : ''; ?>><?php _e('Dominican Republic', 'textdomain'); ?></option>
                <option value="Ecuador" <?php echo ($country === 'Ecuador') ? 'selected' : ''; ?>><?php _e('Ecuador', 'textdomain'); ?></option>
                <option value="Egypt" <?php echo ($country === 'Egypt') ? 'selected' : ''; ?>><?php _e('Egypt', 'textdomain'); ?></option>
                <option value="El Salvador" <?php echo ($country === 'El Salvador') ? 'selected' : ''; ?>><?php _e('El Salvador', 'textdomain'); ?></option>
                <option value="Equatorial Guinea" <?php echo ($country === 'Equatorial Guinea') ? 'selected' : ''; ?>><?php _e('Equatorial Guinea', 'textdomain'); ?></option>
                <option value="Eritrea" <?php echo ($country === 'Eritrea') ? 'selected' : ''; ?>><?php _e('Eritrea', 'textdomain'); ?></option>
                <option value="Estonia" <?php echo ($country === 'Estonia') ? 'selected' : ''; ?>><?php _e('Estonia', 'textdomain'); ?></option>
                <option value="Ethiopia" <?php echo ($country === 'Ethiopia') ? 'selected' : ''; ?>><?php _e('Ethiopia', 'textdomain'); ?></option>
                <option value="Falkland Islands (Malvinas)" <?php echo ($country === 'Falkland Islands (Malvinas)') ? 'selected' : ''; ?>><?php _e('Falkland Islands (Malvinas)', 'textdomain'); ?></option>
                <option value="Faroe Islands" <?php echo ($country === 'Faroe Islands') ? 'selected' : ''; ?>><?php _e('Faroe Islands', 'textdomain'); ?></option>
                <option value="Fiji" <?php echo ($country === 'Fiji') ? 'selected' : ''; ?>><?php _e('Fiji', 'textdomain'); ?></option>
                <option value="Finland" <?php echo ($country === 'Finland') ? 'selected' : ''; ?>><?php _e('Finland', 'textdomain'); ?></option>
                <option value="France" <?php echo ($country === 'France') ? 'selected' : ''; ?>><?php _e('France', 'textdomain'); ?></option>
                <option value="French Guiana" <?php echo ($country === 'French Guiana') ? 'selected' : ''; ?>><?php _e('French Guiana', 'textdomain'); ?></option>
                <option value="French Polynesia" <?php echo ($country === 'French Polynesia') ? 'selected' : ''; ?>><?php _e('French Polynesia', 'textdomain'); ?></option>
                <option value="French Southern Territories" <?php echo ($country === 'French Southern Territories') ? 'selected' : ''; ?>><?php _e('French Southern Territories', 'textdomain'); ?></option>
                <option value="Gabon" <?php echo ($country === 'Gabon') ? 'selected' : ''; ?>><?php _e('Gabon', 'textdomain'); ?></option>
                <option value="Gambia" <?php echo ($country === 'Gambia') ? 'selected' : ''; ?>><?php _e('Gambia', 'textdomain'); ?></option>
                <option value="Georgia" <?php echo ($country === 'Georgia') ? 'selected' : ''; ?>><?php _e('Georgia', 'textdomain'); ?></option>
                <option value="Germany" <?php echo ($country === 'Germany') ? 'selected' : ''; ?>><?php _e('Germany', 'textdomain'); ?></option>
                <option value="Ghana" <?php echo ($country === 'Ghana') ? 'selected' : ''; ?>><?php _e('Ghana', 'textdomain'); ?></option>
                <option value="Gibraltar" <?php echo ($country === 'Gibraltar') ? 'selected' : ''; ?>><?php _e('Gibraltar', 'textdomain'); ?></option>
                <option value="Greece" <?php echo ($country === 'Greece') ? 'selected' : ''; ?>><?php _e('Greece', 'textdomain'); ?></option>
                <option value="Greenland" <?php echo ($country === 'Greenland') ? 'selected' : ''; ?>><?php _e('Greenland', 'textdomain'); ?></option>
                <option value="Grenada" <?php echo ($country === 'Grenada') ? 'selected' : ''; ?>><?php _e('Grenada', 'textdomain'); ?></option>
                <option value="Guadeloupe" <?php echo ($country === 'Guadeloupe') ? 'selected' : ''; ?>><?php _e('Guadeloupe', 'textdomain'); ?></option>
                <option value="Guam" <?php echo ($country === 'Guam') ? 'selected' : ''; ?>><?php _e('Guam', 'textdomain'); ?></option>
                <option value="Guatemala" <?php echo ($country === 'Guatemala') ? 'selected' : ''; ?>><?php _e('Guatemala', 'textdomain'); ?></option>
                <option value="Guernsey" <?php echo ($country === 'Guernsey') ? 'selected' : ''; ?>><?php _e('Guernsey', 'textdomain'); ?></option>
                <option value="Guinea" <?php echo ($country === 'Guinea') ? 'selected' : ''; ?>><?php _e('Guinea', 'textdomain'); ?></option>
                <option value="Guinea-bissau" <?php echo ($country === 'Guinea-bissau') ? 'selected' : ''; ?>><?php _e('Guinea-bissau', 'textdomain'); ?></option>
                <option value="Guyana" <?php echo ($country === 'Guyana') ? 'selected' : ''; ?>><?php _e('Guyana', 'textdomain'); ?></option>
                <option value="Haiti" <?php echo ($country === 'Haiti') ? 'selected' : ''; ?>><?php _e('Haiti', 'textdomain'); ?></option>
                <option value="Heard Island and Mcdonald Islands" <?php echo ($country === 'Heard Island and Mcdonald Islands') ? 'selected' : ''; ?>><?php _e('Heard Island and Mcdonald Islands', 'textdomain'); ?></option>
                <option value="Holy See (Vatican City State)" <?php echo ($country === 'Holy See (Vatican City State)') ? 'selected' : ''; ?>><?php _e('Holy See (Vatican City State)', 'textdomain'); ?></option>
                <option value="Honduras" <?php echo ($country === 'Honduras') ? 'selected' : ''; ?>><?php _e('Honduras', 'textdomain'); ?></option>
                <option value="Hong Kong" <?php echo ($country === 'Hong Kong') ? 'selected' : ''; ?>><?php _e('Hong Kong', 'textdomain'); ?></option>
                <option value="Hungary" <?php echo ($country === 'Hungary') ? 'selected' : ''; ?>><?php _e('Hungary', 'textdomain'); ?></option>
                <option value="Iceland" <?php echo ($country === 'Iceland') ? 'selected' : ''; ?>><?php _e('Iceland', 'textdomain'); ?></option>
                <option value="India" <?php echo ($country === 'India') ? 'selected' : ''; ?>><?php _e('India', 'textdomain'); ?></option>
                <option value="Indonesia" <?php echo ($country === 'Indonesia') ? 'selected' : ''; ?>><?php _e('Indonesia', 'textdomain'); ?></option>
                <option value="Iran, Islamic Republic of" <?php echo ($country === 'Iran, Islamic Republic of') ? 'selected' : ''; ?>><?php _e('Iran, Islamic Republic of', 'textdomain'); ?></option>
                <option value="Iraq" <?php echo ($country === 'Iraq') ? 'selected' : ''; ?>><?php _e('Iraq', 'textdomain'); ?></option>
                <option value="Ireland" <?php echo ($country === 'Ireland') ? 'selected' : ''; ?>><?php _e('Ireland', 'textdomain'); ?></option>
                <option value="Isle of Man" <?php echo ($country === 'Isle of Man') ? 'selected' : ''; ?>><?php _e('Isle of Man', 'textdomain'); ?></option>
                <option value="Israel" <?php echo ($country === 'Israel') ? 'selected' : ''; ?>><?php _e('Israel', 'textdomain'); ?></option>
                <option value="Italy" <?php echo ($country === 'Italy') ? 'selected' : ''; ?>><?php _e('Italy', 'textdomain'); ?></option>
                <option value="Jamaica" <?php echo ($country === 'Jamaica') ? 'selected' : ''; ?>><?php _e('Jamaica', 'textdomain'); ?></option>
                <option value="Japan" <?php echo ($country === 'Japan') ? 'selected' : ''; ?>><?php _e('Japan', 'textdomain'); ?></option>
                <option value="Jersey" <?php echo ($country === 'Jersey') ? 'selected' : ''; ?>><?php _e('Jersey', 'textdomain'); ?></option>
                <option value="Jordan" <?php echo ($country === 'Jordan') ? 'selected' : ''; ?>><?php _e('Jordan', 'textdomain'); ?></option>
                <option value="Kazakhstan" <?php echo ($country === 'Kazakhstan') ? 'selected' : ''; ?>><?php _e('Kazakhstan', 'textdomain'); ?></option>
                <option value="Kenya" <?php echo ($country === 'Kenya') ? 'selected' : ''; ?>><?php _e('Kenya', 'textdomain'); ?></option>
                <option value="Kiribati" <?php echo ($country === 'Kiribati') ? 'selected' : ''; ?>><?php _e('Kiribati', 'textdomain'); ?></option>
                <option value="Korea, Democratic People's Republic of" <?php echo ($country === "Korea, Democratic People's Republic of") ? 'selected' : ''; ?>><?php _e("Korea, Democratic People's Republic of", 'textdomain'); ?></option>
                <option value="Korea, Republic of" <?php echo ($country === 'Korea, Republic of') ? 'selected' : ''; ?>><?php _e('Korea, Republic of', 'textdomain'); ?></option>
                <option value="Kuwait" <?php echo ($country === 'Kuwait') ? 'selected' : ''; ?>><?php _e('Kuwait', 'textdomain'); ?></option>
                <option value="Kyrgyzstan" <?php echo ($country === 'Kyrgyzstan') ? 'selected' : ''; ?>><?php _e('Kyrgyzstan', 'textdomain'); ?></option>
                <option value="Lao People's Democratic Republic" <?php echo ($country === "Lao People's Democratic Republic") ? 'selected' : ''; ?>><?php _e("Lao People's Democratic Republic", 'textdomain'); ?></option>
                <option value="Latvia" <?php echo ($country === 'Latvia') ? 'selected' : ''; ?>><?php _e('Latvia', 'textdomain'); ?></option>
                <option value="Lebanon" <?php echo ($country === 'Lebanon') ? 'selected' : ''; ?>><?php _e('Lebanon', 'textdomain'); ?></option>
                <option value="Lesotho" <?php echo ($country === 'Lesotho') ? 'selected' : ''; ?>><?php _e('Lesotho', 'textdomain'); ?></option>
                <option value="Liberia" <?php echo ($country === 'Liberia') ? 'selected' : ''; ?>><?php _e('Liberia', 'textdomain'); ?></option>
                <option value="Libyan Arab Jamahiriya" <?php echo ($country === 'Libyan Arab Jamahiriya') ? 'selected' : ''; ?>><?php _e('Libyan Arab Jamahiriya', 'textdomain'); ?></option>
                <option value="Liechtenstein" <?php echo ($country === 'Liechtenstein') ? 'selected' : ''; ?>><?php _e('Liechtenstein', 'textdomain'); ?></option>
                <option value="Lithuania" <?php echo ($country === 'Lithuania') ? 'selected' : ''; ?>><?php _e('Lithuania', 'textdomain'); ?></option>
                <option value="Luxembourg" <?php echo ($country === 'Luxembourg') ? 'selected' : ''; ?>><?php _e('Luxembourg', 'textdomain'); ?></option>
                <option value="Macao" <?php echo ($country === 'Macao') ? 'selected' : ''; ?>><?php _e('Macao', 'textdomain'); ?></option>
                <option value="Macedonia, The Former Yugoslav Republic of" <?php echo ($country === 'Macedonia, The Former Yugoslav Republic of') ? 'selected' : ''; ?>><?php _e('Macedonia, The Former Yugoslav Republic of', 'textdomain'); ?></option>
                <option value="Madagascar" <?php echo ($country === 'Madagascar') ? 'selected' : ''; ?>><?php _e('Madagascar', 'textdomain'); ?></option>
                <option value="Malawi" <?php echo ($country === 'Malawi') ? 'selected' : ''; ?>><?php _e('Malawi', 'textdomain'); ?></option>
                <option value="Malaysia" <?php echo ($country === 'Malaysia') ? 'selected' : ''; ?>><?php _e('Malaysia', 'textdomain'); ?></option>
                <option value="Maldives" <?php echo ($country === 'Maldives') ? 'selected' : ''; ?>><?php _e('Maldives', 'textdomain'); ?></option>
                <option value="Mali" <?php echo ($country === 'Mali') ? 'selected' : ''; ?>><?php _e('Mali', 'textdomain'); ?></option>
                <option value="Malta" <?php echo ($country === 'Malta') ? 'selected' : ''; ?>><?php _e('Malta', 'textdomain'); ?></option>
                <option value="Marshall Islands" <?php echo ($country === 'Marshall Islands') ? 'selected' : ''; ?>><?php _e('Marshall Islands', 'textdomain'); ?></option>
                <option value="Martinique" <?php echo ($country === 'Martinique') ? 'selected' : ''; ?>><?php _e('Martinique', 'textdomain'); ?></option>
                <option value="Mauritania" <?php echo ($country === 'Mauritania') ? 'selected' : ''; ?>><?php _e('Mauritania', 'textdomain'); ?></option>
                <option value="Mauritius" <?php echo ($country === 'Mauritius') ? 'selected' : ''; ?>><?php _e('Mauritius', 'textdomain'); ?></option>
                <option value="Mayotte" <?php echo ($country === 'Mayotte') ? 'selected' : ''; ?>><?php _e('Mayotte', 'textdomain'); ?></option>
                <option value="Mexico" <?php echo ($country === 'Mexico') ? 'selected' : ''; ?>><?php _e('Mexico', 'textdomain'); ?></option>
                <option value="Micronesia, Federated States of" <?php echo ($country === 'Micronesia, Federated States of') ? 'selected' : ''; ?>><?php _e('Micronesia, Federated States of', 'textdomain'); ?></option>
                <option value="Moldova, Republic of" <?php echo ($country === 'Moldova, Republic of') ? 'selected' : ''; ?>><?php _e('Moldova, Republic of', 'textdomain'); ?></option>
                <option value="Monaco" <?php echo ($country === 'Monaco') ? 'selected' : ''; ?>><?php _e('Monaco', 'textdomain'); ?></option>
                <option value="Mongolia" <?php echo ($country === 'Mongolia') ? 'selected' : ''; ?>><?php _e('Mongolia', 'textdomain'); ?></option>
                <option value="Montenegro" <?php echo ($country === 'Montenegro') ? 'selected' : ''; ?>><?php _e('Montenegro', 'textdomain'); ?></option>
                <option value="Montserrat" <?php echo ($country === 'Montserrat') ? 'selected' : ''; ?>><?php _e('Montserrat', 'textdomain'); ?></option>
                <option value="Morocco" <?php echo ($country === 'Morocco') ? 'selected' : ''; ?>><?php _e('Morocco', 'textdomain'); ?></option>
                <option value="Mozambique" <?php echo ($country === 'Mozambique') ? 'selected' : ''; ?>><?php _e('Mozambique', 'textdomain'); ?></option>
                <option value="Myanmar" <?php echo ($country === 'Myanmar') ? 'selected' : ''; ?>><?php _e('Myanmar', 'textdomain'); ?></option>
                <option value="Namibia" <?php echo ($country === 'Namibia') ? 'selected' : ''; ?>><?php _e('Namibia', 'textdomain'); ?></option>
                <option value="Nauru" <?php echo ($country === 'Nauru') ? 'selected' : ''; ?>><?php _e('Nauru', 'textdomain'); ?></option>
                <option value="Nepal" <?php echo ($country === 'Nepal') ? 'selected' : ''; ?>><?php _e('Nepal', 'textdomain'); ?></option>
                <option value="Netherlands" <?php echo ($country === 'Netherlands') ? 'selected' : ''; ?>><?php _e('Netherlands', 'textdomain'); ?></option>
                <option value="Netherlands Antilles" <?php echo ($country === 'Netherlands Antilles') ? 'selected' : ''; ?>><?php _e('Netherlands Antilles', 'textdomain'); ?></option>
                <option value="New Caledonia" <?php echo ($country === 'New Caledonia') ? 'selected' : ''; ?>><?php _e('New Caledonia', 'textdomain'); ?></option>
                <option value="New Zealand" <?php echo ($country === 'New Zealand') ? 'selected' : ''; ?>><?php _e('New Zealand', 'textdomain'); ?></option>
                <option value="Nicaragua" <?php echo ($country === 'Nicaragua') ? 'selected' : ''; ?>><?php _e('Nicaragua', 'textdomain'); ?></option>
                <option value="Niger" <?php echo ($country === 'Niger') ? 'selected' : ''; ?>><?php _e('Niger', 'textdomain'); ?></option>
                <option value="Nigeria" <?php echo ($country === 'Nigeria') ? 'selected' : ''; ?>><?php _e('Nigeria', 'textdomain'); ?></option>
                <option value="Niue" <?php echo ($country === 'Niue') ? 'selected' : ''; ?>><?php _e('Niue', 'textdomain'); ?></option>
                <option value="Norfolk Island" <?php echo ($country === 'Norfolk Island') ? 'selected' : ''; ?>><?php _e('Norfolk Island', 'textdomain'); ?></option>
                <option value="Northern Mariana Islands" <?php echo ($country === 'Northern Mariana Islands') ? 'selected' : ''; ?>><?php _e('Northern Mariana Islands', 'textdomain'); ?></option>
                <option value="Norway" <?php echo ($country === 'Norway') ? 'selected' : ''; ?>><?php _e('Norway', 'textdomain'); ?></option>
                <option value="Oman" <?php echo ($country === 'Oman') ? 'selected' : ''; ?>><?php _e('Oman', 'textdomain'); ?></option>
                <option value="Pakistan" <?php echo ($country === 'Pakistan') ? 'selected' : ''; ?>><?php _e('Pakistan', 'textdomain'); ?></option>
                <option value="Palau" <?php echo ($country === 'Palau') ? 'selected' : ''; ?>><?php _e('Palau', 'textdomain'); ?></option>
                <option value="Palestinian Territory, Occupied" <?php echo ($country === 'Palestinian Territory, Occupied') ? 'selected' : ''; ?>><?php _e('Palestinian Territory, Occupied', 'textdomain'); ?></option>
                <option value="Panama" <?php echo ($country === 'Panama') ? 'selected' : ''; ?>><?php _e('Panama', 'textdomain'); ?></option>
                <option value="Papua New Guinea" <?php echo ($country === 'Papua New Guinea') ? 'selected' : ''; ?>><?php _e('Papua New Guinea', 'textdomain'); ?></option>
                <option value="Paraguay" <?php echo ($country === 'Paraguay') ? 'selected' : ''; ?>><?php _e('Paraguay', 'textdomain'); ?></option>
                <option value="Peru" <?php echo ($country === 'Peru') ? 'selected' : ''; ?>><?php _e('Peru', 'textdomain'); ?></option>
                <option value="Philippines" <?php echo ($country === 'Philippines') ? 'selected' : ''; ?>><?php _e('Philippines', 'textdomain'); ?></option>
                <option value="Pitcairn" <?php echo ($country === 'Pitcairn') ? 'selected' : ''; ?>><?php _e('Pitcairn', 'textdomain'); ?></option>
                <option value="Poland" <?php echo ($country === 'Poland') ? 'selected' : ''; ?>><?php _e('Poland', 'textdomain'); ?></option>
                <option value="Portugal" <?php echo ($country === 'Portugal') ? 'selected' : ''; ?>><?php _e('Portugal', 'textdomain'); ?></option>
                <option value="Puerto Rico" <?php echo ($country === 'Puerto Rico') ? 'selected' : ''; ?>><?php _e('Puerto Rico', 'textdomain'); ?></option>
                <option value="Qatar" <?php echo ($country === 'Qatar') ? 'selected' : ''; ?>><?php _e('Qatar', 'textdomain'); ?></option>
                <option value="Reunion" <?php echo ($country === 'Reunion') ? 'selected' : ''; ?>><?php _e('Reunion', 'textdomain'); ?></option>
                <option value="Romania" <?php echo ($country === 'Romania') ? 'selected' : ''; ?>><?php _e('Romania', 'textdomain'); ?></option>
                <option value="Russian Federation" <?php echo ($country === 'Russian Federation') ? 'selected' : ''; ?>><?php _e('Russian Federation', 'textdomain'); ?></option>
                <option value="Rwanda" <?php echo ($country === 'Rwanda') ? 'selected' : ''; ?>><?php _e('Rwanda', 'textdomain'); ?></option>
                <option value="Saint Helena" <?php echo ($country === 'Saint Helena') ? 'selected' : ''; ?>><?php _e('Saint Helena', 'textdomain'); ?></option>
                <option value="Saint Kitts and Nevis" <?php echo ($country === 'Saint Kitts and Nevis') ? 'selected' : ''; ?>><?php _e('Saint Kitts and Nevis', 'textdomain'); ?></option>
                <option value="Saint Lucia" <?php echo ($country === 'Saint Lucia') ? 'selected' : ''; ?>><?php _e('Saint Lucia', 'textdomain'); ?></option>
                <option value="Saint Pierre and Miquelon" <?php echo ($country === 'Saint Pierre and Miquelon') ? 'selected' : ''; ?>><?php _e('Saint Pierre and Miquelon', 'textdomain'); ?></option>
                <option value="Saint Vincent and The Grenadines" <?php echo ($country === 'Saint Vincent and The Grenadines') ? 'selected' : ''; ?>><?php _e('Saint Vincent and The Grenadines', 'textdomain'); ?></option>
                <option value="Samoa" <?php echo ($country === 'Samoa') ? 'selected' : ''; ?>><?php _e('Samoa', 'textdomain'); ?></option>
                <option value="San Marino" <?php echo ($country === 'San Marino') ? 'selected' : ''; ?>><?php _e('San Marino', 'textdomain'); ?></option>
                <option value="Sao Tome and Principe" <?php echo ($country === 'Sao Tome and Principe') ? 'selected' : ''; ?>><?php _e('Sao Tome and Principe', 'textdomain'); ?></option>
                <option value="Saudi Arabia" <?php echo ($country === 'Saudi Arabia') ? 'selected' : ''; ?>><?php _e('Saudi Arabia', 'textdomain'); ?></option>
                <option value="Senegal" <?php echo ($country === 'Senegal') ? 'selected' : ''; ?>><?php _e('Senegal', 'textdomain'); ?></option>
                <option value="Serbia" <?php echo ($country === 'Serbia') ? 'selected' : ''; ?>><?php _e('Serbia', 'textdomain'); ?></option>
                <option value="Seychelles" <?php echo ($country === 'Seychelles') ? 'selected' : ''; ?>><?php _e('Seychelles', 'textdomain'); ?></option>
                <option value="Sierra Leone" <?php echo ($country === 'Sierra Leone') ? 'selected' : ''; ?>><?php _e('Sierra Leone', 'textdomain'); ?></option>
                <option value="Singapore" <?php echo ($country === 'Singapore') ? 'selected' : ''; ?>><?php _e('Singapore', 'textdomain'); ?></option>
                <option value="Slovakia" <?php echo ($country === 'Slovakia') ? 'selected' : ''; ?>><?php _e('Slovakia', 'textdomain'); ?></option>
                <option value="Slovenia" <?php echo ($country === 'Slovenia') ? 'selected' : ''; ?>><?php _e('Slovenia', 'textdomain'); ?></option>
                <option value="Solomon Islands" <?php echo ($country === 'Solomon Islands') ? 'selected' : ''; ?>><?php _e('Solomon Islands', 'textdomain'); ?></option>
                <option value="Somalia" <?php echo ($country === 'Somalia') ? 'selected' : ''; ?>><?php _e('Somalia', 'textdomain'); ?></option>
                <option value="South Africa" <?php echo ($country === 'South Africa') ? 'selected' : ''; ?>><?php _e('South Africa', 'textdomain'); ?></option>
                <option value="South Georgia and The South Sandwich Islands" <?php echo ($country === 'South Georgia and The South Sandwich Islands') ? 'selected' : ''; ?>><?php _e('South Georgia and The South Sandwich Islands', 'textdomain'); ?></option>
                <option value="Spain" <?php echo ($country === 'Spain') ? 'selected' : ''; ?>><?php _e('Spain', 'textdomain'); ?></option>
                <option value="Sri Lanka" <?php echo ($country === 'Sri Lanka') ? 'selected' : ''; ?>><?php _e('Sri Lanka', 'textdomain'); ?></option>
                <option value="Sudan" <?php echo ($country === 'Sudan') ? 'selected' : ''; ?>><?php _e('Sudan', 'textdomain'); ?></option>
                <option value="Suriname" <?php echo ($country === 'Suriname') ? 'selected' : ''; ?>><?php _e('Suriname', 'textdomain'); ?></option>
                <option value="Svalbard and Jan Mayen" <?php echo ($country === 'Svalbard and Jan Mayen') ? 'selected' : ''; ?>><?php _e('Svalbard and Jan Mayen', 'textdomain'); ?></option>
                <option value="Swaziland" <?php echo ($country === 'Swaziland') ? 'selected' : ''; ?>><?php _e('Swaziland', 'textdomain'); ?></option>
                <option value="Sweden" <?php echo ($country === 'Sweden') ? 'selected' : ''; ?>><?php _e('Sweden', 'textdomain'); ?></option>
                <option value="Switzerland" <?php echo ($country === 'Switzerland') ? 'selected' : ''; ?>><?php _e('Switzerland', 'textdomain'); ?></option>
                <option value="Syrian Arab Republic" <?php echo ($country === 'Syrian Arab Republic') ? 'selected' : ''; ?>><?php _e('Syrian Arab Republic', 'textdomain'); ?></option>
                <option value="Taiwan" <?php echo ($country === 'Taiwan') ? 'selected' : ''; ?>><?php _e('Taiwan', 'textdomain'); ?></option>
                <option value="Tajikistan" <?php echo ($country === 'Tajikistan') ? 'selected' : ''; ?>><?php _e('Tajikistan', 'textdomain'); ?></option>
                <option value="Tanzania, United Republic of" <?php echo ($country === 'Tanzania, United Republic of') ? 'selected' : ''; ?>><?php _e('Tanzania, United Republic of', 'textdomain'); ?></option>
                <option value="Thailand" <?php echo ($country === 'Thailand') ? 'selected' : ''; ?>><?php _e('Thailand', 'textdomain'); ?></option>
                <option value="Timor-leste" <?php echo ($country === 'Timor-leste') ? 'selected' : ''; ?>><?php _e('Timor-leste', 'textdomain'); ?></option>
                <option value="Togo" <?php echo ($country === 'Togo') ? 'selected' : ''; ?>><?php _e('Togo', 'textdomain'); ?></option>
                <option value="Tokelau" <?php echo ($country === 'Tokelau') ? 'selected' : ''; ?>><?php _e('Tokelau', 'textdomain'); ?></option>
                <option value="Tonga" <?php echo ($country === 'Tonga') ? 'selected' : ''; ?>><?php _e('Tonga', 'textdomain'); ?></option>
                <option value="Trinidad and Tobago" <?php echo ($country === 'Trinidad and Tobago') ? 'selected' : ''; ?>><?php _e('Trinidad and Tobago', 'textdomain'); ?></option>
                <option value="Tunisia" <?php echo ($country === 'Tunisia') ? 'selected' : ''; ?>><?php _e('Tunisia', 'textdomain'); ?></option>
                <option value="Turkey" <?php echo ($country === 'Turkey') ? 'selected' : ''; ?>><?php _e('Turkey', 'textdomain'); ?></option>
                <option value="Turkmenistan" <?php echo ($country === 'Turkmenistan') ? 'selected' : ''; ?>><?php _e('Turkmenistan', 'textdomain'); ?></option>
                <option value="Turks and Caicos Islands" <?php echo ($country === 'Turks and Caicos Islands') ? 'selected' : ''; ?>><?php _e('Turks and Caicos Islands', 'textdomain'); ?></option>
                <option value="Tuvalu" <?php echo ($country === 'Tuvalu') ? 'selected' : ''; ?>><?php _e('Tuvalu', 'textdomain'); ?></option>
                <option value="Uganda" <?php echo ($country === 'Uganda') ? 'selected' : ''; ?>><?php _e('Uganda', 'textdomain'); ?></option>
                <option value="Ukraine" <?php echo ($country === 'Ukraine') ? 'selected' : ''; ?>><?php _e('Ukraine', 'textdomain'); ?></option>
                <option value="United Arab Emirates" <?php echo ($country === 'United Arab Emirates') ? 'selected' : ''; ?>><?php _e('United Arab Emirates', 'textdomain'); ?></option>
                <option value="United Kingdom" <?php echo ($country === 'United Kingdom') ? 'selected' : ''; ?>><?php _e('United Kingdom', 'textdomain'); ?></option>
                <option value="United States" <?php echo ($country === 'United States') ? 'selected' : ''; ?>><?php _e('United States', 'textdomain'); ?></option>
                <option value="United States Minor Outlying Islands" <?php echo ($country === 'United States Minor Outlying Islands') ? 'selected' : ''; ?>><?php _e('United States Minor Outlying Islands', 'textdomain'); ?></option>
                <option value="Uruguay" <?php echo ($country === 'Uruguay') ? 'selected' : ''; ?>><?php _e('Uruguay', 'textdomain'); ?></option>
                <option value="Uzbekistan" <?php echo ($country === 'Uzbekistan') ? 'selected' : ''; ?>><?php _e('Uzbekistan', 'textdomain'); ?></option>
                <option value="Vanuatu" <?php echo ($country === 'Vanuatu') ? 'selected' : ''; ?>><?php _e('Vanuatu', 'textdomain'); ?></option>
                <option value="Venezuela" <?php echo ($country === 'Venezuela') ? 'selected' : ''; ?>><?php _e('Venezuela', 'textdomain'); ?></option>
                <option value="Viet Nam" <?php echo ($country === 'Viet Nam') ? 'selected' : ''; ?>><?php _e('Viet Nam', 'textdomain'); ?></option>
                <option value="Virgin Islands, British" <?php echo ($country === 'Virgin Islands, British') ? 'selected' : ''; ?>><?php _e('Virgin Islands, British', 'textdomain'); ?></option>
                <option value="Virgin Islands, U.S." <?php echo ($country === 'Virgin Islands, U.S.') ? 'selected' : ''; ?>><?php _e('Virgin Islands, U.S.', 'textdomain'); ?></option>
                <option value="Wallis and Futuna" <?php echo ($country === 'Wallis and Futuna') ? 'selected' : ''; ?>><?php _e('Wallis and Futuna', 'textdomain'); ?></option>
                <option value="Western Sahara" <?php echo ($country === 'Western Sahara') ? 'selected' : ''; ?>><?php _e('Western Sahara', 'textdomain'); ?></option>
                <option value="Yemen" <?php echo ($country === 'Yemen') ? 'selected' : ''; ?>><?php _e('Yemen', 'textdomain'); ?></option>
                <option value="Zambia" <?php echo ($country === 'Zambia') ? 'selected' : ''; ?>><?php _e('Zambia', 'textdomain'); ?></option>
                <option value="Zimbabwe" <?php echo ($country === 'Zimbabwe') ? 'selected' : ''; ?>><?php _e('Zimbabwe', 'textdomain'); ?></option>
                <!-- Add more countries -->
            </select>
            <input type="submit" value="Filter" class="button" style="width: 150px; height: 50px; font-size: medium;">
        </form>
        <table class="wp-list-table widefat fixed striped attendees">
            <thead>
                <tr>
                    <th><?php _e('Order ID', 'textdomain'); ?></th>
                    <th><?php _e('Date Created', 'textdomain'); ?></th>
                    <th><?php _e('Product Type', 'textdomain'); ?></th>
                    <th><?php _e('First Name', 'textdomain'); ?></th>
                    <th><?php _e('Last Name', 'textdomain'); ?></th>
                    <th><?php _e('Email', 'textdomain'); ?></th>
                    <th><?php _e('Organization', 'textdomain'); ?></th>
                    <th><?php _e('Dietary', 'textdomain'); ?></th>
                    <th><?php _e('Country', 'textdomain'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($attendees as $attendee): 
                    // Fetch WooCommerce order details
                    $order_id = $attendee->order_id;
                    $order = wc_get_order($order_id);
					if (!$order || in_array($order->get_status(), ['trash', 'cancelled'])) {
							continue;
						}
                    $date_created = $order ? $order->get_date_created()->date('Y-m-d') : 'N/A';
                    $product_names = $order ? implode(', ', array_map(function($item) {
                        return $item->get_name();
                    }, $order->get_items())) : 'N/A';
                ?>
                    <tr>
                        <td><a href="<?php echo esc_url(admin_url('post.php?post=' . $order_id . '&action=edit')); ?>"><?php echo esc_html($order_id); ?></a></td>
                        <td><?php echo esc_html($date_created); ?></td>
                        <td><?php echo esc_html($product_names); ?></td>
                        <td><?php echo esc_html($attendee->attendee_first_name); ?></td>
                        <td><?php echo esc_html($attendee->attendee_last_name); ?></td>
                        <td><?php echo esc_html($attendee->attendee_email); ?></td>
                        <td><?php echo esc_html($attendee->attendee_organization); ?></td>
                        <td><?php echo esc_html($attendee->attendee_dietary); ?></td>
                        <td><?php echo esc_html($attendee->attendee_country); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php
}


add_action('wp_ajax_flush_attendees', function () {
    global $wpdb;

    $table_name = $wpdb->prefix . 'ices_attendees';

    // Execute the flush (truncate) query
    $result = $wpdb->query("TRUNCATE TABLE `$table_name`");

    // Return a response based on the success or failure of the operation
    if ($result === false) {
        wp_send_json_error(['message' => 'Failed to flush the attendees table.']);
    } else {
        wp_send_json_success(['message' => 'The attendees table has been successfully flushed.']);
    }
});
