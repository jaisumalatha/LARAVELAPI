<?php

namespace App\Http\Controllers\Course;
use App\Http\Controllers\Controller;

use App\Models\VidhvaaNewAnnouncement;
use App\Models\JobNewsNewDetail;
use App\Models\ExamCategory;
use App\Models\Exam;

//Syllabus
use App\Models\MaterialPlan;
use App\Models\MaterialGeneralStudy;
use App\Models\MaterialLanguageStudy;
use App\Models\PreviousYearQuestion;
use App\Models\CurrentAffairMaterial;
use App\Models\MaterialModel;
use App\Models\MaterialModelQuestion;
use App\Models\MaterialLanguage;

use App\Models\Subject;
use App\Models\Chapter;

use App\Models\MaterialModelResult;
use App\Models\MaterialLanguageQuestion;
use App\Models\CurrentAffairQuestion;

//BookBack Question
use App\Models\BookBackQuestion;
use App\Models\MaterialGeneralQuestion;

use App\Libraries\checkValidSubscriptin;

use App\Libraries\GetWebSession;

//use Illuminate\Support\Facades\Redis;
//use Illuminate\Support\Facades\Cache;

use Illuminate\Http\Request;
use App\Models\UserDetail;

use DB;

class TnpscController extends Controller
{


    /*** TNPSC Course page function **/


    public function vidhvaatnpsc(Request $request) {

        $ob = GetWebSession::getUserSessionDetails($request);

        $ct_code = "-";
        $ls_code = "-";
        $ln_code = "LN02";

        $d1 = ExamCategory::select('ct_code')->where('ct_name', 'TNPSC')->first();
        if( !empty($d1) ) {
            $ct_code = $d1->ct_code;
        }

        $d2 = MaterialLanguage::select('ls_code')->where('ct_code', $ct_code)->first();
        if( !empty($d2) ) {
            $ls_code = $d2->ls_code;
        }

        $subscription = checkValidSubscriptin::checkCurrentSubscriptin('week', $ob->userCode, 'LiveExam');

        if( $subscription == 2 ) {
            $results = UserDetail::where('user_code', '=', $ob->userCode)->first();
            $results->login_status = 0;
            if($results->save()) {
                session()->flush();
                return redirect('vidhvaaloginpage');
            }
        }

        $announce['announce']              = VidhvaaNewAnnouncement::where('ct_code', $ct_code)->where('ex_code','!=', 'GENERAL')->where('ann_status', '1')->get();    
        $announce['jobnews']               = JobNewsNewDetail::where('ct_code', $ct_code)->where('ex_code','!=', 'GENERAL')->where('job_status', '1')->get();  
        $announce['currentaffairmaterial'] = CurrentAffairMaterial::select('ca_code','ca_year','ca_month')->where('ln_code', '=','LN02')->get();
        $announce['previousyearquestion']  = PreviousYearQuestion::where('ln_code', '=','LN02')->get();
        $announce['materialmodelsgroup4']  = MaterialModel::where('ex_type', '=','EXCT01')->where('ex_code', '=','EX04')->get();
        $announce['generalPractice']       = DB::table('subjects')->join('material_general_details', 'material_general_details.sb_code', '=', 'subjects.sb_code')->where('material_general_details.gs_status', '1')->get();
        $announce['materiallanguagestudy'] = DB::select("select sum(id) as idc,ls_code,ls_part,ln_code FROM material_language_studies WHERE ls_code = '".$ls_code."' and ln_code = '".$ln_code."' group by ls_part");
        $announce['subscription']          = $subscription;
        $announce['sessions']              = $ob;

        return view('designs/designlayout/vidhvaatnpsc',$announce);
    }

