<?php
    // Variables
    $_SESSION['question_amount'] = 0;
    $_SESSION['first_question'] = "";
    $_SESSION['second_question'] = "";
    $_SESSION['third_question'] = "";
    $_SESSION['fourth_question'] = "";
    $_SESSION['fifth_question'] = "";
    $_SESSION['active_survey'] = false;

    function init()
    {
        if (isset($_POST['questions']))
        {
            if ($_POST['questions'] > 5)
            {
                $_SESSION['question_amount'] = 5;
                $_SESSION['active_survey'] = true;
            }
            else if ($_POST['questions'] > 0)
            {
                $_SESSION['question_amount'] = $_POST['questions'];
                $_SESSION['active_survey'] = true;
            }
            else
            {
                $_SESSION['question_amount'] = 1;
                $_SESSION['active_survey'] = true;
            }
        }

        if ($_SESSION['active_survey'] == false)
        {
            echo
            '<div id="form-container">
            <form method="POST" action="index.php">
                <div class="flex row w-100 ml-2" style="max-width: 800px;">
                <div class="column w-65 p-1">
                <h2 class="play-once">FORM SETTINGS (REQUIRED)</h2>
                <div class="row w-100">
                <div class="field w-30">
                    <label class="glow text">Number of questions</label>
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
            echo '<div id="form-container" style="display: none"></div>';
            echo '<h1>test</h1>';
        }
    }

    init();
?>