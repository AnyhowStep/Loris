<?php
    namespace API;
    
    //I really think this route should be more like /sessions/candidates/{candidate_id}/visit_labels/{visit_label}
    //I think that route, while longer, explains what it returns more clearly.
    //However, I'll follow the spec. -anyhowstep
    App::$Instance->get("/candidates/{candidate_id:\d+}/{visit_label}", function ($request, $response, $args) {
        $candidate_id = $args["candidate_id"];
        $visit_label  = $args["visit_label"];
        
        $candidate = Candidate::Fetch($candidate_id);
        if (is_null($candidate)) {
            return $response->withStatus(404);
        }
        
        $session_id = Session::GetIDFromCandidateVisitLabel($candidate_id, $visit_label);
        if (is_null($session_id)) {
            return $response->withStatus(404);
        }
        $session = Session::Fetch($session_id);
        
        $timepoint_info = $session->_timePointInfo;
        
        $meta = array(
            "CandID" =>$candidate_id,
            //Shouldn't this be "Visit_label" to fit in with the database naming? Or, at least have the suffix "label" somewhere?
            //-anyhowstep
            "Visit"  =>$visit_label,
            "Battery"=>$timepoint_info["SubprojectTitle"]
        );
        $stages = array();
        if (!is_null($timepoint_info["Date_screening"])) {
            $stages["Screening"] = array(
                "Date"  =>$timepoint_info["Date_screening"],
                "Status"=>$timepoint_info["Screening"]
            );
        }
        if (!is_null($timepoint_info["Date_visit"])) {
            $stages["Visit"] = array(
                "Date"  =>$timepoint_info["Date_visit"],
                "Status"=>$timepoint_info["Visit"]
            );
        }
        if (!is_null($timepoint_info["Date_approval"])) {
            $stages["Approval"] = array(
                "Date"  =>$timepoint_info["Date_approval"],
                "Status"=>$timepoint_info["Approval"]
            );
        }
        
        
        return $response->withJson(array(
            "Meta"  =>(object)$meta,
            "Stages"=>(object)$stages
        ));
    })->add(RequiredAuth::Create());
?>