<?php
// Routes
// $prefix = '/restapi';
$prefix = '';


$app->get($prefix.'/comm-oaths-rules', $routeCommOathsRules);
$app->get($prefix.'/comm-oaths-exam/question/{questionNumber:[0-9]+}', $routeCommOathsExamQuestion);
$app->post($prefix.'/comm-oaths-exam/question/{questionNumber:[0-9]+}/choice', $routeCommOathsExamQuestionChoice);
$app->get($prefix.'/comm-oaths-exam/results', $routeCommOathsExamResults);


// Appointment info page : GET
$app->get( $prefix . '/get_co_appointment_info', function ( $request, $response, $args ) {
    $applicant_id = 10; // TODO change this.. is only for test
    $sql = 'SELECT previous_appointment AS prevApptCommOaths, previous_app_revoked AS prevApptRevoked, previous_revocation_reason AS ReasonForRevoke, previous_app_jurisdiction AS jurisdictionOfPrevAppt, year(previous_app_expiry_date) AS expOfPrevAPPyear, month(previous_app_expiry_date) AS expOfPrevAPPmonth, day(previous_app_expiry_date) AS expOfPrevAPPday, previous_app_required_business_name AS nameOfBusinessForPrevAppt, previous_app_required_location AS locationOfBusinessForPrevAppt, current_app_required_business AS nameOfBusinessForCurrentAppt, current_app_city AS cityOfBusinessForCurrentAppt, current_app_province AS provinceOfBusinessForCurrentAppt, current_app_reason AS reasonForAppt, current_app_types_of_docs AS typesOfDoc, no_docs_monthly AS documentsToBeCommissioned
          FROM applicants_appointment_info WHERE `applicant_id` = :applicant_id';

    $appInfo = $this->db->_getFromDB( $sql, array ( 'applicant_id' => $applicant_id ) );

    return json_encode( $appInfo );
} );

