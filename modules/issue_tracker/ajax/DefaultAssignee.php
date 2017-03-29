<?php
    if ($_SERVER["REQUEST_METHOD"] == "GET") {
        $user =& User::singleton();
        if (!$user->hasPermission("issue_tracker_reporter")) {
            http_response_code(403);
            die(json_encode(array(
                "error"=>"You are not authorized to get a list of default assignees"
            )));
        }
        //Get default assignees user has permissions for
        $db =& Database::singleton();
        
        $default_assignee_arr = $db->pselect("
            SELECT
                ida.*,
                u.UserID AS username,
                icat.categoryName AS issue_category_name
            FROM
                issues_default_assignee ida
            JOIN
                (
                    SELECT
                        upr.CenterID
                    FROM
                        user_psc_rel upr
                    WHERE
                        upr.UserID = :user_id
                ) AS my_psc
            ON
                my_psc.CenterID = ida.center_id
            JOIN
                users u
            ON
                u.ID = ida.user_id
            JOIN
                issues_categories icat
            ON
                icat.categoryID = ida.issue_category_id
        ", array(
            "user_id"=>$user->getId()
        ));
        echo json_encode(array(
            "arr"=>$default_assignee_arr
        ));
    } else {
        http_response_code(405);
        die(json_encode(array(
            "error"=>"Method not allowed, Only GET allowed"
        )));
    }
?>