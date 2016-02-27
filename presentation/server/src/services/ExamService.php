<?php
// namespace Conp\Exam;
class ExamService {
    //PDO instance
    private $db = null;
    private $numberOfExamQuestions;
    private $minimumCorrectAnswersRequired;

    public function __construct($db,$numberOfExamQuestions=24,$minimumCorrectAnswersRequired=22) {
        $this->db = $db;
        $this->numberOfExamQuestions = $numberOfExamQuestions;
        $this->minimumCorrectAnswersRequired = $minimumCorrectAnswersRequired;
    }

    public function getExamSession($applicantId) {
        $sqlEs = "SELECT * FROM exam_sessions as `es`
                WHERE `es`.`applicant_id` = :applicant_id
                    AND ( `es`.status = 'completed-failed' OR `es`.status = 'completed-passed' )
                ORDER BY `updated` DESC
                LIMIT 1;";
        $examSession = $this->db->_getFromDB($sqlEs,[':applicant_id'=>$applicantId]);

        if (!empty($examSession)) { // get the current exam session id
            $examSession = $examSession[0];
        } else {
            throw new \Exception('Exam session not available');
        }

        return $examSession;
    }

    public function getExamSessionId($applicantId, $createIfNotFound = false) {
      $sqlEs = "SELECT * FROM exam_sessions as `es`
              WHERE `es`.`applicant_id` = :applicant_id AND `es`.`status` = 'in-progress'
              ORDER BY `updated` DESC;";
      $examSession = $this->db->_getFromDB($sqlEs,[':applicant_id'=>$applicantId]);
      if (!empty($examSession)) { // get the current exam session id
          $examSessionId = $examSession[0]['id'];
      } elseif($createIfNotFound) { // create a new exam session
          $examSessionId = $this->createExamSession($applicantId);
          $this->prepareQuestionsForTheExamSession($applicantId, $examSessionId);
      } else {
          throw new \Exception('Exam session not available');
      }

      return $examSessionId;
    }

    public function createExamSession($applicantId) {
      $sqlCreateEs = "INSERT INTO `exam_sessions` (`applicant_id`,`status`,`created_at`, `updated`)
                      VALUES (:applicantId, 'in-progress', NOW(), CURRENT_TIMESTAMP)";
      $examSessionId = $this->db->_pushToDB($sqlCreateEs,[':applicantId'=> $applicantId]);

      return $examSessionId;
    }

    public function prepareQuestionsForTheExamSession($applicantId, $examSessionId) {
      // prepare the test questions by inserting them into the exam session answers
      // select a set of 24 random question
      // NOTE: if the total number of questions in the table will be bigger than a couple of thousands we should find another way of selecting randomly
      // $sqlEsQ = "INSERT INTO `exam_session_answers` (`applicant_id`,`question_number`,`question_id`,`exam_session_id`)
      //            SELECT :applicantId,:qn,`id`,:examSessionId FROM `exam_questions`
      //            ORDER BY RAND()
      //            LIMIT :numberOfExamQuestions";
      $sqlExamQuestionIds = "SELECT `id` FROM `exam_questions`
                             ORDER BY RAND()
                             LIMIT ".$this->numberOfExamQuestions;
      $examSessionQuestionIds = $this->db->_getFromDB($sqlExamQuestionIds);

      $sqlExamSessionQuestions = "INSERT INTO `exam_session_answers`
                                  (`applicant_id`,`question_number`,`question_id`,`exam_session_id`)
                                  VALUES ";
      $qNb = 1;
      foreach ($examSessionQuestionIds as $key => $questionId) {
          if ($key>0) {
              $sqlExamSessionQuestions .= ",";
          }
          $sqlExamSessionQuestions .= "(:applicantId,".$qNb.",".$questionId['id'].",:examSessionId)";
          $qNb +=1;
      }
      $sqlExamSessionQuestions.=";";

      $countInserted = $this->db->_pushUpdatesToDB($sqlExamSessionQuestions,[':applicantId'=>$applicantId,':examSessionId'=>$examSessionId]);

      if ($countInserted!==$this->numberOfExamQuestions) {
          throw new \Exception('Unable to prepare questions for the exam session');
      }

    }

    public function getQuestionId($applicantId, $examSessionId, $questionNumber) {
        $sqlQuestionId = "SELECT question_id FROM `exam_session_answers`
                          WHERE `applicant_id`=:applicantId
                               AND `exam_session_id` = :examSessionId
                               AND `question_number` = :questionNumber;
                          ";
        $questionId = $this->db->_getFromDB($sqlQuestionId,[':applicantId' => $applicantId,
                                                                        ':examSessionId' => $examSessionId,
                                                                        ':questionNumber' => $questionNumber]);

        if (!empty($questionId) && isset($questionId[0]['question_id'])){
            $questionId = $questionId[0]['question_id'];
        } else {
            throw new \Exception('Unable to get question id');
        }

        return $questionId;
    }

