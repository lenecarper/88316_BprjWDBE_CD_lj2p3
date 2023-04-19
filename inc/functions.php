<?php
    session_start();
    // Variables
    $_SESSION['question_amount'] = 0;
    $questionAmount = $_SESSION['question_amount'];
    $_SESSION['question_list'] = [];
    $questions = $_SESSION['question_list'];
    $_SESSION['active_survey'] = false;
    $activeSurvey = $_SESSION['active_survey'];

    // Initialize function that's called when the page loads
    function init()
    {
        // Check if the question amount input has been set
        if (isset($_POST['questions']))
        {
            // Make sure the questions cannot go higher than 5
            if ($_POST['questions'] > 5)
            {
                $questionAmount = 5;
                $activeSurvey = true;
            }
            // Make sure the questions cannot be lower than 1
            else if ($_POST['questions'] > 0)
            {
                $questionAmount = $_POST['questions'];
                $activeSurvey = true;
            }
            else
            {
                $questionAmount = 1;
                $activeSurvey = true;
            }
        }

        function db()
        {   // Connect to the MySQL database
            $db = new mysqli('localhost', 'root', '', 'rocsurvey');
        
            // Checks the connection
            if($db -> connect_errno)
            {
                echo "Connection failed " . $db -> connect_error;
                exit();
            }

            // Return the database
            return $db;
        }

        // Display a different form if the number of questions has been set or not yet
        // $activeSurvey = false;
        if ($activeSurvey == false)
        {
            echo
            '<div id="form-container">
            <form method="POST" action="index.php">
                <div class="flex row w-100 ml-2" style="max-width: 800px;"></div>
                <div class="column w-65 p-1"></div>
                <h2 class="play-once">FORM SETTINGS (REQUIRED)</h2>
                <div class="row w-100">
                <div class="field w-30">
                    <label class="glow text">Number of questions (5 max)</label>
                    <input id="questions" name="questions" class="settings-form" type="number" maxlength="1" required />
                </div>
                </div><br>
                <h2></h2>
                <div class="flex row mt-1">
                    <input id="submit" name="submit" class="green" type="submit" value="Save" >
                </div>
            </form>
            </div>';
        }
        else
        {
            // Pre-emptively display the title before the input fields
            echo
            '<div id="question-container">
            <form method="POST" action="index.php">
            <h2 class="play-once">ENTER QUESTIONS (REQUIRED)</h2>';
            // Loop through the questions and display different input fields
            for ($i = 0; $i < $questionAmount; $i++)
            {
                echo 
                '<div class="field w-25">
                <label class="glow text">Question' . ' ' . $i + 1 . '</label>
                <input id="question_survey" name="question' . $i + 1 . '" class="settings-form" type="text" maxlength="25" required />
                </div><br><br>';
            }
            // Display submit button outside the loop to make sure it doesn't get repeated
            echo 
            '<h2></h2>
            <div class="flex row mt-1">
            <input id="submit_questions" name="submit_questions" class="green" type="submit" value="Save questions" >
            </div></form></div>';
        }

        if (isset($_POST['submit_questions']))
        {
            echo
            '<div id="survey-container">
            <form method="POST" action="index.php">
            <h2 class="play-once">QUESTION SURVEY</h2>';
            // Use $q < $questionAmount (currently deprecated and only for debugging)
            for ($q = 0; $q < 5; $q++)
            {
                $questions = $_POST['question' . ($q + 1)];
                echo 
                '<div class="field w-25">
                <label class="glow text">Question' . ' ' . $q + 1 . '</label>
                <label class="glow text">' . '<b style="font-size: 1.2em;">' . $questions . '</b>' . '</label>
                <input id="question_survey" name="question' . ($q + 1) . '" class="settings-form" type="text" maxlength="25" required />
                </div><br>
                <div class="flex row mt-1">
                <input id="submit_answers" name="submit_answers" class="green" type="submit" value="Save answer" >
                </div></form></div>
                <br><h2></h2>';
            }
            uploadScore();
        }
    }

    function uploadScore()
    {
        // Check if there is a POST request
        if($_SERVER['REQUEST_METHOD'] == "POST")
        {            
            # Define variables
            $db = db();
            $question = $_POST['question1'];
            $answer = $_POST['question1'];

            # Gather all the data into an SQL query
            if (isset($_POST['submit_answers']))
            {
                $upload = "INSERT into survey (`question`, `answer`, answerDate) VALUES ('$question', '$answer', NOW())";
                # Query the data to be sent into the corresponding database tables
                $query = $db->query($upload) or die($db->error);
            }
        }
    }

    // init();
    // var_dump($_POST);
?>