    public function getTNPSCPdf(Request $request)
    {
        $ob = GetWebSession::getUserSessionDetails($request);

        $ct_name     = trim($request->ct_name);
        $ex_name     = trim($request->ex_name);
        $ex_language = trim($request->ex_language);
        $ct_code     = "-";
        $ex_code     = "-";

        if($ex_language == "english") {
            $ex_language = "LN02";
        }
        else {
            $ex_language = "LN01";
        }

        $d1 = ExamCategory::select('ct_code')->where('ct_name', $ct_name)->first();
        if( !empty($d1) ) {
            $ct_code = $d1->ct_code;
        }

        $d2 = Exam::where('ct_code', $ct_code)->get();
        foreach($d2 as $d)
        {
            if($d->ex_name == $ex_name) {
                $ex_code = $d->ex_code;
                break;
            }
        }

        $file_name = "-";
        $d3 = MaterialPlan::select('sy_name')->where('ct_code', $ct_code)->where('ex_code', $ex_code)->where('ln_code', $ex_language)->where('sy_status', '1')->get();
        if($d3[0]->count() > 0) {
            $file_name = $d3[0]->sy_name;
        }

        if($file_name != "-")
        {
            $file = public_path('material/syllabus/') . $file_name;
     
            if (file_exists($file)) {
     
                $headers = [
                    'Content-Type' => 'application/pdf'
                ];
     
                return response()->download($file, 'PDF File', $headers, 'inline');
            } 
            else 
            {
                $file = storage_path('app/material/general_studies/GS1097_SAMPLE_FILE.pdf');

                $headers = [
                    'Content-Type' => 'application/pdf'
                ];

                return response()->download($file, 'PDF File', $headers, 'inline');
               // abort(404, 'File not found!');
            }
        }
        
    }

    public function bookbackpdfview(Request $request) {

        $ob = GetWebSession::getUserSessionDetails($request);

        $sb_code      = trim($request->sb_code);
        $position     = trim($request->position);
        $subscription = checkValidSubscriptin::checkCurrentSubscriptin('week', $ob->userCode, 'LiveExam');

        if( $subscription == 2 ) {
            $results = UserDetail::where('user_code', '=', $ob->userCode)->first();
            $results->login_status = 0;
            if($results->save()) {
                session()->flush();
                return redirect('vidhvaaloginpage');
            }
        }

        if( $subscription == 1 ) {
                return view('designs/course/tnpsc/bookbackpdfview', ['sessions' => $ob,'sb_code' => $sb_code,'position' => $position, 'subscription' => $subscription]);
        }
        else {
            if( $position == 1 ) {
                return view('designs/course/tnpsc/bookbackpdfview', ['sessions' => $ob,'sb_code' => $sb_code,'position' => $position, 'subscription' => $subscription]);
            }
            else {
                return redirect('vidhvaalogin2');
            }
        }
    }

    public function getBookBackPdf(Request $request)
    {
        $ob = GetWebSession::getUserSessionDetails($request);
        //Free content
        $ct_name = trim($request->ct_name);
        $ex_name = trim($request->ex_name);
        $st_code = trim($request->st_code);
        $sb_code = trim($request->sb_code);
        $ln_code = trim($request->ln_code);

        $ct_code   = "-";
        $ex_code   = "-";
        $file_name = "-";

        $d1 = ExamCategory::where('ct_name', $ct_name)->first();
        
        if( !empty($d1) ) {
            $ct_code = $d1->ct_code;
        }

        
        $d2 = Exam::where('ct_code', $ct_code)->get();
        foreach($d2 as $d)
        {
            if($d->ex_name == $ex_name) {
                $ex_code = $d->ex_code;
                break;
            }
        }

        
        $d3 =  BookBackQuestion::where('ct_code', $ct_code)->where('ex_code', $ex_code)
                                        ->where('sb_code', $sb_code)
                                        ->where('st_code', $st_code)
                                        ->where('ln_code', $ln_code)->where('bk_status', '1')->first();
        if( !empty($d3) ) {
            $file_name = $d3->bk_name;
        }

       if($file_name != "-") {

           $file = storage_path('app/material/book_back/') . $file_name;
     
            if (file_exists($file)) {
     
                $headers = [
                    'Content-Type' => 'application/pdf'
                ];

                return response()->download($file, 'PDF File', $headers, 'inline');

            } 
            else 
            {

                $file = storage_path('app/material/general_studies/GS1097_SAMPLE_FILE.pdf');

                $headers = [
                    'Content-Type' => 'application/pdf'
                ];

                return response()->download($file, 'PDF File', $headers, 'inline');

               // abort(404, 'File not found!');
            }
        }  
        
    }