// Appointment Info Page : SAVE
$app->post( $prefix . '/save_co_appointment_info', function ( $request, $response, $args ) {

    $applicant_id = 9; // TODO this must be changed. is for testing only!!!

    $sql = "SELECT id FROM applicants_appointment_info WHERE applicant_id=:applicant_id";
    $appID = $this->db->_getFromDB( $sql, array ( 'applicant_id' => $applicant_id ) );

    // set the rules for validation!
    $rules = array (
        // 'applicant_id'                     => 'required|integer',
        'prevApptCommOaths'                => 'alpha_numeric|max_len,1',
        'prevApptRevoked'                  => 'alpha_numeric|max_len,1',
        //        'ReasonForRevoke' => '',
        'previous_app_expiry_date'         => 'alpha_dash',
        //        'jurisdictionOfPrevAppt' => '',
        //        'nameOfBusinessForPrevAppt' => '',
        //        'locationOfBusinessForPrevAppt' => '',
        //        'nameOfBusinessForCurrentAppt' => '',
        //        'cityOfBusinessForCurrentAppt' => '',
        'provinceOfBusinessForCurrentAppt' => 'required|alpha_numeric|min_len,2',
        //        'reasonForAppt'                    => 'required|alpha_numeric|min_len,1',
        //        'typesOfDoc'                       => 'required|alpha_numeric|min_len,1',
        'documentsToBeCommissioned'        => 'required|integer'
    );

    // set the filters if is the case
    $filters = array (
        // 'applicant_id'                     => 'trim|sanitize_numbers',
        'prevApptCommOaths'                => 'trim',
        'prevApptRevoked'                  => 'trim',
        'ReasonForRevoke'                  => 'trim|sanitize_string',
        'previous_app_expiry_date'         => 'trim',
        'jurisdictionOfPrevAppt'           => 'trim|sanitize_string',
        'nameOfBusinessForPrevAppt'        => 'trim|sanitize_string',
        'locationOfBusinessForPrevAppt'    => 'trim|sanitize_string',
        'nameOfBusinessForCurrentAppt'     => 'trim|sanitize_string',
        'cityOfBusinessForCurrentAppt'     => 'trim|sanitize_string',
        'provinceOfBusinessForCurrentAppt' => 'trim|sanitize_string',
        //        'reasonForAppt'                    => 'trim|sanitize_string',
        //        'typesOfDoc'                       => 'trim|sanitize_string',
        'documentsToBeCommissioned'        => 'trim|sanitize_numbers'
    );
    $gump = new GUMP();

    $person = json_decode( $request->getBody() );
    if ( !isset( $person->expOfPrevAPPyear ) || $person->expOfPrevAPPyear == '' ) {
        $person->previous_app_expiry_date = '0000-00-00';
    } else {
        $person->previous_app_expiry_date = $person->expOfPrevAPPyear . substr( '0' . $person->expOfPrevAPPmonth, -2 ) . substr( '0' . $person->expOfPrevAPPday, -2 );
    }
//echo $person->previous_app_expiry_date . " ---- " . $person->expOfPrevAPPyear;exit;
    //$validated = $gump->validate($gump->filter($person, $filters), $rules);
    $gump->validation_rules( $rules );
    $gump->filter_rules( $filters );
    $validated_data = $gump->run( (array) $person );

    if ( $validated_data === FALSE ) { // Houston... we have a problem
        $errors = array (
            'error'  => TRUE,
            'errors' => $gump->get_errors_array()
        );

        return json_encode( $errors );
    } else { // awesome... we pass the test
        $person = (object) $validated_data;
        $params = array (
            'previous_appointment'                => $person->prevApptCommOaths,
            'previous_app_revoked'                => $person->prevApptRevoked,
            'previous_revocation_reason'          => $person->ReasonForRevoke,
            'previous_app_expiry_date'            => $person->previous_app_expiry_date,
            'previous_app_jurisdiction'           => $person->jurisdictionOfPrevAppt,
            'previous_app_required_business_name' => $person->nameOfBusinessForPrevAppt,
            'previous_app_required_location'      => $person->locationOfBusinessForPrevAppt,
            'current_app_required_business'       => $person->nameOfBusinessForCurrentAppt,
            'current_app_city'                    => $person->cityOfBusinessForCurrentAppt,
            'current_app_province'                => $person->provinceOfBusinessForCurrentAppt,
            'current_app_reason'                  => $person->reasonForAppt,
            'current_app_types_of_docs'           => $person->typesOfDoc,
            'no_docs_monthly'                     => $person->documentsToBeCommissioned
        );
        if ( count( $appID ) > 0 ) {
            $sql = "UPDATE applicants_appointment_info SET
                    previous_appointment=:previous_appointment, previous_app_revoked=:previous_app_revoked, previous_revocation_reason=:previous_revocation_reason, previous_app_expiry_date=:previous_app_expiry_date, previous_app_jurisdiction=:previous_app_jurisdiction, previous_app_required_business_name=:previous_app_required_business_name, previous_app_required_location=:previous_app_required_location, current_app_required_business=:current_app_required_business, current_app_city=:current_app_city, current_app_province=:current_app_province, current_app_reason=:current_app_reason, current_app_types_of_docs=:current_app_types_of_docs, no_docs_monthly=:no_docs_monthly
                    WHERE id=:id ;";
            $params[ 'id' ] = $appID[ 0 ][ 'id' ];
            $saveData = $this->db->_pushUpdatesToDB( $sql, $params, FALSE );
        } else {
            $sql = "INSERT INTO applicants_appointment_info (
              		applicant_id, previous_appointment, previous_app_revoked, previous_revocation_reason, previous_app_expiry_date, previous_app_jurisdiction, previous_app_required_business_name, previous_app_required_location, current_app_required_business, current_app_city, current_app_province, current_app_reason, current_app_types_of_docs, no_docs_monthly, created_date
                  ) VALUES (
              		:applicant_id, :previous_appointment, :previous_app_revoked, :previous_revocation_reason, :previous_app_expiry_date, :previous_app_jurisdiction, :previous_app_required_business_name, :previous_app_required_location, :current_app_required_business, :current_app_city, :current_app_province, :current_app_reason, :current_app_types_of_docs, :no_docs_monthly, now()
                  );";
            $params[ 'applicant_id' ] = $applicant_id;

            $saveData = $this->db->_pushToDB( $sql, $params, FALSE );
        }

        return json_encode( $saveData );
    }

    return json_encode( '' );
} );