    public function getQuestionDataById($questionId) {
        // get the question data
        $sqlQ = "SELECT question_text as questionText, question_type as questionType
                 FROM exam_questions as q
                 JOIN exam_question_choices as qc ON q.id = qc.question_id
                 WHERE q.id=:id";
        $question = $this->db->_getFromDB($sqlQ,[':id'=>$questionId]);
        $question = $question[0];

        $sqlC = "SELECT question_choice_text as questionChoiceText,  id
                 FROM exam_question_choices qc
                 WHERE qc.question_id = :id
                 ORDER BY id ASC";
        $questionChoices = $this->db->_getFromDB($sqlC,[':id'=>$questionId]);

        $questionData = [
            'questionText'   => $question['questionText'],
            'questionType'   => $question['questionType'],
            'choicesList'    => $questionChoices
        ];

        return $questionData;
    }

    public function answerQuestion($questionChoiceId, $applicantId, $examSessionId, $questionNumber) {
        $sqlUpdateQuestionChoice = "UPDATE `exam_session_answers`
                                    SET `question_choice_id`=:questionChoiceId
                                    WHERE `applicant_id`=:applicantId
                                      AND `exam_session_id` = :examSessionId
                                      AND `question_number` = :questionNumber;
                                   ";

        $count = $this->db->_pushUpdatesToDB($sqlUpdateQuestionChoice,[
                                              ':questionChoiceId' =>$questionChoiceId,
                                              ':applicantId' => $applicantId,
                                              ':examSessionId' => $examSessionId,
                                              ':questionNumber' => $questionNumber]);

        return $count;
    }

    public function getNbOfCorrectAnswers($applicantId, $examSessionId) {
        $sqlExamSessionNbCorrect = "SELECT COUNT(*) as nb_correct_answers FROM `exam_session_answers` as `esa`
                                    JOIN `exam_question_choices` as `eqc` ON `esa`.`question_choice_id` = `eqc`.`id`
                                    WHERE `esa`.`applicant_id`=:applicantId
                                      AND `esa`.`exam_session_id`=:examSessionId
                                      AND `eqc`.`is_correct` = 1";
        $nbCorrectAnswers = $this->db->_getFromDB($sqlExamSessionNbCorrect, [':applicantId'=>$applicantId,':examSessionId'=>$examSessionId]);

        $nbCorrectAnswers = $nbCorrectAnswers[0]['nb_correct_answers'];

        return $nbCorrectAnswers;
    }

    public function getCorrectChoicesForWrongAnswers($applicantId, $sessionId) {
        $sqlExamSessionWrongQuestions = "SELECT `esa`.`question_number` as questionNumber,
                                                `eq`.`question_text` as questionText,
                                                `eqc2`.`question_choice_text` as correctAnswerText
                                    FROM `exam_session_answers` as `esa`
                                    JOIN `exam_questions` as `eq` ON `esa`.`question_id` = `eq`.`id`
                                    JOIN `exam_question_choices` as `eqc` ON `esa`.`question_choice_id` = `eqc`.`id`
                                    JOIN `exam_question_choices` as `eqc2` ON (`esa`.`question_id` = `eqc2`.`question_id` AND `eqc2`.`is_correct`=1)
                                    WHERE `esa`.`applicant_id`=:applicantId
                                      AND `esa`.`exam_session_id`=:examSessionId
                                      AND `eqc`.`is_correct` = 0";
        $correctAnswers = $this->db->_getFromDB($sqlExamSessionWrongQuestions, [':applicantId'=>$applicantId,':examSessionId'=>$examSessionId]);

        return $correctAnswers;
    }

    public function getExplanationsForWrongAnswers($applicantId, $examSessionId) {
        $sqlExamSessionWrongQuestions = "SELECT `esa`.`question_number` as questionNumber,
                                                `eq`.`question_text` as questionText,
                                                `eq`.`explanation`
                                    FROM `exam_session_answers` as `esa`
                                    JOIN `exam_questions` as `eq` ON `esa`.`question_id` = `eq`.`id`
                                    JOIN `exam_question_choices` as `eqc` ON `esa`.`question_choice_id` = `eqc`.`id`
                                    WHERE `esa`.`applicant_id`=:applicantId
                                      AND `esa`.`exam_session_id`=:examSessionId
                                      AND `eqc`.`is_correct` = 0";
        $explanations = $this->db->_getFromDB($sqlExamSessionWrongQuestions, [':applicantId'=>$applicantId,':examSessionId'=>$examSessionId]);

        return $explanations;
    }
}