    public function languagemetirialviewpdf(Request $request) {

        $ob = GetWebSession::getUserSessionDetails($request);

        $id = trim($request->id);

        $subscription = checkValidSubscriptin::checkCurrentSubscriptin('week', $ob->userCode, 'LiveExam');

        if( $subscription == 2 ) {

            $results = UserDetail::where('user_code', '=', $ob->userCode)->first();
            $results->login_status = 0;
            if($results->save()) {
                session()->flush();
                return redirect('vidhvaaloginpage');
            }

        }
        else {

            $ls_code = trim($request->ls_code);
            $ln_code = trim($request->ln_code);
            $ls_part = trim($request->ls_part);

            if($ln_code == "LN01") {
                $languagefield = "ch_name_tamil";
            }
            else {
                $languagefield = "ch_name_english";
            }

            $languagemetirials = DB::select("select a.id, b.".$languagefield." as languagename FROM material_language_studies as a inner join chapters as b on b.ch_code = a.ch_code WHERE a.ls_code = '".$ls_code."' and a.ln_code = '".$ln_code."' and ls_status = 1 and ls_part = '" . $ls_part . "'");
            return view('designs/course/tnpsc/languagemetirialview', ['sessions' => $ob, 'id' => $id,'subscription'=>$subscription, 'languagemetirials' => $languagemetirials ]);
        }

    }

    public function languagemetirialviewpdfview(Request $request)
    {
 
        $ob = GetWebSession::getUserSessionDetails($request);

        $id = trim($request->id);
    
        $d3 =  MaterialLanguageStudy::where('id', $id)->first();

        if( !empty($d3) ) {

            if($ob->userType != "GUEST") {
                $file_name = $d3->ls_name;
            }
            else {
                $file_name = $d3->ls_name_sample;
            }

        }

        if($file_name != "-") {

            $file = storage_path('app/material/language_material/') . $file_name;
            if (file_exists($file)) {
        
                $headers = [
                    'Content-Type' => 'application/pdf'
                ];

                return response()->download($file, 'PDF File', $headers, 'inline');

            } 
            else 
            {

                $file = storage_path('app/material/general_studies/GS1097_SAMPLE_FILE.pdf');

                $headers = [
                    'Content-Type' => 'application/pdf'
                ];

                return response()->download($file, 'PDF File', $headers, 'inline');
               // abort(404, 'File not found!');

            }
        }  
        
    }

    public function previousyearquestionviewpdf(Request $request)
    {
       //Free content

        $ob = GetWebSession::getUserSessionDetails($request);

        $ln_code   = trim($request->ln_code);
        $pr_status = trim($request->pr_status);
        $pr_year   = trim($request->pr_year); 
        $pr_id     = trim($request->pr_id);

   
        $d3 =  PreviousYearQuestion::where('ln_code', $ln_code)->where('pr_year', $pr_year)->where('pr_status', $pr_status)->first();


        if( !empty($d3) ) {

            if($pr_id == 1) {
                $file_name = $d3->pr_name;
            }
            else {
                $file_name = $d3->pr_name_sample;
            }

        }

        if($file_name != "-") {

           $file = storage_path('app/material/previous_year/') . $file_name;
           if (file_exists($file)) {
    
               $headers = [
                   'Content-Type' => 'application/pdf'
               ];

               return response()->download($file, 'PDF File', $headers, 'inline');

            } 
            else 
            {

                $file = storage_path('app/material/general_studies/GS1097_SAMPLE_FILE.pdf');

                $headers = [
                    'Content-Type' => 'application/pdf'
                ];

                return response()->download($file, 'PDF File', $headers, 'inline');

               //abort(404, 'File not found!');
            }
        }  
        
    }

