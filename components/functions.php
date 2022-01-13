<?php

/**
 * @param: $data: returns value thats needs to be debugged
 * @param: $type: gives type of debugging
 * @return: Array: $data, Debugtype: $type.
 */
function debugData($data, $type = "print_r")
{
    $return = '<pre>';
    switch ($type) {
        case "var_dump":
            $return .= var_dump($data);
            break;
        default:
            $return .= print_r($data);
            break;
    }
    $return .= '</pre>';

    return $return;
}

/**
 * @param: $db: returns database object 
 * @return: Int : id
 */

function getLastId($db)
{
    return mysqli_insert_id($db);
}


/**
 * @param: $loggedIn: true or false
 * @param: $link: current url
 * @return: Error message or true
 */

function CheckAcces($loggedIn, $link)
{
    if (substr($link, -9) == "index.php" || strpos($link, "password_forget.php") || substr($link, -14) == "Ticketsysteem/" || substr($link, -12) == "register.php") {
        return true;
    } else {
        if (!$loggedIn) {
            header("HTTP/1.1 403");
            die(include("../error/403.php"));
        } else {
            return true;
        }
    }
}

/**
 * @param: $db: return mysqli object
 * @param: $userId: returns id from user
 * @param: $message: returns the message that has been typed
 * @param: $issueId: returns id from the url
 */

function uploadMessage($db, $userId, $message, $issueId)
{
    $sql = "INSERT
            INTO    `message`
            (
                    `user_id`,
                    `date`,
                    `message`
            ) VALUES (
                    ?,
                    NOW(),
                    ?
            )
            ";

    $stmt = mysqli_prepare($db, $sql) or die(mysqli_error($db));
    mysqli_stmt_bind_param($stmt, "is", $userId, $message) or die(mysqli_error($db));
    mysqli_stmt_execute($stmt) or die(mysqli_error($db));
    mysqli_stmt_close($stmt);

    linkMessagetoIssue($db, getLastId($db), $issueId);
}

/**
 * @param: $db: return mysqli object
 * @param: $messageId: returns id from message
 * @param: $issueId: returns id from the url
 */

function linkMessagetoIssue($db, $messageId, $issueId)
{
    $sql = "INSERT
            INTO    `issue_message` 
            (
                    `issue_id`,
                    `message_id`,
                    `date`
            )
            VALUES 
            (
                    ?,
                    ?,
                    NOW()
            )
            ";

    $stmt = mysqli_prepare($db, $sql) or die(mysqli_error($db));
    mysqli_stmt_bind_param($stmt, "ii", $issueId, $messageId) or die(mysqli_error($db));
    mysqli_stmt_execute($stmt) or die(mysqli_error($db));
    mysqli_stmt_close($stmt);
}

/**
 * @param: $db: return mysqli object
 * @param: $userId: returns id from user
 * @param: $issueId: returns id from the url
 * @param: $action: returns which user has to take action
 */

function updateIssueAction($db, $userId, $issueId, $action)
{
    $sql = "
            INSERT
            INTO    `message`
            (
                    `user_id`,
                    `date`,
                    `message`
            ) VALUES (
                    ?,
                    NOW(),
                    ?
            )
           ";

    $stmt = mysqli_prepare($db, $sql) or die(mysqli_error($db));
    if ($action == 1) {
        $message = "De actie ligt bij: De klant";
    } else {
        $message = "De actie ligt bij: Bottom Up";
    }
    mysqli_stmt_bind_param($stmt, "is", $userId, $message) or die(mysqli_error($db));
    mysqli_stmt_execute($stmt) or die(mysqli_error($db));
    mysqli_stmt_close($stmt);

    linkMessagetoIssue($db, getLastId($db), $issueId);
}

/**
 * @param: $db: return mysqli object
 * @param: $issueId: returns id from user
 * @param: $action: returns id from the issue
 * @param: $messageId: returns which user has to take action
 * @return: mail function
 */