// Criminal Record Check page : GET
$app->get( $prefix . '/get_co_criminal_record_check_info', function ( $request, $response, $args ) {
    $applicant_id = 1; // TODO this must be changed on the real app - now is for testing only!!!!!

    $sql = "SELECT c.offense AS typeOfOffense, YEAR(c.offense_date) AS offenseYear, MONTH(c.offense_date) AS offenseMonth, DAY(c.offense_date) AS offenseDay, '' AS foundGuiltyOfOffense, '' AS signature FROM applicants_criminal_records AS c WHERE applicant_id=:applicant_id";
    $crcInfo = $this->db->_getFromDB( $sql, array ( 'applicant_id' => $applicant_id ) );
    $sql = "SELECT '' AS typeOfOffense, '' AS offenseYear, '' AS offenseMonth, '' AS offenseDay, have_criminal_record AS foundGuiltyOfOffense, signature FROM applications WHERE applicant_id=:applicant_id ORDER BY application_date DESC LIMIT 0,1;";
    $signInfo = $this->db->_getFromDB( $sql, array ( 'applicant_id' => $applicant_id ) );
    if ( count( $crcInfo ) > 0 AND count( $signInfo ) > 0 ) {
        $crcInfo[ 0 ][ 'signature' ] = $signInfo[ 0 ][ 'signature' ];
        $crcInfo[ 0 ][ 'foundGuiltyOfOffense' ] = $signInfo[ 0 ][ 'foundGuiltyOfOffense' ];
    } elseif (!(count( $crcInfo ) > 0) AND count( $signInfo ) > 0 ) {
        $crcInfo = $signInfo;
    }
    return json_encode( $crcInfo );
} );