    public function getGeneralStudiesPdf(Request $request)
    {
       //Free content
      $ct_name = trim($request->ct_name);
      $ex_name = trim($request->ex_name);
      $ch_code = trim($request->ch_code);
      $sb_code = trim($request->sb_code);
      $ln_code = trim($request->ln_code);

      $ct_code = "-";
      $ex_code = "-";
      $file_name = "-";

       $ob = GetWebSession::getUserSessionDetails($request);

       $d1 = ExamCategory::where('ct_name', $ct_name)->first();
       if( !empty($d1) ) {
            $ct_code = $d1->ct_code;
       }

       $d2 = Exam::where('ct_code', $ct_code)->get();
       foreach($d2 as $d)
       {
           if($d->ex_name == $ex_name) {
               $ex_code = $d->ex_code;
               break;
           }
       }

       
       $d3 =  MaterialGeneralStudy::where('ct_code', $ct_code)->where('ex_code', $ex_code)
                                       ->where('sb_code', $sb_code)
                                       ->where('ch_code', $ch_code)
                                       ->where('ln_code', $ln_code)->where('gs_status', '1')->first();
        if( !empty($d3) ) {
            $file_name = $d3->gs_name;
        }                               

        if($file_name != "-") {

            $file = storage_path('app/material/general_studies/') . $file_name;
            if (file_exists($file)) {
        
                $headers = [
                    'Content-Type' => 'application/pdf'
                ];

                return response()->download($file, 'PDF File', $headers, 'inline');

            } 
            else 
            {

                $file = storage_path('app/material/general_studies/GS1097_SAMPLE_FILE.pdf');

                $headers = [
                    'Content-Type' => 'application/pdf'
                ];

                return response()->download($file, 'PDF File', $headers, 'inline');

               // abort(404, 'File not found!');
            }
        }  
        
    }

    public function languagemetirialviewaudioview(Request $request)
    {

        $ob = GetWebSession::getUserSessionDetails($request);
    
        $id    = trim($request->id);
        $d3    =  MaterialLanguageStudy::where('id', $id)->get();
        $data  = array();
        $data2 = json_decode($d3[0]->ls_audio_file, true);

        if( $data2 != "" ) {

            if( count($data2) == 0) {

                $data = array();

                for( $i=0;$i<count($data2);$i++ ) {

                    $ls_audios = explode(".",$data2[$i+1]);
                    $data1 = array(
                        "track"   => ($i+1),
                        "name"  =>  $data2[$i+1],
                        "length"  => "1:00",
                        "file" => $ls_audios[0],           
                    );      
                    array_push($data, $data1);

                }

                $jsondata               = json_encode($data);
                $materials['materials'] = $jsondata;
                $materials['folder']    = 'language_material';
                $materials['sessions']  = $ob;

                return view('designs/course/tnpsc/generalStudiesAudio', $materials);  

            }
            else {
                return redirect('upcoming');
            }

        }
        else {
            return redirect('upcoming');
        }
    }

    public function getGeneralStudiesAudio(Request $request)
    {

        $ob = GetWebSession::getUserSessionDetails($request);

        $ct_name = trim($request->ct_name);
        $ex_name = trim($request->ex_name);
        $ch_code = trim($request->ch_code);
        $sb_code = trim($request->sb_code);
        $ln_code = trim($request->ln_code);

        $ct_code   = "-";
        $ex_code   = "-";
        $file_name = "-";
  
         $d1 = ExamCategory::where('ct_name', $ct_name)->first();
  
         
         if( !empty($d1) ) {
             $ct_code = $d1->ct_code;
         }
  
         
         $d2 = Exam::where('ct_code', $ct_code)->get();
         foreach($d2 as $d)
         {
             if($d->ex_name == $ex_name) {
                 $ex_code = $d->ex_code;
                 break;
             }
         }
  
         
         $materials =  MaterialGeneralStudy::where('ct_code', $ct_code)->where('ex_code', $ex_code)
                                         ->where('sb_code', $sb_code)
                                         ->where('ch_code', $ch_code)
                                         ->where('ln_code', $ln_code)->where('gs_status', '1')->get();
  
          $str  = "";
          $j    = 0;
          $data = array();
          $sort = array();

          foreach($materials as $material)
          {

              $ls_audio = (!empty($material->gs_audio_file_name))?$material->gs_audio_file_name:'';
              if($ls_audio != "") {

                $i=0;
                $ls_audio = explode(',', $ls_audio);

                foreach($ls_audio as $ls_audios) {

                    $ls_audios = explode(".",$ls_audios);
                    $data1 = array(
                        "track"   => ($i+1),
                        "name"  =>  $ls_audios,
                        "length"  => "1:00",
                        "file" => $ls_audios[0],           
                    );      
                        $i = $i + 1; 

                    array_push($data, $data1);

                }

                $jsondata               = json_encode($data);
                $materials['materials'] = $jsondata;
                $materials['folder']    = 'general_studies';
                $materials['sessions']  = $ob;

                return view('designs/course/tnpsc/generalStudiesAudio',$materials);

              }
              else {
                return redirect('upcoming');
              }   

          } 
    }

