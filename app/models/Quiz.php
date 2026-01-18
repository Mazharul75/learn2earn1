<?php
require_once __DIR__ . '/../core/Database.php';

class Quiz {
    private $connection;

    public function __construct() {
        $database = new Database();
        $this->connection = $database->getConnection();
    }

    public function addQuestion($data) {
        // 1. Get/Create Quiz ID
        $q1 = "SELECT id FROM quizzes WHERE course_id = ?";
        $stmt1 = $this->connection->prepare($q1);
        $stmt1->bind_param("i", $data['course_id']);
        $stmt1->execute();
        $res = $stmt1->get_result();
        $row = $res->fetch_assoc();

        if ($row) {
            $quiz_id = $row['id'];
        } else {
            $q2 = "INSERT INTO quizzes (course_id) VALUES (?)";
            $stmt2 = $this->connection->prepare($q2);
            $stmt2->bind_param("i", $data['course_id']);
            $stmt2->execute();
            $quiz_id = $this->connection->insert_id;
        }

        // 2. Add Question
        $correct = strtoupper($data['correct_option']);
        $query = "INSERT INTO questions (quiz_id, question_text, option_a, option_b, option_c, option_d, correct_option) 
                  VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt3 = $this->connection->prepare($query);
        $stmt3->bind_param("issssss", $quiz_id, $data['question_text'], $data['option_a'], $data['option_b'], $data['option_c'], $data['option_d'], $correct);
        return $stmt3->execute();
    }

    public function getQuizQuestions($course_id) {
        $query = "SELECT q.* FROM questions q 
                  JOIN quizzes z ON q.quiz_id = z.id 
                  WHERE z.course_id = ?";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("i", $course_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function hasQuiz($course_id) {
        $query = "SELECT id FROM quizzes WHERE course_id = ? LIMIT 1";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("i", $course_id);
        $stmt->execute();
        return $stmt->get_result()->num_rows > 0;
    }

    public function gradeQuiz($course_id, $answers) {
        // Fetch Correct Answers
        $query = "SELECT id, correct_option FROM questions WHERE quiz_id = (SELECT id FROM quizzes WHERE course_id = ?)";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("i", $course_id);
        $stmt->execute();
        $questions = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        $score = 0;
        $total = count($questions);

        foreach ($questions as $q) {
            if (isset($answers[$q['id']]) && $answers[$q['id']] == $q['correct_option']) {
                $score++;
            }
        }

        $passed = ($total > 0) && ($score / $total >= 0.5);

        return [
            'passed' => $passed,
            'score' => $score,
            'total' => $total
        ];
    }
}
?>