function notifyAction($db, $issueId, $action, $messageId)
{
    if ($action == 1) {
        $stmt = mysqli_prepare($db, " 
                SELECT  issue.title,
                        user.name,
                        user.email_adres,
                        issue.issue_action
                FROM issue
                INNER JOIN user
                ON user.user_id = issue.user_id
                WHERE issue_id = ?
            ") or die(mysqli_error($db));
        mysqli_stmt_bind_param($stmt, "i", $issueId) or die(mysqli_error($db));
        mysqli_stmt_execute($stmt) or die(mysqli_error($db));
        mysqli_stmt_bind_result($stmt, $issueTitle, $userName, $email, $dbAction);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);

        if ($dbAction != $action) {
            $stmt = mysqli_prepare($db, " 
                SELECT  message,
                        date
                FROM message
                WHERE message_id = ?
                ") or die(mysqli_error($db));
            mysqli_stmt_bind_param($stmt, "i", $messageId) or die(mysqli_error($db));
            mysqli_stmt_execute($stmt) or die(mysqli_error($db));
            mysqli_stmt_bind_result($stmt, $dbMessage, $date);
            mysqli_stmt_fetch($stmt);
            mysqli_stmt_close($stmt);

            mail(
                $email,
                "De actie is aangepast",
                "<h1>Geachte dhr/mevr {$userName},</h1>
                    <p>Betreffende de issue: {$issueTitle} </p>
                    <p>U heeft op " . date('d-m-Y', strtotime($date)) . " een antwoord ontvangen in uw issue met het volgende bericht: </p>
                    <p>'{$dbMessage}'</p>
                    <p>De actie van de issue ligt nu bij U.</p>
                    <br>
                    <p>Met vriendelijke groet,</p>
                    <p>Bottom up</p>
                    ",
                MAIL_HEADERS
            );
        }
    }
}

/**
 * @param: $db: return mysqli object
 * @param: $userId: returns id from user
 * @param: $issueId: returns id from the url
 * @param: $status: returns int with status
 * @return: true or false
 */

function issueStatusUpdate($db, $userId, $issueId, $status)
{
    $stmt = mysqli_prepare($db, " 
        SELECT issue.status,
               issue.title,
               user.name,
               user.email_adres
        FROM issue
        INNER JOIN user
        ON user.user_id = issue.user_id
        WHERE issue_id = ?
    ") or die(mysqli_error($db));
    mysqli_stmt_bind_param($stmt, "i", $issueId) or die(mysqli_error($db));
    mysqli_stmt_execute($stmt) or die(mysqli_error($db));
    mysqli_stmt_bind_result($stmt, $dbStatus, $issueTitle, $userName, $email);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);
    if ($dbStatus != $status) {

        $stmt = mysqli_prepare($db, " 
        INSERT
        INTO    `message`
        (
                `user_id`,
                `date`,
                `message`
        ) 
        VALUES 
        (
                ?,
                NOW(),
                ?
        )
    ") or die(mysqli_error($db));
        $message = "De status van uw issue is: ";
        if ($status == 1) {
            $message .= "Nieuw";
        } else if ($status == 2) {
            $message .= "In behandeling";
        } else if ($status == 3) {
            $message .= "On hold";
        } else {
            $message .= "Gesloten";
        }
        mysqli_stmt_bind_param($stmt, "is", $userId, $message) or die(mysqli_error($db));
        mysqli_stmt_execute($stmt) or die(mysqli_error($db));
        mysqli_stmt_close($stmt);

        linkMessagetoIssue($db, getLastId($db), $issueId);

        mail(
            $email,
            "Issue status aangepast",
            "<h1>Geachte dhr/mevr {$userName},</h1>
            <p>Betreffende de issue: {$issueTitle} </p>
            <p>{$message}</p>
            <br>
            <p>Met vriendelijke groet,</p>
            <p>Bottom up</p>
            ",
            MAIL_HEADERS
        );

        return true;
    } else {
        return false;
    }
}

/**
 * @param: $db: return mysqli object
 * @param: $issueId: returns id from the url
 * @param: $issuePri: returns int with priority
 */

function notifyPriUser($db, $issueId, $issuePri)
{
    $stmt = mysqli_prepare($db, " 
        SELECT issue.priority,
               issue.title,
               user.name,
               user.email_adres
        FROM issue
        INNER JOIN user
        ON user.user_id = issue.user_id
        WHERE issue_id = ?
    ") or die(mysqli_error($db));
    mysqli_stmt_bind_param($stmt, "i", $issueId) or die(mysqli_error($db));
    mysqli_stmt_execute($stmt) or die(mysqli_error($db));
    mysqli_stmt_bind_result($stmt, $dbPri, $issueTitle, $userName, $email);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);
    if ($dbPri != $issuePri) {

        $message = "Het prioriteitsniveau van uw issue is: ";
        if ($issuePri == 1) {
            $message .= "Laag";
        } else if ($issuePri == 2) {
            $message .= "Gemiddeld";
        } else if ($issuePri == 3) {
            $message .= "Hoog";
        } else {
            $message = "U heeft geen geldig prioriteitsniveau aangegeven.";
        }

        mail(
            $email,
            "Issue prioriteit aangepast",
            "<h1>Geachte dhr/mevr {$userName},</h1>
            <p>Betreffende de issue: {$issueTitle} </p>
            <p>{$message}</p>
            <br>
            <p>Met vriendelijke groet,</p>
            <p>Bottom up</p>
            ",
            MAIL_HEADERS
        );
    }
}

/**
 * @param: $db: return mysqli object
 * @param: $issueId: returns id from the url
 * @return: Array: Messages
 */

function getMessage($db, $issueId)
{
    $sql = "
            SELECT      `message`.`message`,
                        `user`.name,
                        `message`.appendex_url,
                        issue_message.date
            FROM        `message`   
            INNER JOIN  issue_message 
            ON          `message`.message_id = issue_message.message_id
            INNER JOIN  user
            ON          `message`.user_id = user.user_id
            WHERE       issue_message.issue_id = ?  
            ORDER BY    issue_message.message_id DESC
           ";

    $stmt = mysqli_prepare($db, $sql) or die(mysqli_error($db));
    mysqli_stmt_bind_param($stmt, 'i', $issueId) or die(mysqli_error($db));
    mysqli_stmt_execute($stmt) or die(mysqli_error($db));
    mysqli_stmt_bind_result($stmt, $message, $name, $appendex_url, $message_date);

    // check if there are messages
    mysqli_stmt_store_result($stmt);
    if (mysqli_stmt_num_rows($stmt) > 0) {
        $return = "";
        while (mysqli_stmt_fetch($stmt)) {
            if ($message == "De actie ligt bij: Bottom Up" || $message == "De actie ligt bij: De klant") {
                $return .= "<div class='col-lg-12 issue_choice'>";
                $return .= "<span class='d-block text-right mt-1'>" . date("H:i d-m-Y", strtotime($message_date)) . "</span>";
                $return .= "<p class='action_message'>{$message}</p>";
                $return .= "</div>";
            } else if ($message == "De status van uw issue is: Nieuw" || $message == "De status van uw issue is: In behandeling" || $message == "De status van uw issue is: On hold" || $message == "De status van uw issue is: Gesloten") {
                $return .= "<div class='col-lg-12 issue_choice'>";
                $return .= "<span class='d-block text-right mt-1'>" . date("H:i d-m-Y", strtotime($message_date)) . "</span>";
                $return .= "<p class='action_message'>{$message}</p>";
                $return .= "</div>";
            } else {
                $return .= "<div class='col-lg-12 message-view'>";
                $return .= "<p>" . ucFirst($name) . "<span class='float-right'>" . date("H:i d-m-Y", strtotime($message_date)) . "</span></p>";
                $return .= "<p class='title-messages'>" . nl2br($message) . "</p>";
                if ($appendex_url != NULL) {
                    $fileInfo = finfo_file(finfo_open(FILEINFO_MIME_TYPE), $appendex_url);
                    if (in_array($fileInfo, ["image/jpg", "image/jpeg", "image/png", "image/gif"])) {
                        $return .= "<p class='title-message mt-4'><img data-fancybox='gallery' class='img-appendex pointer' src='{$appendex_url}' alt='bijlagen'></p>";
                    }
                    $return .= "<p class='title-message'><a target='blank' class='dec-underline' href='{$appendex_url}'>Bijlage Bekijken.</a></p>";
                }
                $return .= "</div>";
            }
        }
    } else {
        $return = "<div class='col-lg-12 issue_choice'>";
        $return .= "<p class='action_message'>Er zijn nog geen berichten gevonden.</p>";
        $return .= "</div>";
    }
    mysqli_stmt_close($stmt);

    return $return;
}

/**
 * @param: $db: return mysqli object
 * @param: $issueId: returns id from the url
 * @return: int: number that signifies who has to take action
 */

function getActionIssue($db, $issueId)
{
    $sql = "
            SELECT      issue_action
            FROM        issue
            WHERE       issue_id = ?
           ";

    $stmt = mysqli_prepare($db, $sql) or die(mysqli_error($db));
    mysqli_stmt_bind_param($stmt, "i", $issueId);
    mysqli_stmt_execute($stmt) or die(mysqli_error($db));
    mysqli_stmt_bind_result($stmt, $issueAction);
    mysqli_stmt_fetch($stmt);

    return $issueAction;
}

/**
 * @param: $actionValue int: Number of action
 * @return: String: changes number into word
 */

function issueActionCheck($actionValue)
{
    $actionStat = [NULL => "Bottom Up", 1 => "Klant", 2 => "Bottom Up"];

    return $actionStat[$actionValue];
}

/**
 * @param: $db: returns mysqli object
 * @param: $issueId: returns the issueId that belongs to the issue in question
 * @param: $issueAction: returns the action which has been chosen for the customer or admin
 */

function uploadActionIssue($db, $issueId, $issueAction)
{
    $sql = "UPDATE  issue 
            SET     issue_action = ? 
            WHERE   issue_id = ?";

    $stmt = mysqli_prepare($db, $sql) or die(mysqli_error($db));
    mysqli_stmt_bind_param($stmt, "ii", $issueAction, $issueId) or die(mysqli_error($db));
    mysqli_stmt_execute($stmt) or die(mysqli_error($db));
    mysqli_stmt_close($stmt);
}

/**
 * @param: $db: returns mysqli object
 * @param: $id: returns the issueId that belongs to the issue in question
 */

function deleteIssue($db, $id)
{
    $stmt = mysqli_prepare($db, "
        DELETE 
        FROM issue 
        WHERE issue_id = ?
    ") or die(mysqli_error($db));
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt) or die(mysqli_error($db));
    mysqli_stmt_close($stmt);
}

/**
 * @param: $db: returns mysqli object
 * @param: $companyId: returns the issueId that belongs to the issue in question
 * @param: $userId: returns the action which has been chosen for the customer or admin
 * @param: $issueType: returns string with the issue type
 * @param: $filterStatus: returns int with status number
 * @param: $searchId: returns int with id of search action
 * @param: $searchTitle: returns string with title of the search action
 * @return: Table with issue overview
 */

function getIssueOverview($db, $companyId, $userId, $issueType, $filterStatus, $searchId, $searchTitle)
{
    $type = "s";
    $params = array($issueType);

    $return = '
        <table cellspacing="0" cellpadding="0" class="table">
        <thead>
            <tr>
                <th>id</th>
                <th>Naam</th>
                <th>Aanmaakdatum</th>
                <th>Sluitingsdatum</th>
                <th>Titel</th>
                ';

    ($companyId == NULL) ? ''  : $return .= '<th>Bedrijf</th><th>Herhalen</th>';

    $return .= '<th>Prioriteit</th>
                <th>Status</th>
                <th>Hoofdcategorie</th>
                <th>Categorie</th>
            </tr>
        </thead>
    <tbody>
    ';
    $query = "
        SELECT  issue.issue_id,
                issue.created_at,
                issue.title,
                issue.priority,
                issue.`status`,
                issue.category,
                issue.frequency,
                issue.sub_category, 
                user.name,
                company.name,
                issue.closed_at
        FROM    issue
        LEFT JOIN company 
        ON issue.company_id = company.company_id
        INNER JOIN user
        ON issue.user_id = user.user_id
        WHERE issue.category = ?
    ";

    switch ($companyId) {
        case NULL:
            $query .= " AND issue.user_id = ?";
            $type .= "i";
            array_push($params, $userId);
            break;
        case 1:
            $query .= "";
            break;
        default:
            $query .= " AND issue.company_id = ?";
            $type .= "i";
            array_push($params, $companyId);
    }
    switch ($filterStatus) {
        case '1':
            $query .= " AND issue.status = 1";
            break;
        case '2':
            $query .= " AND issue.status = 2";
            break;
        case '3':
            $query .= " AND issue.status = 3";
            break;
        case '4':
            $query .= " AND issue.status = 4";
            break;
        default:

            break;
    }

    if (!empty($searchId) && empty($searchTitle)) {
        $query .= " AND issue.issue_id = ?";
        $type .= "i";
        array_push($params, $searchId);
    } elseif (!empty($searchTitle) && empty($searchId)) {
        $query .= " AND issue.title LIKE CONCAT(?, '%')";
        $type .= "s";
        array_push($params, $searchTitle);
    } elseif (!empty($searchTitle) && !empty($searchId)) {
        $query .= " AND issue.title LIKE CONCAT(?, '%')";
        $query .= " AND issue.issue_id = ?";
        $type .= "si";
        array_push($params, $searchTitle, $searchId);
    }
    $query .= " ORDER BY `issue`.status";
    $stmt = mysqli_prepare($db, $query);
    call_user_func_array(array($stmt, "bind_param"), makeValuesReferenced(array_merge(array($type), $params)));
    mysqli_stmt_execute($stmt) or die(mysqli_error($db));
    mysqli_stmt_store_result($stmt) or die(mysqli_error($db));
    mysqli_stmt_bind_result($stmt, $issueId, $createdAt, $title, $priority, $status, $category, $frequency, $subCategory, $userName, $companyName, $closedAt);
    if (mysqli_stmt_num_rows($stmt) > 0) {
        while (mysqli_stmt_fetch($stmt)) {
            if ($filterStatus != 4 && $status == 4) {
                $lineThrough = "table_linethrough";
            } else {
                $lineThrough = "";
            }
            $return .= "<tr class='action {$lineThrough}' data-href='issue_detail.php?id={$issueId}'>
                    <td>{$issueId}</td>
                    <td>" . ucFirst($userName) . "</td>
                    <td>{$createdAt}</td>";
            ($closedAt == NULL) ? $return .= "<td>N.V.T.</td>"  : $return .= "<td>{$closedAt}</td>";

            $return .= "<td>{$title}</td>";
            ($companyId == NULL) ? ""  : $return .= "<td>{$companyName}</td> <td>{$frequency}</td> ";

            $return .= "
                    <td>" . priorityCheck($priority) . "</td>
                    <td>" . statuscheck($status) . "</td>
                    <td>" . ucFirst($category) . "</td>
                    <td>" . ucFirst($subCategory) . "</td>
                </tr>";
        }
    } else {
        if (!empty($searchTitle) || !empty($searchId)) {
            $return .= "<td colspan='11'>U heeft momenteel geen nieuwe {$issueType} aanvragen of u filter heeft geen resultaten opgeleverd</td>";
        } else {
            $return .= "<td colspan='11'>U heeft momenteel geen nieuwe {$issueType} aanvragen.</td>";
        }
    }
    $return .= "</tbody></table>";

    return $return;
}

/**
 * @param: $arr: an array with bind param values
 * @return: an array with specific references
 * this function makes from merged to seperate and individual values for the bind param function
 */
function makeValuesReferenced($arr)
{
    $refs = array();
    foreach ($arr as $key => $value)
        $refs[$key] = &$arr[$key];

    return $refs;
}

/**
 * @param: $priorityValue int: Number of priority
 * @return: String: changes number into word
 */

function priorityCheck($priorityValue)
{
    $priorityStat = [1 => "Laag", 2 => "Gemiddeld", 3 => "Hoog"];

    return $priorityStat[$priorityValue];
}

/**
 * @param: $statusValue int: Number of status
 * @return: String: changes number into word
 */

function statusCheck($statusValue)
{
    $statusStat = [1 => "Nieuw", 2 => "In behandeling", 3 => "On hold", 4 => "Gesloten"];

    return $statusStat[$statusValue];
}

/**
 * @param: $file: returns file object with properties
 * @return: true or false
 */

function checkIfFile($file)
{
    return is_uploaded_file($_FILES[$file]["tmp_name"]);
}

/**
 * @param: $fileName: returns file name
 * @return: true or false
 */

function checkFileSize($fileName)
{
    if ($_FILES[$fileName]["size"] <= 5000000) {
        return true;
    } else {
        return false;
    }
}

/**
 * @param: $fileName: returns file name
 * @param: $mimeArray: returns array with MIME types
 * @return: true or false
 */

function checkFileType($fileName, $mimeArray)
{
    $fileInfo = finfo_file(finfo_open(FILEINFO_MIME_TYPE), $_FILES[$fileName]["tmp_name"]);
    if (in_array($fileInfo, $mimeArray)) {
        if (!$_FILES[$fileName]["error"] > 0) {
            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }
}

/**
 * @param: $issueId: returns id of issue
 * @param: $path: returns file path
 * @return: true or false
 */

function makeFolder($issueId, $path)
{
    $directory = $path . $issueId;
    if (!file_exists($directory)) {
        mkdir($directory, 0777);
    }

    return true;
}

/**
 * @param: $directory: returns directory to file
 * @param: $fileName: returns file name
 * @return: true or false
 */

function checkFileExist($directory, $fileName)
{
    return file_exists($directory . $fileName);
}

/**
 * @param: $directory: returns directory to file
 * @return: true
 */

function deleteFile($directory)
{
    $files = glob($directory . '*'); // get all file names
    foreach ($files as $file) { // iterate files
        if (is_file($file)) {
            unlink($file); // delete file
        }
    }

    return true;
}

/**
 * @param: $db: returns mysqli object
 * @param: $file: returns file object with properties
 * @param: $tableName: returns name of the selected table
 * @param: $recordName: returns name of selected record
 * @param: $relationId string: name of relation
 * @param: $Id int: relation ID
 * @param: $directory: returns directory
 * @return: true or false
 */

function uploadFile($db, $file, $tableName, $recordName, $relationId, $Id, $directory)
{
    $type = "";
    $params = array();

    $query = "UPDATE " . $tableName;

    $query .= " SET " . $recordName . " = ? ";
    $type .= "s";
    array_push($params,  $directory . $_FILES[$file]["name"]);

    $query .= "WHERE " . $relationId . " = ?";
    $type .= "i";
    array_push($params,  $Id);


    $stmt = mysqli_prepare($db, $query) or die(mysqli_error($db));
    call_user_func_array(array($stmt, "bind_param"), makeValuesReferenced(array_merge(array($type), $params)));

    if (move_uploaded_file($_FILES[$file]["tmp_name"], realpath(dirname(getcwd())) . $directory . $_FILES[$file]["name"]) && mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        return true;
    } else {
        return false;
    }
}

/**
 * @param: $category string: returns category of issue 
 * @param: $subCat string: returns subcategory of issue
 * @return: returns Array options for dropdown menu
 */

function getCatOptions($category, $subCat = "")
{
    $options = "";
    if ($category == "Ticket") {
        $optionsArray = array("Klachten", "Feedback");
    } else if ($category == "Dienst/service") {
        $optionsArray = array("Vijvers", "Volière", "Schuuronderhoud", "Tuinonderhoud");
    } else if ($category == "Product") {
        $optionsArray = array("Tuingereedschap", "Gereedschap opslag", "Machines", "Planten", "Tuin verzorging");
    }
    foreach ($optionsArray as $value) {
        $checked =  ($subCat == $value ? "selected" : "");
        $options .= "<option " . $checked . ">" . $value . "</option>";
    }

    return $options;
}
