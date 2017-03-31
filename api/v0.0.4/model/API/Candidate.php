<?php
    namespace API;
    class Candidate {
        public static function FetchAll () {
            //Copy-pasting code from v0.0.3's impl
            $candidates = DB::$Instance->pselect("
                SELECT
                    CandID,
                    ProjectID,
                    PSCID,
                    s.Alias as Site,
                    EDC,
                    DoB,
                    Gender
                FROM
                    candidate c
                JOIN
                    psc s
                ON
                    s.CenterID = c.CenterID
                WHERE
                    Active='Y'
            ", []);
            $projects   = \Utility::getProjectList();
            $candValues = array_map(
                function ($row) use ($projects) {
                    $row['Project'] = isset($projects[$row['ProjectID']])
                        ? $projects[$row['ProjectID']]
                        : "loris";
                    unset($row['ProjectID']);
                    return $row;
                },
                $candidates
            );
            return $candValues;
        }
        public static function Fetch ($candidate_id) {
            //Copy-pasting code from v0.0.3's impl
            //I don't like the idea of throwing an exception here.
            //Models should return null if the item doesn't exist.
            //Candidate.class.inc is throwing an Exception.
            //Also, I don't like it that you have to call getCandidateSite() and getCandidateGender() to get that info separately.
            //IMO, the candidate should have that info *already*
            try {
                $item = \NDB_Factory::singleton()->Candidate($candidate_id);
                //This check is useful, in case the day comes where the above method call does not throw an exception
                if (is_null($item)) {
                    return null;
                }
                $site = $item->getCandidateSite();
                $item->candidateInfo["Site"] = $site;
                
                return $item;
            } catch (\Exception $ex) {
                return null;
            }
        }
        public static function CalculateETag ($candidate_id) {
            //Taken from v0.0.3's impl.
            //-anyhowstep
            $row = DB::$Instance->fetchOrNull("
                SELECT
                    MAX(c.Testdate) as CandChange,
                    MAX(s.Testdate) as VisitChange,
                    COUNT(s.Visit_label) as VisitCount
                FROM
                    candidate c
                JOIN
                    session s
                ON
                    s.CandID = c.CandID
                WHERE
                    c.CandID=:candidate_id
            ",array(
                "candidate_id" => $candidate_id
            ));
            if (is_null($row)) {
                return null;
            }
            return md5(
                'Candidate:' . $candidate_id . ':'
                . $row['CandChange'] . ':'
                . $row['VisitChange'] . ':'
                . $row['VisitCount']
            );
        }
    }
?>