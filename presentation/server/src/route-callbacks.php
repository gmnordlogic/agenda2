<?php
/*
 Route GET /comm-oaths-rules
 @params - none
 @return json

Example
[
   {
     'id': 1,
     'ruleText': 'Rule1 text ( can contain html )'
   },
   {
     'id': 2,
     'ruleText': 'Rule2 text ( can contain html )'
   },
   {
     'id': 3,
     'ruleText': 'Rule3 text ( can contain html )'
   },
   {
     'id': 4,
     'ruleText': 'Rule4 text ( can contain html )'
   }
]
 */
$routeCommOathsRules = function ( $request, $response, $args ) use ( $container ) {

    $sql = 'SELECT * FROM comm_oaths_rules';
    $coRules = $container->get( 'db' )->_getFromDB( $sql );

    return json_encode( $coRules );
}; /* do not forget the trailing ; */


/*
 Route GET /comm-oaths-exam/question/{questionNumber:[0-9]+}

 @return json
 */
$routeCommOathsExamQuestion = function ( $request, $response, $args ) use ( $container ) {

    $numberOfExamQuestions = $container->get( 'settings' )[ 'conpExamQuestionsNumber' ];
    $minimumCorrectAnswersRequired = $container->get( 'settings' )[ 'conpExamCorrectQuestionsRequired' ];

    $questionNumber = 1;
    if ( isset( $args[ 'questionNumber' ] ) ) {
        $questionNumber = $args[ 'questionNumber' ];
    }
//TODO change this when login is available
    $applicantId = 1;

    $examService = new ExamService( $container->get( 'db' ), $numberOfExamQuestions, $minimumCorrectAnswersRequired );
    $createExamSessionIfNotCreated = TRUE;
    if ( $questionNumber === 1 ) {
        $createExamSessionIfNotCreated = TRUE;
    }
    $examSessionId = $examService->getExamSessionId( $applicantId, $createExamSessionIfNotCreated );

    $questionId = $examService->getQuestionId( $applicantId, $examSessionId, $questionNumber );
    $questionData = $examService->getQuestionDataById( $questionId );
    $questionData[ 'questionNumber' ] = $questionNumber;

    /**
     * [
     * 'questionNumber'
     * 'questionText'
     * 'questionType'
     * 'choicesList'
     * ]
     */
    return json_encode( $questionData );
};


/*
Route POST /comm/oaths-exam/question/{questionNumber:[0-9]+}/choice


params sent in post
choiceId -

@return json
*/
$routeCommOathsExamQuestionChoice = function ( $request, $response, $args ) use ( $container, $app ) {
    $numberOfExamQuestions = $container->get( 'settings' )[ 'conpExamQuestionsNumber' ];
    $minimumCorrectAnswersRequired = $container->get( 'settings' )[ 'conpExamCorrectQuestionsRequired' ];

    $questionNumber = 1;
    if ( isset( $args[ 'questionNumber' ] ) ) {
        $questionNumber = $args[ 'questionNumber' ];
    }
    //TODO change this when login is available
    $applicantId = 1;

    $examService = new ExamService( $container->get( 'db' ), $numberOfExamQuestions, $minimumCorrectAnswersRequired );
    $examSessionId = $examService->getExamSessionId( $applicantId );

    //TODO make sure the question_choice_id is a valid one
    $tmp = json_decode( $request->getBody() );
    $questionChoiceId = $tmp->choiceId;
    $count = $examService->answerQuestion( $questionChoiceId, $applicantId, $examSessionId, $questionNumber );

    $status = 'failed';
    if ( $count >= 1 ) {
        $status = 'success';
    }

    $result = [ 'choice_saved_status' => $status ];
    if ( $questionNumber == $numberOfExamQuestions ) {
        if ( !isset( $examSessionId ) ) {
            //throw error or set question to 1 and create a new session
            throw new Exception( 'Exam session not set' );
        } else {
            // the exam has been completed - update exam session status
            $nbCorrectAnswers = $examService->getNbOfCorrectAnswers( $applicantId, $examSessionId );

            $examSessionStatus = 'completed-failed';
            if ( $nbCorrectAnswers >= $minimumCorrectAnswersRequired ) {
                $examSessionStatus = 'completed-passed';
            }

            $sqlExamSessionUpdate = "UPDATE `exam_sessions` SET `status`=:examSessionStatus
                                     WHERE `id`=:examSessionId";
            $container->get( 'db' )->_pushUpdatesToDB( $sqlExamSessionUpdate, [ ':examSessionStatus' => $examSessionStatus, ':examSessionId' => $examSessionId ] );

            $result[ 'examResult' ] = ( $examSessionStatus === 'completed-passed' ) ? 'passed' : 'failed';
            $result[ 'countTotal' ] = $numberOfExamQuestions;
            $result[ 'countCorrect' ] = $nbCorrectAnswers;
            $result[ 'qn' ] = $questionNumber;
            $result[ 'numberOfExamQuestions' ] = $numberOfExamQuestions;
        }
    }

    return json_encode( $result );
};


/*
Route GET /comm/oaths-exam/results

returns the exam results for the current session if all quuestions have been answered
The exam results contain
countCorrect - number of questions answered correctly
countTotal - total number of questions per test
wrongQuestions - contains an array with an entry for each wrong wuestion having 'questionNumber', 'questionText', 'explanation' (in case the test was failed) and 'answerText' (with the correct answer in case the test was passed)

 */
$routeCommOathsExamResults = function ( $request, $response, $args ) use ( $container ) {
    $numberOfExamQuestions = $container->get( 'settings' )[ 'conpExamQuestionsNumber' ];
    $minimumCorrectAnswersRequired = $container->get( 'settings' )[ 'conpExamCorrectQuestionsRequired' ];

    //TODO change this when login is available
    $applicantId = 1;

    $examService = new ExamService( $container->get( 'db' ), $numberOfExamQuestions, $minimumCorrectAnswersRequired );
    $examSession = $examService->getExamSession( $applicantId );

    if ( empty( $examSession ) ) {
        // throw error
        throw new Exception( 'Exam session not set when trying to save choice' );
    }
    $examSessionId = $examSession[ 'id' ];
    $result = [ ];

    $nbCorrectAnswers = $nbCorrectAnswers = $examService->getNbOfCorrectAnswers( $applicantId, $examSessionId );

    switch ( $examSession[ 'status' ] ) {
        case 'completed-passed':
            $wrongQuestions = $examService->getCorrectChoicesForWrongAnswers( $applicantId, $examSessionId );
            break;
        case 'completed-failed':
            $wrongQuestions = $examService->getExplanationsForWrongAnswers( $applicantId, $examSessionId );
            break;
        default:
            throw new Exception( 'Trying to access the results before completing the exam session' );
            break;
    }
    $result[ 'wrongQuestions' ] = $wrongQuestions;
    $result[ 'countTotal' ] = $numberOfExamQuestions;
    $result[ 'countCorrect' ] = $nbCorrectAnswers;

    return json_encode( $result );
};
