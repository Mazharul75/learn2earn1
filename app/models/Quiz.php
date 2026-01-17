<?php
class Quiz extends Model {
    public function addQuestion($data) {
        // 1. Check for existing quiz or create new
        $this->db->query("SELECT id FROM quizzes WHERE course_id = :cid");
        $this->db->bind(':cid', $data['course_id']);
        $quiz = $this->db->single();

        $quiz_id = $quiz ? $quiz['id'] : null;
        if (!$quiz_id) {
            $this->db->query("INSERT INTO quizzes (course_id) VALUES (:cid)");
            $this->db->bind(':cid', $data['course_id']);
            $this->db->execute();
            $quiz_id = $this->db->lastInsertId(); // Now works after Phase 1 fix
        }

        // 2. Insert the question using standardized keys
        $this->db->query("INSERT INTO questions (quiz_id, question_text, option_a, option_b, option_c, option_d, correct_option) 
                          VALUES (:qid, :text, :oa, :ob, :oc, :od, :correct)");
        $this->db->bind(':qid', $quiz_id);
        $this->db->bind(':text', $data['question_text']);
        $this->db->bind(':oa', $data['option_a']);
        $this->db->bind(':ob', $data['option_b']);
        $this->db->bind(':oc', $data['option_c']);
        $this->db->bind(':od', $data['option_d']);
        $this->db->bind(':correct', strtoupper($data['correct_option']));
        return $this->db->execute();
    }

    public function getQuizQuestions($course_id) {
        // This joins Quizzes and Questions to find questions for a specific course
        $this->db->query("SELECT q.* FROM questions q 
                          JOIN quizzes z ON q.quiz_id = z.id 
                          WHERE z.course_id = :cid");
        $this->db->bind(':cid', $course_id);
        return $this->db->resultSet();
    }

    public function getQuizByCourse($course_id) {
        $this->db->query("SELECT * FROM questions WHERE quiz_id = (SELECT id FROM quizzes WHERE course_id = :cid)");
        $this->db->bind(':cid', $course_id);
        return $this->db->resultSet();
    }

    public function gradeQuiz($course_id, $answers) {
        // Fetch questions
        $this->db->query("SELECT * FROM questions WHERE quiz_id = (SELECT id FROM quizzes WHERE course_id = :cid)");
        $this->db->bind(':cid', $course_id);
        $questions = $this->db->resultSet();
        
        $score = 0;
        $total = count($questions);

        foreach($questions as $q) {
            // Check if answer is set and matches correct option
            if(isset($answers[$q['id']]) && $answers[$q['id']] == $q['correct_option']) {
                $score++;
            }
        }

        // Calculate pass status (50% threshold)
        $passed = ($total > 0) && ($score / $total >= 0.5);

        return [
            'passed' => $passed,
            'score' => $score,
            'total' => $total
        ];
    }

    public function hasQuiz($course_id) {
        // FIX: Check the 'quizzes' table, because 'questions' does not have course_id
        $this->db->query("SELECT id FROM quizzes WHERE course_id = :cid LIMIT 1");
        $this->db->bind(':cid', $course_id);
        $this->db->execute();
        return ($this->db->rowCount() > 0);
    }
}