// Criminal Record Check Page : SAVE
$app->post( $prefix . '/save_co_criminal_record_check_info', function ( $request, $response, $args ) {
    $applicant_id = 1; // TODO this must be changed in production ... is for TEST only!!!
    // set rules for validation
    $rules = array (
        // 'applicant_id'         => 'required|integer',
        //'typeOfOffense' => 'alpha_numeric',
        'offense_date'         => 'alpha_numeric',
        'foundGuiltyOfOffense' => 'alpha_numeric|max_len,1',
        'signature'            => 'required|valid_name|min_len,4'
    );

    // set filters for validation
    $filters = array (
        //'applicant_id'         => 'trim|sanitize_numbers',
        'typeOfOffense'        => 'trim|sanitize_string',
        'offense_date'         => 'trim',
        'foundGuiltyOfOffense' => 'trim',
        'signature'            => 'trim|sanitize_string'

    );

    $sql = "SELECT id FROM applicants_criminal_records WHERE applicant_id=:applicant_id";
    $crcID = $this->db->_getFromDB( $sql, array ( 'applicant_id' => $applicant_id ) );


    $gump = new GUMP();

    $person = json_decode( $request->getBody() );
    //print_R($person);exit;
    $terms = isset( $person->checkboxes ) ? $person->checkboxes : [ ];

    //$validated = $gump->validate($gump->filter($person, $filters), $rules);
    $gump->validation_rules( $rules );
    $gump->filter_rules( $filters );
    $person->offense_date = $person->offenseYear . substr( '0' . $person->offenseMonth, -2 ) . substr( '0' . $person->offenseDay, -2 );
    $validated_data = $gump->run( (array) $person );

    if ( $validated_data === FALSE ) { // Houston... we have a problem
        $errors = array (
            'error'  => TRUE,
            'errors' => $gump->get_errors_array()
        );

        return json_encode( $errors );
    } else { // awesome... we pass the test

        $person = (object) $validated_data;
        //print $person->applicant_id;
        $sql = "SELECT id FROM applications WHERE applicant_id = :applicant_id ORDER BY application_date DESC LIMIT 0, 1;";
        //echo $sql;
        $params = array (
            'applicant_id' => $applicant_id
        );
        $ids = $this->db->_getFromDB( $sql, $params );
        if ( empty( $ids ) || !isset( $ids[ 0 ][ 'id' ] ) ) {
            $sql = "INSERT INTO applications (applicant_id, application_date, signature) VALUES (:applicant_id, now(), :signature);";
            $params = array ( 'applicant_id' => $applicant_id, 'signature' => $person->signature );
            $application_id = $this->db->_pushToDB( $sql, $params );
        } else {
            $application_id = $ids[ 0 ][ 'id' ];
        }

        //update applications to set have_criminal_records !!
        $sql = "UPDATE applications SET
					have_criminal_record = :have_criminal_record, signature=:signature
					WHERE
					applicant_id = :applicant_id AND
					id = :application_id
		";
        $params = array (
            'have_criminal_record' => $person->foundGuiltyOfOffense,
            'applicant_id'         => $applicant_id,
            'application_id'       => $application_id,
            'signature'            => $person->signature
        );
        //echo $sql;
        $saveData = $this->db->_pushUpdatesToDB( $sql, $params, FALSE );

        if ( count( $terms ) > 0 ) {
            // save the terms accepted by applicant
            $sql = "INSERT INTO applicants_terms (applicant_id, terms_id, accepted) VALUES ";
            $accepted = array ();
            $i = 0;
            foreach ( $terms as $term => $accept ) {
                $accepted[ $i ] = "(" . $applicant_id . ", " . (int) $term . ", '" . $accept . "')";
                $i++;
            }
            $sql .= implode( ",", $accepted );
            //echo $sql;
            $this->db->_pushToDB( $sql );
        }
        // check if have crimiinal record, if yes save it
        if ( $person->foundGuiltyOfOffense == '1' or $person->foundGuiltyOfOffense ) {
            if ( count( $crcID ) > 0 ) {
                $sql = "UPDATE applicants_criminal_records SET offense=:offense, offense_date=:offense_date WHERE id=:id;";
                $params = array (
                    'id'           => $crcID[ 0 ][ 'id' ],
                    'offense'      => $person->typeOfOffense,
                    'offense_date' => $person->offense_date
                );
                $saveData = $this->db->_pushUpdatesToDB( $sql, $params, FALSE );
            } else {
                $sql = "INSERT INTO applicants_criminal_records (
							applicant_id, offense, offense_date
						) VALUES (
							:applicant_id, :offense, :offense_date
						);";
                $params = array (
                    'applicant_id' => $applicant_id,
                    'offense'      => $person->typeOfOffense,
                    'offense_date' => $person->offense_date
                );
                $saveData = $this->db->_pushToDB( $sql, $params, FALSE );
            }
        }

        return json_encode( $saveData );
    }
} );

// Personal Info Page

$app->get( $prefix . '/get_co_personal_info', function ( $request, $response, $args ) {

    //TODO change this when login is available
    $applicantId = '99';

    $sql = 'SELECT fname AS firstName, lname AS lastName, email, address, city, province, postalcode AS postalCode, phone AS phoneNumber
          FROM applicants_personal_info WHERE `applicant_id` = :applicant_id';
    $personalInfo = $this->db->_getFromDB( $sql, array ( 'applicant_id' => $applicantId ) );

    return json_encode( $personalInfo );
} );