    public function getGeneralStudiesAudioStream(Request $request)
    {

        $ob = GetWebSession::getUserSessionDetails($request);

        $ct_name = trim($request->ct_name);
        $ex_name = trim($request->ex_name);
        $ch_code = trim($request->ch_code);
        $sb_code = trim($request->sb_code);
        $ln_code = trim($request->ln_code);
  
         $ct_code   = "-";
         $ex_code   = "-";
         $file_name = "-";
         $str       = "";

         $d1 = ExamCategory::where('ct_name', $ct_name)->first();
         if( !empty($d1) ) {
             $ct_code = $d1->ct_code;
         }

         $d2 = Exam::where('ct_code', $ct_code)->get();
         foreach($d2 as $d)
         {
             if($d->ex_name == $ex_name) {
                 $ex_code = $d->ex_code;
                 break;
             }
         }
         
         $materials =  MaterialGeneralStudy::where('ct_code', $ct_code)->where('ex_code', $ex_code)
                                         ->where('sb_code', $sb_code)
                                         ->where('ch_code', $ch_code)
                                         ->where('ln_code', $ln_code)->where('gs_status', '1')->get();
          $j    = 0;
          $data = array();
          $sort = array();

          foreach($materials as $material)
          {

            $ls_audio = (!empty($material->gs_audio_file_name))?$material->gs_audio_file_name:'';
            $i=0;
            $ls_audio = explode(',', $ls_audio);

            foreach($ls_audio as $ls_audios) {

                $data1 = array(
                    "track"   => ($i+1),
                    "name"  =>  $ls_audios,
                    "length"  => "1:00",
                    "file" => $ls_audios,           
                );    

                $i = $i + 1; 
                array_push($data, $data1);

              }              
          }

          $jsondata               = json_encode($data);
          $materials['materials'] = $jsondata;
          echo $materials['materials'];
    }


    public function getVidhvaaCurrentAffairsPdf(Request $request)
    {

        $ob = GetWebSession::getUserSessionDetails($request);

        $ca_code = trim($request->ca_code);
        $ln_code = trim($request->ln_code);

        $file_name = "-";

        $d3 =  CurrentAffairMaterial::where('ca_code', $ca_code)->where('ln_code', $ln_code)->where('ca_mt_status', '1')->first();

       
        if(!empty($d3)) {

            if($ob->userType != "GUEST") {
                $file_name = $d3->ca_mt_name;
            }
            else {
                $file_name = $d3->ca_mt_name_status;
            }
            
        }

        if($file_name != "-") {

            $file = storage_path('app/material/current_affairs/') . $file_name;
            if (file_exists($file)) {
        
                $headers = [
                    'Content-Type' => 'application/pdf'
                ];

                return response()->download($file, 'PDF File', $headers, 'inline');

            } 
            else 
            {

                $file = storage_path('app/material/general_studies/GS1097_SAMPLE_FILE.pdf');

                $headers = [
                    'Content-Type' => 'application/pdf'
                ];

                return response()->download($file, 'PDF File', $headers, 'inline');

                //abort(404, 'File not found!');

            }
        }  
        
    }

