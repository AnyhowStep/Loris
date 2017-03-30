<?php
    class NDB_Form_issue_tracker_default_assignee extends NDB_Form {
        public function _hasAccess () {
            $user =& User::singleton();
            return $user->hasPermission("superuser");
        }
        public function issue_tracker_default_assignee () {
            $db = Database::singleton();
            
            //Why are our booleans "N/Y enums"? -anyhowstep
            $center_arr = $db->pselect("
                SELECT
                    CenterID AS id,
                    Name AS name
                FROM
                    psc
                WHERE
                    Study_site='Y'
                ORDER BY
                    name
            ");
            $this->tpl_data["center_arr"] = $center_arr;
            
            $this->tpl_data["category_arr"] = $db->pselect("
                SELECT
                    categoryID AS id,
                    categoryName AS name
                FROM
                    issues_categories
                ORDER BY
                    name
            ");
            $this->tpl_data["user_arr"] = $db->pselect("
                SELECT
                    u.ID AS id,
                    u.UserID AS username,
                    u.Real_name AS real_name
                FROM
                    users u
                ORDER BY
                    username
            ");
            
            $center_assignee_arr = array();
            
            foreach ($center_arr as $center) {
                $arr = $db->pselect("
                    SELECT
                        ida.*,
                        icat.categoryName AS issue_category_name,
                        u.UserID AS username,
                        u.Real_name AS real_name
                    FROM
                        issues_default_assignee ida
                    JOIN
                        issues_categories icat
                    ON
                        icat.categoryID = ida.issue_category_id
                    JOIN
                        users u
                    ON
                        u.ID = ida.user_id
                    WHERE
                        ida.center_id = :center_id
                    ORDER BY
                        issue_category_name ASC
                ", array(
                    "center_id"=>$center["id"]
                ));
                $center_assignee = array(
                    "name"=>$center["name"],
                    "id"=>$center["id"],
                    "arr"=>$arr
                );
                $center_assignee_arr[] = $center_assignee;
            }
            $this->tpl_data["center_assignee_arr"] = $center_assignee_arr;
            return true;
        }
        //I honestly don't like that we are POST'ing to DELETE something.
        //But, with the current tools we have, this is the best we can manage =/
        //We seriously need a proper router...........
        public function delete () {
            if ($_SERVER["REQUEST_METHOD"] !== "POST") {
                http_response_code(405);
                die();
            }
            if (!isset(
                $_POST["center_id"],
                $_POST["issue_category_id"],
                $_POST["user_id"]
            )) {
                http_response_code(400);
                die();
            }
            $user = User::singleton();
            if (!$user->hasPermission("superuser")) {
                http_response_code(401);
                die();
            }
            $center_id         = (int)$_POST["center_id"];
            $issue_category_id = (int)$_POST["issue_category_id"];
            $user_id           = (int)$_POST["user_id"];
            
            $db = Database::singleton();
            $stmt = $db->prepare("
                DELETE FROM
                    issues_default_assignee
                WHERE
                    center_id = :center_id AND
                    issue_category_id = :issue_category_id AND
                    user_id = :user_id
            ");
            $db->execute($stmt, array(
                "center_id"=>$center_id,
                "issue_category_id"=>$issue_category_id,
                "user_id"=>$user_id
            ));
            
            http_response_code(302);
            header("Location: /issue_tracker_default_assignee");
            die();
        }
        //I honestly don't like that we are POST'ing to PUT something.
        //But, with the current tools we have, this is the best we can manage =/
        //We seriously need a proper router...........
        public function replace () {
            if ($_SERVER["REQUEST_METHOD"] !== "POST") {
                http_response_code(405);
                die();
            }
            if (!isset(
                $_POST["center_id"],
                $_POST["issue_category_id"],
                $_POST["user_id"]
            )) {
                http_response_code(400);
                die();
            }
            $user = User::singleton();
            if (!$user->hasPermission("superuser")) {
                http_response_code(401);
                die();
            }
            $center_id         = (int)$_POST["center_id"];
            $issue_category_id = (int)$_POST["issue_category_id"];
            $user_id           = (int)$_POST["user_id"];
            
            $db = Database::singleton();
            $stmt = $db->prepare("
                REPLACE INTO
                    issues_default_assignee (center_id, issue_category_id, user_id)
                VALUES (
                    :center_id,
                    :issue_category_id,
                    :user_id
                )
            ");
            $db->execute($stmt, array(
                "center_id"=>$center_id,
                "issue_category_id"=>$issue_category_id,
                "user_id"=>$user_id
            ));
            
            http_response_code(302);
            header("Location: /issue_tracker_default_assignee");
            die();
        }
    }
?>