$app->post( $prefix . '/save_co_personal_info', function ( $request, $response, $args ) {

    //TODO change this when login is available
    $applicantId = '99';

    // check if user exist
    $sql = 'SELECT id FROM applicants_personal_info WHERE `applicant_id` = :applicant_id';
    $personalInfoId = $this->db->_getFromDB( $sql, array ( 'applicant_id' => $applicantId ) );


    // set the rules for validation!
    $rules = array (
        'firstName'   => 'required|valid_name',
        'lastName'    => 'required|valid_name',
        'email'       => 'required|valid_email',
        'address'     => 'required',
        'city'        => 'required|alpha_space',
        'province'    => 'required|alpha|exact_len,2',
        'postalCode'  => 'required|alpha_numeric',
        'phoneNumber' => 'required|phone_number'
    );

    // set the filters if is the case
    $filters = array (
        'firstName'   => 'trim|sanitize_string',
        'lastName'    => 'trim|sanitize_string',
        'email'       => 'trim',
        'address'     => 'trim|sanitize_string',
        'city'        => 'trim|sanitize_string',
        'province'    => 'trim|sanitize_string',
        'postalCode'  => 'trim|sanitize_numbers',
        'phoneNumber' => 'trim'
    );

    $gump = new GUMP();

    $person = json_decode( $request->getBody() );
    $person = (array) $person;

    $gump->validation_rules( $rules );
    $gump->filter_rules( $filters );
    $validated_data = $gump->run( $person );

    if ( $validated_data === FALSE ) {
        $errors = array (
            'error'  => TRUE,
            'errors' => $gump->get_errors_array()
        );

        return json_encode( $errors );
    } else {
        $person = $validated_data;

        if ( count( $personalInfoId ) > 0 ) {
            $sql = 'UPDATE `applicants_personal_info` SET
              `fname` = :fname, `lname` = :lname, `email` = :email, `address` = :address, `city` = :city, `province` = :province, `postalcode` = :postalcode, `phone` = :phone
              WHERE id = :id ;';

            $params = array (
                'id'         => $personalInfoId[ 0 ][ 'id' ],
                'fname'      => $person[ 'firstName' ],
                'lname'      => $person[ 'lastName' ],
                'email'      => $person[ 'email' ],
                'address'    => $person[ 'address' ],
                'city'       => $person[ 'city' ],
                'province'   => $person[ 'province' ],
                'postalcode' => $person[ 'postalCode' ],
                'phone'      => $person[ 'phoneNumber' ]
            );

            $saveData = $this->db->_pushUpdatesToDB( $sql, $params, FALSE );

        } else {
            $sql = 'INSERT INTO `applicants_personal_info`
              (`applicant_id`, `fname`, `lname`, `email`, `address`, `city`, `province`, `postalcode`, `phone`)
              VALUES
              (:applicant_id, :fname, :lname, :email, :address, :city, :province, :postalcode, :phone);';

            $params = array (
                'applicant_id' => $applicantId,
                'fname'        => $person[ 'firstName' ],
                'lname'        => $person[ 'lastName' ],
                'email'        => $person[ 'email' ],
                'address'      => $person[ 'address' ],
                'city'         => $person[ 'city' ],
                'province'     => $person[ 'province' ],
                'postalcode'   => $person[ 'postalCode' ],
                'phone'        => $person[ 'phoneNumber' ]
            );

            $saveData = $this->db->_pushToDB( $sql, $params, FALSE );
        }

        return json_encode( 'Done' );
    }
} );


// Employment Info Page

$app->get( $prefix . '/get_co_employment_info', function ( $request, $response, $args ) {

    //TODO change this when login is available
    $applicantId = '99';

    $sql = 'SELECT employer AS name, occupation, nature_employer_business AS businessType, employer_address AS address, employer_city AS city, employer_province AS province, employer_postalcode AS postalCode, employer_phone AS phone, gov_employee_ministry AS govMinistry, gov_employee AS govEmployee, employer_manager_email AS managerEmail FROM applicants_employment_info WHERE `applicant_id` = :applicant_id';
    $employmentInfo = $this->db->_getFromDB( $sql, array ( 'applicant_id' => $applicantId ) );

    return json_encode( $employmentInfo );
} );