    /*** TNPSC course tamil page function **/
    public function vidhvaatnpsctamil(Request $request) {

        $ob = GetWebSession::getUserSessionDetails($request);

        $ct_code = "-";
        $ln_code = "LN01";
        $ls_code = "-";

        $d1 = ExamCategory::where('ct_name', 'TNPSC')->first();
        if( !empty($d1) ) {
            $ct_code = $d1->ct_code;
        }

        $d2 = MaterialLanguage::select('ls_code')->where('ct_code', $ct_code)->first();
        if( !empty($d2) ) {
            $ls_code = $d2->ls_code;
        }

        $announce['announce']              = VidhvaaNewAnnouncement::where('ct_code', $ct_code)->where('ex_code','!=', 'GENERAL')->where('ann_status', '1')->get();    
        $announce['jobnews']               = JobNewsNewDetail::where('ct_code', $ct_code)->where('ex_code','!=', 'GENERAL')->where('job_status', '1')->get();  
        $announce['currentaffairmaterial'] = CurrentAffairMaterial::select('ca_code','ca_year','ca_month')->where('ln_code', '=','LN01')->get();
        $announce['previousyearquestion']  = PreviousYearQuestion::where('ln_code', '=','LN01')->get();
        $announce['materialmodelsgroup4']  = MaterialModel::where('ex_type', '=','EXCT01')->where('ex_code', '=','EX04')->get();

        $announce['generalPractice']       = DB::table('subjects')->join('material_general_details', 'material_general_details.sb_code', '=', 'subjects.sb_code')->where('material_general_details.gs_status', '1')->get();
        $announce['materiallanguagestudy'] = DB::select("select sum(id) as idc,ls_code,ls_part,ln_code FROM material_language_studies WHERE ls_code = '".$ls_code."' and ln_code = '".$ln_code."' group by ls_part");
        $announce['sessions']              = $ob;

        
        return view('designs/course/tnpsc/vidhvaatnpsctamil',$announce);

    }

    public function studiesmetirials(Request $request) {


        $ob = GetWebSession::getUserSessionDetails($request);
       
        $subject = trim($request->subject);

        $subscription = checkValidSubscriptin::checkCurrentSubscriptin('week', $ob->userCode, 'LiveExam');

        if( $subscription == 2 ) {
            $results = UserDetail::where('user_code', '=', $ob->userCode)->first();
            $results->login_status = 0;
            if($results->save()) {
                session()->flush();
                return redirect('vidhvaaloginpage');
            }
        }
     
        $sb_code = "-";

        $d1 = Subject::where('sb_name', $subject)->first();
        if( !empty($d1) ) {
            $sb_code = $d1->sb_code;
        }
     
        $d2['chapter']      = Chapter::where('sb_code', $sb_code)->where('ch_status', '=', 1)->get();
        $d2['subscription'] = $subscription;
        $d2['sessions']     = $ob;
    
        return view('designs/course/tnpsc/studiesmetirials',$d2);

    }

    public function studiesmetirialview(Request $request) {
        
        $ob = GetWebSession::getUserSessionDetails($request);
         
        $subject  = trim($request->subject);
        $sb_code  = trim($request->sb_code);
        $ch_code  = trim($request->ch_code);
        $position = trim($request->position);

        $d1 = Subject::where('sb_name', $subject)->get();

        $subscription = checkValidSubscriptin::checkCurrentSubscriptin('week', $ob->userCode, 'LiveExam');

        if( $subscription == 2 ) {
            $results = UserDetail::where('user_code', '=', $ob->userCode)->first();
            $results->login_status = 0;
            if($results->save()) {
                session()->flush();
                return redirect('vidhvaaloginpage');
            }
        }
     
        $d2['chapter']      = Chapter::where('sb_code', $sb_code)->where('ch_status', '=', 1)->get();

        $d2['ch_code']      = $ch_code;
        $d2['sb_code']      = $sb_code;
        $d2['position']     = $position;
        $d2['subscription'] = $subscription;
        $d2['sessions']     = $ob;

        return view('designs/course/tnpsc/studiesmetirialview', $d2);

    }

    public function geographymetirial(Request $request) {

        $ob = GetWebSession::getUserSessionDetails($request);

        if($ob->userName) {
            return view('designs/course/tnpsc/geographymetirialview');
        }
        else {
            return redirect()->back();
        }
    }
    public function previousyearquestionview(Request $request) {

        $ob = GetWebSession::getUserSessionDetails($request);

        $sessions['sessions'] = $ob;

        if($ob->userName) {

            $subscription = checkValidSubscriptin::checkCurrentSubscriptin('week', $ob->userCode, 'LiveExam');

            if( $subscription == 2 ) {

                $results = UserDetail::where('user_code', '=', $ob->userCode)->first();
                $results->login_status = 0;
                if($results->save()) {
                    session()->flush();
                    return redirect('vidhvaaloginpage');
                }

            }

            if( $subscription == 1 ) {
                return view('designs/course/tnpsc/previousyearquestionview',$sessions);
            }
            else {
                return redirect('vidhvaalogin2');
            }

        }
        else {
            return redirect('vidhvaalogin2');
        }
    }
    public function getVidhvaaCurrentAffairs(Request $request) {

        $ob = GetWebSession::getUserSessionDetails($request);

        $sessions['sessions'] = $ob;

        if($ob->userName) {
            $subscription = checkValidSubscriptin::checkCurrentSubscriptin('week', $ob->userCode, 'LiveExam');

            if( $subscription == 2 ) {

                $results = UserDetail::where('user_code', '=', $ob->userCode)->first();
                $results->login_status = 0;
                if($results->save()) {
                    session()->flush();
                    return redirect('vidhvaaloginpage');
                }

            }

            if( $subscription == 1 ) {
                return view('designs/course/tnpsc/currentaffairmatirial',$sessions);
            }
            else {
                return redirect('vidhvaalogin2');
            }
        }
        else {
            return redirect()->back();
        }
    }


