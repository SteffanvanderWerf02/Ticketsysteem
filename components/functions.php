<?php

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
                <th>Aanmaak datum</th>
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
                company.name
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
    $stmt = mysqli_prepare($db, $query);
    call_user_func_array(array($stmt, "bind_param"), makeValuesReferenced(array_merge(array($type), $params)));
    mysqli_stmt_execute($stmt) or die(mysqli_error($db));
    mysqli_stmt_store_result($stmt) or die(mysqli_error($db));
    mysqli_stmt_bind_result($stmt, $issueId, $createdAt, $title, $priority, $status, $category, $frequency, $subCategory, $userName, $companyName);
    if (mysqli_stmt_num_rows($stmt) > 0) {
        while (mysqli_stmt_fetch($stmt)) {

            $return .= "<tr class='action' data-href='ticket_detail.php?id={$issueId}'>
                    <td>{$issueId}</td>
                    <td>{$userName}</td>
                    <td>{$createdAt}</td>
                    <td>{$title}</td>";
            ($companyId == NULL) ? ""  : $return .= "<td>{$companyName}</td> <td>{$frequency}</td> ";

            $return .= "
                    <td>" . priorityCheck($status) . "</td>
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

function makeUserFolder($userId)
{
    $directory = "../assets/img/pfpic/" . $userId;
    if (!file_exists($directory)) {
        mkdir($directory, 0777);
    }
    return true;
}

function checkFileExist($directory, $fileName)
{
    return file_exists($directory . $fileName);
}

function uploadFile($db, $file, $tableName, $valueRow, $relationId, $userId, $directory)
{
    $query = "UPDATE ";
    switch ($tableName) {
        case 'user':
            $query.= "user" ;
            break;
        
        default:
            # code...
            break;
    }
    switch ($valueRow) {
        case 'value':
            # code...
            break;
        
        default:
            # code...
            break;
    }
    $stmt = mysqli_prepare($db, $query) or die(mysqli_error($db));
    mysqli_stmt_bind_param($stmt, "si", $ , $userId) or die(mysqli_error($db));
}