$app->post($prefix.'/save_co_employment_info', function($request, $response, $args) {

  //TODO change this when login is available
  $applicantId = '99';

  // check if user exist
  $sql = 'SELECT id FROM applicants_employment_info where `applicant_id` = :applicant_id';
  $employmentInfoId = $this->db->_getFromDB($sql, array('applicant_id' => $applicantId));

  // set the rules for validation!
  $rules = array(
    'name'         => 'required|valid_name',
    'occupation'   => 'required|alpha_space',
    'businessType' => 'required|alpha_numeric',
    'address'      => 'required',
    'city'         => 'required|alpha_space',
    'province'     => 'required|alpha|exact_len,2',
    'postalCode'   => 'required|alpha_numeric',
    'phone'        => 'required|phone_number',
    'govMinistry'  => 'alpha_numeric',
    'govEmployee'  => 'contains,1 0',
    'managerEmail' => 'required|valid_email'
  );

  // set the filters if is the case
  $filters = array(
    'name'         => 'trim|sanitize_string',
    'occupation'   => 'trim|sanitize_string',
    'businessType' => 'trim|sanitize_string',
    'address'      => 'trim|sanitize_string',
    'city'         => 'trim|sanitize_string',
    'province'     => 'trim|sanitize_string',
    'postalCode'   => 'trim|sanitize_string',
    'phone'        => 'trim',
    'govMinistry'  => 'trim|sanitize_string',
    'govEmployee'  => 'trim',
    'managerEmail' => 'trim'
  );

  $gump = new GUMP();

  $employment = json_decode($request->getBody());
  $employment = (array)$employment;

  $gump->validation_rules($rules);
  $gump->filter_rules($filters);
  $validated_data = $gump->run($employment);

  if ($validated_data === FALSE) {
    $errors = array(
      'error'   => TRUE,
      'errors'  => $gump->get_errors_array()
    );
    return json_encode($errors);
  } else {
    $employment = $validated_data;

    if(count($employmentInfoId) > 0 ) {
      $sql = 'UPDATE `applicants_employment_info` SET
              `employer` = :employer, `occupation` = :occupation, `nature_employer_business` = :nature_employer_business, `employer_address` = :employer_address, `employer_city` = :employer_city, `employer_province` = :employer_province, `employer_postalcode` = :employer_postalcode, `employer_phone` = :employer_phone, `gov_employee_ministry` =:gov_employee_ministry, `gov_employee` = :gov_employee, `employer_manager_email` = :employer_manager_email
              where id = :id ;';

      $params = array(
                  'id'                      => $employmentInfoId[0]['id'],
                  'employer'                => $employment['name'],
                  'occupation'              => $employment['occupation'],
                  'nature_employer_business'=> $employment['businessType'],
                  'employer_address'        => $employment['address'],
                  'employer_city'           => $employment['city'],
                  'employer_province'       => $employment['province'],
                  'employer_postalcode'     => $employment['postalCode'],
                  'employer_phone'          => $employment['phone'],
                  'gov_employee_ministry'   => $employment['govMinistry'],
                  'gov_employee'            => $employment['govEmployee'],
                  'employer_manager_email'  => $employment['managerEmail']
                );


      $saveData = $this->db->_pushUpdatesToDB($sql, $params, FALSE);

    } else {


      $sql = 'INSERT INTO `applicants_employment_info`
                (`applicant_id`, `employer`, `occupation`, `nature_employer_business`, `employer_address`, `employer_city`, `employer_province`, `employer_postalcode`, `employer_phone`, `gov_employee_ministry`, `gov_employee`, `employer_manager_email`, `created_date`)
                 VALUES
                (:applicant_id, :employer, :occupation, :nature_employer_business, :employer_address, :employer_city, :employer_province, :employer_postalcode, :employer_phone, :gov_employee_ministry, :gov_employee, :employer_manager_email, now());';

       $params = array(
                  'applicant_id'            => $applicantId,
                  'employer'                => $employment['name'],
                  'occupation'              => $employment['occupation'],
                  'nature_employer_business'=> $employment['businessType'],
                  'employer_address'        => $employment['address'],
                  'employer_city'           => $employment['city'],
                  'employer_province'       => $employment['province'],
                  'employer_postalcode'     => $employment['postalCode'],
                  'employer_phone'          => $employment['phone'],
                  'gov_employee_ministry'   => $employment['govMinistry'],
                  'gov_employee'            => $employment['govEmployee'],
                  'employer_manager_email'  => $employment['managerEmail']
                );

        $saveData = $this->db->_pushToDB($sql, $params, FALSE);
    }
    return json_encode('Done');
  }
});