    public function modelquestionsenglish(Request $request) {

        $ob = GetWebSession::getUserSessionDetails($request);
             
        $mm_code = trim($request->mm_code);
        $ln_code = trim($request->ln_code);

        $materialmodel['materialmodel'] = MaterialModelQuestion::where('mm_code','=', $mm_code)->where('ln_code','=', $ln_code)->get();
        $materialmodel['ct_codecount']  = DB::select('select sum(mm_seconds) as ct_codecount from material_model_questions where mm_code = ? and ln_code = ?',[$mm_code,$ln_code]);
        $materialmodel['sessions']      = $ob;

        return view('designs/course/tnpsc/modelquestionsenglish', $materialmodel);

    }  

    public function materialmodelresults(Request $request) {

        $ob = GetWebSession::getUserSessionDetails($request);

        $mm_code        = trim($request->mm_code);
        $ln_code        = trim($request->ln_code);
        $questionno     = trim($request->questionno);
        $answer         = trim($request->answer);
        $correctanswer  = trim($request->correctanswer);
        $quesid         = trim($request->quesid);
        $totalquestions = trim($request->totalquestions);
        $resultid       = trim($request->resultid);

        if( $resultid == 0 ) {

            $mresult = new MaterialModelResult;
            $mresult->trans_id       = rand(10000,99999);
            $mresult->user_code      = $ob->userCode;
            $mresult->mm_code        = $mm_code;
            $mresult->ln_code        = $ln_code;
            $mresult->correct_answer = 0;
            $mresult->wrong_answer   = 0;
            $mresult->not_answer     = $totalquestions;
            $mresult->all_answer     = "_";
            $mresult->save();
            return $mresult->id;

        }
        else {
            $mresult = MaterialModelResult::where('id','=', $resultid)->first();
            if( $answer == $correctanswer ) {

                $mresult->not_answer = $mresult->not_answer-1;
                $mresult->correct_answer = $mresult->correct_answer+1;
                $mresult->save();
                return $resultid;

            }
            else if( $answer != $correctanswer ) {

                $mresult->not_answer = $mresult->not_answer-1;;
                $mresult->wrong_answer = $mresult->wrong_answer+1;
                $mresult->save();
                return $resultid;

            }  
        }  
    }

    public function materialmodelexamresults(Request $request) {

        $ob = GetWebSession::getUserSessionDetails($request);

        $resultid = trim($request->resultid);
        $alldata  = json_encode($request->alldata);

        $mresult = MaterialModelResult::where('id','=', $resultid)->first();

        $mresult->all_answer = base64_encode ( serialize($alldata) );


        if($mresult->save()) {
            $mresult2['sessions'] = $ob;
            $mresult2['daily_task_result'] = MaterialModelResult::where('id','=', $resultid)->first();
            return view('designs/course/tnpsc/modelanswerscreen', $mresult2);
        }

    }

