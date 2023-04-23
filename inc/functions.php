<?php
    session_start();

    function handlePost()
    {
        if($_SERVER['REQUEST_METHOD'] == "POST")
        {
            // Check if the next form should be loaded
            if(isset($_POST['numQuestions']))
            {
                $_SESSION['question_amount'] = $_POST['questions'];
            }
            // Check if the next form should be loaded
            if(isset($_POST['setQuestions']))
            {
                $_SESSION['questions'] = $_POST['questions'];
            }
            // Check if the next form should be loaded
            if(isset($_POST['setAnswers']))
            {
            }
            // Check if the next form should be loaded
            if(isset($_POST['saveData']))
            {
                // Save all session data to SQL database
                uploadScore();
            }
            if(isset($_POST['deleteData']))
            {
                // Clear all session data and truncate SQL database
                uploadScore();
                session_destroy();
            }
            // Check if the reset button was clicked
            if(isset($_POST['reset']))
            {
                // Destroy the session and clear all data
                session_destroy();
            }
        }
        // Reload the page
        header("location:index.php");
    }

    // Initialize function that's called when the page loads
    function init()
    {
        // Session Variables (indexes)
        if(!isset($_SESSION['formStep']))
        {
            $_SESSION['formStep'] = 0;
        }
        if(!isset($_SESSION['formQuestions']))
        {
            $_SESSION['formQuestions'] = [];
        }
        if(!isset($_SESSION['formAnswers']))
        {
            $_SESSION['formAnswers'] = [];
        }
        if(!isset($_SESSION['question_amount']))
        {
            $_SESSION['question_amount'] = 3;
        }
        if($_SESSION['question_amount'] > 5)
        {
            $_SESSION['question_amount'] = 5;
        }
        $_SESSION['surveyCount'] = 0;

        checkState();
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

    function uploadScore()
    {
        // Check if there is a POST request
        if($_SERVER['REQUEST_METHOD'] == "POST")
        {
            # Define variables
            $db = db();

            $question = [];
            $answer = [];

            $data = $_SESSION['question_amount'];
            // If the user has requested to POST all their data into the database
            if (isset($_POST['saveData']))
            {
                // Loop until there are no more questions left
                for ($temp = 0; $temp < $data; $temp++)
                {   // Push the Questions and Answers into an array to insert into the database
                    array_push($question, $_SESSION['formQuestions'][$temp]);
                    array_push($answer, $_SESSION['formAnswers'][$temp]);

                    # Gather all the data into an SQL query
                    $upload = "INSERT into survey (`question`, `answer`, answerDate) VALUES ('$question[$temp]', '$answer[$temp]', NOW())";
                    # Query the data to be sent into the corresponding database tables
                    $query = $db->query($upload) or die($db->error);
                }
            }
            // If the user has requested to delete all database data
            else if (isset($_POST['deleteData']))
            {
                $upload = "TRUNCATE TABLE `survey`";
                $query = $db->query($upload) or die($db->error);
                session_destroy();
                header("location:index.php");
            }
        }
    }

    // Currently deprecated function to get Questions and Answers from the database
    // Using $_SESSION variables instead to display the user's personal content

    // function getScore()
    // {   // Connect to the SQL database
    //     $db = db();

    //     $data = 'SELECT * from survey LIMIT 3';
    //     $result = $db->query($data) or die($db->error);
    //     // Insert all stored data into the database
    //     $score = $result->fetch_all(MYSQLI_ASSOC);
    //     // Check if there are any objects in the database
    //     if (count($score) > 0)
    //     { // Loop through all the highscores and print them out into the leaderboard
    //     foreach($score as $point) 
    //     {
    //         echo "<table border='1' class='leaderboard'><tr><th>Question</th><th>Answer</th><th>Answer Date</th></tr>";
    //         echo "<tr>";          
    //         echo "<td>" . $point['question'] . "</td>";
    //         echo "<td>" . $point['answer'] . "</td>";
    //         echo "<td>" . $point['answerDate'] . "</td>";
    //         echo "</tr>";
    //         echo "</table>";
    //     }
    //     } else
    //     { // If there are no highscores to display in the leaderboard
    //         echo "No highscores yet! Be the first one by playing a match.";
    //     }
    // }

    function getScore()
    {
        $data = $_SESSION['question_amount'];
        for ($a = 0; $a < $data; $a++)
        {
            echo "<br><table border='1' class='leaderboard'><tr><th>Question</th><th>Answer</th></tr>";
            echo "<tr>";          
            echo "<td class='form-td'>" . $_SESSION['formQuestions'][$a] . "</td>";
            echo "<td class='form-td'>" . $_SESSION['formAnswers'][$a] . "</td>";
            echo "</tr>";
            echo "</table>";
        }
    }

    function checkState()
    {
        if ($_SESSION['formStep'] == 0)
        {
            settingsForm();
            if (isset($_POST['numQuestions']) && $_POST['questions'] != "")
            {
                $_SESSION['formStep']++;
                handlePost();
            }
        }
        elseif ($_SESSION['formStep'] == 1)
        {
            questionForm();
            if (isset($_POST['setQuestions']))
            {
                $_SESSION['formStep']++;
                handlePost();
            }
        }
        elseif($_SESSION['formStep'] == 2)
        {
            surveyForm();
            if (isset($_POST['setAnswers']))
            {
                $_SESSION['formStep']++;
                handlePost();
            }
        }
        elseif($_SESSION['formStep'] == 3)
        {
            saveForm();
            if (isset($_POST['saveData']))
            {
                uploadScore();
                $_SESSION['formStep'] = 0;
                handlePost();
                session_destroy();
            }
        }
    }

    function settingsForm()
    {
        // Display a different form if the number of questions has been set or not yet
        echo
        '<div id="form-container">
        <form method="POST" action="index.php">
            <div class="flex row w-100 ml-2" style="max-width: 800px;"></div>
            <div class="column w-65 p-1"></div>
            <h2 class="play-once">FORM SETTINGS (REQUIRED)</h2>
            <div class="row w-100">
            <div class="field w-30">
                <label class="glow text">Number of questions</label>
                <input id="questions" name="questions" class="settings-form" type="number" maxlength="1" required />
            </div>
            </div><br>
            <h2></h2>
            <div class="flex row mt-1">
                <input id="submit" name="numQuestions" class="green" type="submit" value="Save" />
                <input id="reset" name="reset" class="red" type="submit" value="Reset" />
            </div>
        </form>
        </div>';
    }

    function questionForm()
    {
        // Pre-emptively display the title before the input fields
        echo
        '<div id="question-container">
        <form method="POST" action="index.php">
        <h2 class="play-once">ENTER QUESTIONS (REQUIRED)</h2>';
        // Loop through the questions and display different input fields
        for ($i = 0; $i < $_SESSION['question_amount']; $i++)
        {
            echo 
            '<div class="field w-25">
            <label class="glow text">Question' . ' ' . $i + 1 . '</label>
            <input id="question_survey" name="question' . $i + 1 . '" class="settings-form" type="text" maxlength="25" required />
            </div><br><br>';
            if (isset($_POST['setQuestions']))
            {
                $question = $_POST['question' . ($i + 1)];
                array_push($_SESSION['formQuestions'], $question);
                var_dump($_SESSION['formQuestions']);
            }
        }
        // Display submit button outside the loop to make sure it doesn't get repeated
        echo 
        '<h2></h2>
        <div class="flex row mt-1">
        <input id="submit_questions" name="setQuestions" class="green" type="submit" value="Save questions" >
        <input id="reset" name="reset" class="red" type="submit" value="Reset" />
        </div></form></div>';
    }

    function surveyForm()
    {
        echo
        '<div id="survey-container">
        <form method="POST" action="index.php">
        <h2 class="play-once">QUESTION SURVEY</h2>';
        for ($q = 0; $q < $_SESSION['question_amount']; $q++)
        {
            // for ($s = 0; $s < $_SESSION['surveyCount']; $s++)
            // {

            // }
            echo 
            '<div class="field w-25">
            <label class="glow text">Question' . ' ' . $q + 1 . '</label>
            <label class="glow text">' . '<b style="font-size: 1.2em;">' . $_SESSION['formQuestions'][$q] . '</b>' . '</label>
            <input id="question_survey" name="answer' . ($q + 1) . '" class="settings-form" type="text" maxlength="25" required />
            </div><br>
            <div class="flex row mt-1">

            </div></div>
            <br><h2></h2>';
            if (isset($_POST['setAnswers']))
            {
                $answer = $_POST['answer' . ($q + 1)];
                array_push($_SESSION['formAnswers'], $answer);
            }
        }
        echo
        '<input id="submit_answers" name="setAnswers" class="green" type="submit" value="Save answer" >
        <input id="reset" name="reset" class="red" type="submit" value="Reset" /></form>';
        var_dump($_SESSION['formAnswers']);
    }

    function saveForm()
    {
        ?>
        <form action="index.php" method="POST">
            <button name="saveData">Save To Database</button>
            <button name="deleteData" id="deleteData">Delete Data</button>            
        </form>
        <?php
        getScore();
    }
?>
