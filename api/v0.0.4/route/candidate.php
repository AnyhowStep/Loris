<?php
    namespace API;
    App::$Instance->get("/candidates", function ($request, $response, $args) {
        return $response->withJson(
            Candidate::FetchAll()
        );
    })->add(RequiredAuth::Create());
    App::$Instance->get("/candidates/{id:\d+}", function ($request, $response, $args) {
        $id = $args["id"];
        $item = Candidate::Fetch($id);
        if (is_null($item)) {
            return $response->withStatus(404);
        }
        $visit_label_arr_raw = Session::FetchAllVisitLabel($id);
        $info = $item->candidateInfo;
        
        $visit_label_arr = array();
        foreach ($visit_label_arr_raw as $row) {
            $visit_label_arr[] = $row->Visit_label;
        }
        
        return $response->withJson(array(
            "Meta"=>array(
                "CandID" =>$id,
                "Project"=>$info["ProjectTitle"],
                "PSCID"  =>$info["PSCID"],
                "Site"   =>$info["Site"],
                "EDC"    =>$info["EDC"],
                "DoB"    =>$info["DoB"],
                "Gender" =>$info["Gender"]
            ),
            "Visits"=>$visit_label_arr
        ));
    })
        ->add(RequiredAuth::Create())
        ->add(new CheckETag(
            Candidate::class,
            "id"
        ));
?>