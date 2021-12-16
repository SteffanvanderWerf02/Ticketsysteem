<?php
function printArray($array)
{
    echo '<pre>';
    print_r($array);
    echo '</pre>';
}

function getIssueOverview($db, $companyId, $userId, $issueType, $filterStatus, $searchId, $searchTitle) {
    $type = "s";
    $params = array($issueType);

    $return = '
        <table cellspacing="0" cellpadding="0" class="table">
        <thead>
            <tr data-href="ticket_detail.php?id=1">
                <th>id</th>
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
    $query= "
        SELECT  issue.issue_id,
                issue.created_at,
                issue.title,
                issue.priority,
                issue.`status`,
                issue.category,
                issue.frequency,
                issue.sub_category, 
                company.name
        FROM    issue
        LEFT JOIN company 
        ON issue.company_id = company.company_id
        WHERE issue.category = ?
    ";
    // ($companyId == NULL) ? $query.=" AND issue.user_id = ? " : $query.=" AND issue.company_id = ?";  
    switch ($companyId) {
        case NULL:
            $query.=" AND issue.user_id = ? ";
            $type .= "i";
            array_push($params, $userId);
            break;
        default: 
            $query.=" AND issue.company_id = ?";
            $type .= "i";
            array_push($params, $companyId);
    }

    switch ($filterStatus) {
        case '1':
            $query.= " AND issue.status = 1";
            break;
        case '2':
            $query.= " AND issue.status = 2";
            break;
        case '3':
            $query.= " AND issue.status = 3";
            break;
        case '4':
            $query.= " AND issue.status = 4";
            break;
    }

    if (!empty($searchId) && empty($searchTitle)) {
        $query .= " AND issue.issue_id = ?";
        $type .= "i";
        array_push($params, $searchId);
    } elseif (!empty($searchTitle) && empty($searchId)) {
        $query .= " AND issue.title LIKE '?%'";
        $type .= "s";
        array_push($params, $searchTitle);
       
    } else {
        //return "you can't filter on both at the same time";
        $query .= " AND issue.title LIKE '?%'";
        $query .= " AND issue.issue_id = ?";
        $type .= "si";
        array_push($params, $searchTitle, $searchId);
    }

     $stmt = mysqli_prepare($db,$query);
        // return var_dump($params) . $query;
        // return $query;
        call_user_func_array(array($stmt, "bind_param"), makeValuesReferenced(array_merge(array($type), $params)));
        mysqli_stmt_execute($stmt) OR DIE(mysqli_error($db));
        mysqli_stmt_store_result($stmt) OR DIE(mysqli_error($db));
        mysqli_stmt_bind_result($stmt, $issueID, $createdAt, $title, $priority, $status, $category, $frequency, $subCategory, $companyName);
        if(mysqli_stmt_num_rows($stmt) > 0) {
            while(mysqli_stmt_fetch($stmt)) {
                    
                $return .= "<tr class='action' data-href='ticket_detail.php?id={$issueID}'>
                    <td>{$issueID}</td>
                    <td>{$createdAt}</td>
                    <td>{$title}</td>";
                    ($companyId == NULL) ? ""  : $return .= "<td>{$companyName}</td> <td>{$frequency}</td> ";
                    
                $return .= "
                    <td>{$priority}</td>
                    <td>{$status}</td>
                    <td>{$category}</td>
                    <td>{$subCategory}</td>
                </tr>";
            }
        } else {
            $return .= "<td colspan='8'>U heeft momenteel geen nieuwe dienst/service aanvragen.</td>";
        }                  
        $return .= "</tbody></table>";
        
        return $return;
    }
    //this function makes from merged to seperate and individual values for the bind param function
    function makeValuesReferenced($arr){
        $refs = array();
        foreach($arr as $key => $value)
            $refs[$key] = &$arr[$key];
        return $refs;
    
    }
?>