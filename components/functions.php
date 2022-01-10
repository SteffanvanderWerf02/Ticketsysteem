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

function getLastId($db)
{
    return mysqli_insert_id($db);
}

function CheckAcces($loggedIn, $link)
{
    if (substr($link, -9) == "index.php") {
        return true;
    } else {
        if (!$loggedIn) {
            return die(header("HTTP/1.1 403 Forbidden"));
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
 * @return: Array: $data, Debugtype: $type.
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

function issueStatusUpdate($db, $userId, $issueId, $status)
{
    $stmt = mysqli_prepare($db, " 
        SELECT issue.status
        FROM issue
        WHERE issue_id = ?
    ") or die(mysqli_error($db));
    mysqli_stmt_bind_param($stmt, "i", $issueId) or die(mysqli_error($db));
    mysqli_stmt_execute($stmt) or die(mysqli_error($db));
    mysqli_stmt_bind_result($stmt, $dbStatus);
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

        return true;
    } else {
        return false;
    }
}

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
    mysqli_stmt_close($stmt);

    return $return;
}

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

function issueActionCheck($actionValue)
{
    $actionStat = [NULL => "Bottom Up", 1 => "Klant", 2 => "Bottom Up"];

    return $actionStat[$actionValue];
}

/**
 * @param: $db: returns mysqli object
 * @param: $issueId: returns the issueId that belongs to the issue in question
 * @param: $issueAction: returns the action which has been chosen for the customer or admin
 * @return: Object: $db, Int: $issueId, Int: $issueAction.
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
            $return .= "<td colspan='10'>U heeft momenteel geen nieuwe {$issueType} aanvragen of u filter heeft geen resultaten opgeleverd</td>";
        } else {
            $return .= "<td colspan='10'>U heeft momenteel geen nieuwe {$issueType} aanvragen.</td>";
        }
    }
    $return .= "</tbody></table>";

    return $return;
}
//this function makes from merged to seperate and individual values for the bind param function
function makeValuesReferenced($arr)
{
    $refs = array();
    foreach ($arr as $key => $value)
        $refs[$key] = &$arr[$key];

    return $refs;
}

function priorityCheck($priorityValue)
{
    $priorityStat = [1 => "Laag", 2 => "Gemiddeld", 3 => "Hoog"];

    return $priorityStat[$priorityValue];
}

function statusCheck($statusValue)
{
    $statusStat = [1 => "Nieuw", 2 => "In behandeling", 3 => "On hold", 4 => "Gesloten"];

    return $statusStat[$statusValue];
}

function checkIfFile($file)
{
    return is_uploaded_file($_FILES[$file]["tmp_name"]);
}

function checkFileSize($fileName)
{
    if ($_FILES[$fileName]["size"] <= 5000000) {
        return true;
    } else {
        return false;
    }
}

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

function makeFolder($issueId, $path)
{
    $directory = $path . $issueId;
    if (!file_exists($directory)) {
        mkdir($directory, 0777);
    }

    return true;
}



function checkFileExist($directory, $fileName)
{
    return file_exists($directory . $fileName);
}

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