    public function generalstudiesquestion(Request $request) {

        $ob = GetWebSession::getUserSessionDetails($request);

        $ct_code = trim($request->ct_code);
        $ex_code = trim($request->ex_code);
        $sb_code = trim($request->sb_code);
        $ln_code = trim($request->ln_code);
        $gr_code = trim($request->gr_code); 

        if( $sb_code == "SB08" || $sb_code == "SB09" ) {
            $materialgeneralquestion['practicequestion'] = MaterialLanguageQuestion::select('ls_question as question','ls_ans_1 as ans1','ls_ans_2 as ans2','ls_ans_3 as ans3','ls_ans_4 as ans4','ls_correct_ans as correct_ans')->where([['sb_code','=', $sb_code],['ln_code','=', $ln_code]])->get();
        }
        else if( $sb_code == "ca" ) {
            $materialgeneralquestion['practicequestion'] = CurrentAffairQuestion::select('ca_question as question','ca_ans_1 as ans1','ca_ans_2 as ans2','ca_ans_3 as ans3','ca_ans_4 as ans4','ca_correct_ans as correct_ans')->where([['ca_code','=', $ct_code],['ln_code','=', $ln_code]])->get();
        }
        else {
            $materialgeneralquestion['practicequestion'] = MaterialGeneralQuestion::select('gs_question as question','gs_ans_1 as ans1','gs_ans_2 as ans2','gs_ans_3 as ans3','gs_ans_4 as ans4','gs_correct_ans as correct_ans')->where([['ct_code','=', $ct_code],['ex_code','=', $ex_code],['sb_code','=', $sb_code],['ln_code','=', $ln_code],['gr_code','=', $gr_code]])->get();
        }
        $materialgeneralquestion['sessions'] = $ob;
        return view('designs/course/tnpsc/generalstudiesquestion', $materialgeneralquestion);
    
    }

    public function generalstudiesquestionresult(Request $request) {
        //practicequestionresult

        $ob = GetWebSession::getUserSessionDetails($request);

        $questionresult['correct_answer'] = trim($request->correct_answer);
        $questionresult['wrong_answer']   = trim($request->wrong_answer);
        $questionresult['not_answer']     = trim($request->not_answer);
        $questionresult['sessions']       = $ob;

        return view('designs/course/tnpsc/practicequestionresult', $questionresult);
    }

    public function getReferenceBookes(Request $request) {

        $ob = GetWebSession::getUserSessionDetails($request);

        $ln_code = trim($request->ln_code);
        
        if( $ln_code == "LN01" ) {
            $file_name = "REF001_TAMIL.pdf";
        }

        if( $ln_code == "LN02" ) {
            $file_name = "REF002_ENGLISH.pdf";
        }

        if($file_name != "-") {

            $file = storage_path('app/material/reference_material/') . $file_name;
             if (file_exists($file)) {
      
                 $headers = [
                     'Content-Type' => 'application/pdf'
                 ];
  
                 return response()->download($file, 'PDF File', $headers, 'inline');
  
             } 
             else 
             {

                $file = storage_path('app/material/general_studies/GS1097_SAMPLE_FILE.pdf');

                $headers = [
                    'Content-Type' => 'application/pdf'
                ];

                return response()->download($file, 'PDF File', $headers, 'inline');

               //  abort(404, 'File not found!');
             }
         }

    }

    public function languagemetirial(Request $request) {

        $ob = GetWebSession::getUserSessionDetails($request);

        $ls_code = trim($request->ls_code);
        $ln_code = trim($request->ln_code);
        $ls_part = trim($request->ls_part);

        if($ln_code == "LN01") {
            $languagefield = "ch_name_tamil";
        }
        else {
            $languagefield = "ch_name_english";
        }

        $subscription = checkValidSubscriptin::checkCurrentSubscriptin('week', $ob->userCode, 'LiveExam');

        if( $subscription == 2 ) {
            $results = UserDetail::where('user_code', '=', $ob->userCode)->first();
            $results->login_status = 0;
            if($results->save()) {
                session()->flush();
                return redirect('vidhvaaloginpage');
            }
        }

        $MaterialLanguageStudy['MaterialLanguageStudy'] = DB::select("select a.id, b.".$languagefield." as languagename FROM material_language_studies as a inner join chapters as b on b.ch_code = a.ch_code WHERE a.ls_code = '".$ls_code."' and a.ln_code = '".$ln_code."' and ls_status = 1 and ls_part = '" . $ls_part . "'");
        $MaterialLanguageStudy['subscription']          = $subscription;
        $MaterialLanguageStudy['sessions']              = $ob;

        return view('designs/course/tnpsc/languagemetirial', $MaterialLanguageStudy);

    }

    


}