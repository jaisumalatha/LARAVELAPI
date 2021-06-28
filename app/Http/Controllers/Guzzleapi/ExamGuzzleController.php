<?php

namespace App\Http\Controllers\Guzzleapi;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helper\Helper;


class ExamGuzzleController extends Controller
{

    
    public function getVidhvaaExamList(Request $request)
    {
        $authToken = 'cDdXWHd5bTR5cElaTlhKUFB6N045N0ZSY2VIZ1J060d22514bc61d';
        $headers = ['Content-Type' => 'application/json', 'Authorization' => 'Bearer ' . $authToken];
		$params['headers']       = $headers;
        $request =  Helper::GetApi('http://13.235.243.15/api/vidhvaa/exam/list',$params);
        echo $request;
    }

    public function getExamQuestionsNew(Request $request, $examType)
    {
        $authToken = $request->bearerToken();
        if(empty($authToken))
        {
            return response()->json(['status' => false, 'Message' => 'Authentication Failure'], 401);            
        }
        $userDetail = UserDetail::where('auth_token', $authToken)->first();
        if(empty($userDetail))
        {
            return response()->json(['status' => false, 'Message' => 'Authentication Failure'], 401);
        }
        if(empty($examType))
        {
            return response()->json(['status' => false, 'Message' => 'Data Empty'], 200);
        }
        $validator = Validator::make($request->all(), [
            'planCode'     => 'required',
            'examCode'     => 'required',
            'languageCode' => 'required'
        ]);
        if($validator->fails()){  
            $errors = $validator->errors();
            $message = '';
            foreach ($errors->all() as $error) {
               $message .= $error . ' ';
            }       
            $result         = ['status' => false, 'message' => $message];
            return response()->json($result, 200);
        }
        $planCode     = trim($request->planCode);
        $examCode     = trim($request->examCode);
        $languageCode = trim($request->languageCode);
        $data              = [];            
        $currentDate       = date("Y-m-d");;
        $currentTime       = "00:00:00";            
        $previousDate      = date('Y-m-d',strtotime("-1 days"));
        $previousTime      = "00:00:00";
        $currentDateStatus = false;
        $isTimerLoad       = false;
        $taskTime          = "";

        if($examType === 'online')
        {
            $planonlineexam = PlanOnlineExam::where('online_exam_code', $examCode)->where('plan_code', $planCode)->where('exam_date', $currentDate)->first();
            if(!empty($planonlineexam))
            {
                $onlineExam = $planonlineexam->onlineexam;
                if(!empty($onlineExam))
                {
                    $data['examCode']       = $onlineExam->on_code;
                    $data['examDate']       = $planonlineexam->exam_date;
                    $data['examTime']       = $planonlineexam->exam_time;
                    $data['taskTime']       = '';
                    $data['currentStatus']  = false;
                    $data['isTimerLoad']    = false;
                    $examDateTime           = $planonlineexam->exam_date . ' ' . $planonlineexam->exam_time;
                    $taskDateTime           = date("Y-m-d H:i:s", strtotime($currentDate .' '. $planonlineexam->exam_time));
                    $currentDateTime        = date("Y-m-d H:i:s");
                    if(strtotime(date('Y-m-d H:i:s', strtotime($examDateTime))) >= strtotime(date("Y-m-d H:i:s")))
                    {
                        $data['currentStatus'] = true;
                        if(strtotime(date('Y-m-d H:i:s', strtotime($examDateTime))) > strtotime(date('Y-m-d H:i:s')))
                        {                      
                            $data['isTimerLoad'] = true;
                            $data['taskTime'] = date('Y-m-d H:i:s', strtotime($examDateTime));
                        }
                    }                    
                    $data['examQuestions'] = array();
                    $onlineExamQuestions = OnlineExamQuestion::where('on_code', $data['examCode'])->where('ln_code', $languageCode)->orderByRaw("CAST(on_order as UNSIGNED) ASC")->get();
                    if(count($onlineExamQuestions) > 0)
                    {
                        foreach ($onlineExamQuestions as $question) 
                        {
                            $options = array();

                            $option = array(
                                'id'     => 1,
                                'option' => $question->on_ans_1,
                                );
                            array_push($options, $option);
                            $option = array(
                                'id'     => 2,
                                'option' => $question->on_ans_2,
                                );
                            array_push($options, $option);
                            $option = array(
                                'id'     => 3,
                                'option' => $question->on_ans_3,
                                );
                            array_push($options, $option);
                            $option = array(
                                'id'     => 4,
                                'option' => $question->on_ans_4
                                );
                            array_push($options, $option);
                            $data1 = array(
                                'questionCode'      => $question->on_ques_id, 
                                'languageCode'      => $languageCode, 
                                'questionOrder'     => $question->on_order, 
                                'question'          => $question->on_question, 
                                'questionPattern'   => $question->on_pattern,
                                'questionSeconds'   => $question->on_seconds,
                                'options'           => $options,
                                'correctAnswer'     => $question->on_correct_ans,
                                'explanation'       => $question->on_explain
                            );
                            array_push($data['examQuestions'], $data1);  
                        }
                        $data['serverTime'] = date("Y-m-d H:i:s");
                        return response()->json(['Status' => true, 'Message' => 'Exams Available', 'data' => $data], 200);
                    }
                }
            }
        }
        elseif ($examType === 'model') 
        {
            $modelExam = ModelExam::where('mq_code', $examCode)->first();
            if(!empty($modelExam))
            {                
                $planModelExam = PlanModelExam::where('model_exam_code', $examCode)->where('plan_code', $planCode)->where('exam_date', $currentDate)->first();
                if(!empty($planModelExam))
                {
                    $modelExam              = $planModelExam->modelexam;
                    $data['examCode']       = $modelExam->mq_code;                                   
                    $data['examDate']       = $planModelExam->exam_date;
                    $data['examTime']       = $planModelExam->exam_time;
                    $data['taskTime']       = '';
                    $data['currentStatus']  = false;
                    $data['isTimerLoad']    = false;
                    $examDateTime           = $planModelExam->exam_date . ' ' . $planModelExam->exam_time;
                    $taskDateTime           = date("Y-m-d H:i:s", strtotime($currentDate .' '. $planModelExam->exam_time));                    
                    if(strtotime(date('Y-m-d H:i:s', strtotime($examDateTime))) >= strtotime(date("Y-m-d H:i:s")))
                    {
                        $data['currentStatus'] = true;                   
                        $newcurrentDateTime = date('Y-m-d H:i:s');                    
                        if(strtotime(date('Y-m-d H:i:s', strtotime($examDateTime))) > strtotime($newcurrentDateTime))
                        {
                            $data['isTimerLoad'] = true;
                            $data['taskTime'] = date('Y-m-d H:i:s', strtotime($examDateTime));
                        }
                    }
                    $data['examQuestions'] = array();                    
                    $modelExamQuestions = ModelExamQuestion::where('mq_code', $data['examCode'])->where('ln_code', $languageCode)->orderByRaw("CAST(mq_order as UNSIGNED) ASC")->get();
                    if(count($modelExamQuestions) > 0)
                    {
                        foreach ($modelExamQuestions as $question) 
                        {
                            $options = array();

                            $option = array(
                                'id'     => 1,
                                'option' => $question->mq_ans_1,
                                );
                            array_push($options, $option);
                            $option = array(
                                'id'     => 2,
                                'option' => $question->mq_ans_2,
                                );
                            array_push($options, $option);
                            $option = array(
                                'id'     => 3,
                                'option' => $question->mq_ans_3,
                                );
                            array_push($options, $option);
                            $option = array(
                                'id'     => 4,
                                'option' => $question->mq_ans_4
                                );
                            array_push($options, $option);
                            $data1 = array(
                                'questionCode'      => $question->mq_ques_id, 
                                'languageCode'      => $languageCode, 
                                'questionOrder'     => $question->mq_order, 
                                'question'          => $question->mq_question, 
                                'questionPattern'   => $question->mq_pattern,
                                'questionSeconds'   => $question->mq_seconds,
                                'options'           => $options,
                                'correctAnswer'     => $question->mq_correct_ans,
                                'explanation'       => $question->mq_explain
                            );
                            array_push($data['examQuestions'], $data1);  
                        }
                        $data['serverTime'] = date("Y-m-d H:i:s");
                        return response()->json(['Status' => true, 'Message' => 'Exams Available', 'data' => $data], 200);
                    }
                }
            }
        }
        elseif ($examType === 'daily') 
        {
            // $dailyTask = DailyTask::where('dy_code', $examCode)->first();            
            $planDailyTask = PlanDailyTask::where('daily_task_code', $examCode)->where('plan_code', $planCode)->first();

            if(!empty($planDailyTask))
            {
                $dailyTask = $planDailyTask->dailytask;

                $examDate  = explode(" ", $planDailyTask->exam_date);
                $tempDate  = $examDate[0]." 00:00:00";

                // For Current Date
                $TempTimeOne = strtotime($tempDate);
                $TempTimeTwo = strtotime($currentDate . ' ' . $currentTime);

                if($TempTimeOne == $TempTimeTwo) {
                    $currentDateStatus  = true;
                    $data['currentStatus'] = true;
                }
                //For Previous Date
                $TempTimeThree = strtotime($previousDate . ' ' . $previousTime);
                if($TempTimeThree == $TempTimeOne) {
                    $currentDateStatus     = false;
                    $data['currentStatus'] = false;
                }

                $todayTime = date("H:i:s");
                $examTime  = date("H:i:s", strtotime($planDailyTask->exam_time));

                $data['examCode'] = $dailyTask->dy_code;
                $data['examDate'] = $planDailyTask->exam_date;
                $data['examTime'] = $planDailyTask->exam_time;
                $data['taskTime'] = '';
                $data['isTimerLoad'] = false;
                $examDateTime  = $planDailyTask->exam_date . ' ' . $planDailyTask->exam_time;
                if(strtotime(date("Y-m-d H:i:s", strtotime($examDateTime))) >= strtotime(date("Y-m-d H:i:s")))
                {
                    $newcurrentDateTime = date('Y-m-d H:i:s');                    
                    // $newcurrentDateTime = date('Y-m-d H:i:s', strtotime(' - 1 hour'));                    
                    // if((((strtotime(date("Y-m-d H:i:s", strtotime($examDate[0].' '.$dailyTask->dy_time))) - strtotime($newcurrentDateTime))/60)/60) >= 1)
                    if(strtotime(date("Y-m-d H:i:s", strtotime($examDateTime))) > strtotime($newcurrentDateTime))
                    {
                        $data['isTimerLoad'] = true;
                        $data['taskTime'] = date('Y-m-d H:i:s', strtotime($examDate[0] . ' '. $dailyTask->dy_time));
                    }
                }                
                $dailyTaskQuestions = DailyTaskQuestion::where('dy_code', $data['examCode'])->where('ln_code', $languageCode)->orderByRaw("CAST(dy_order as UNSIGNED) ASC")->get();                
                $data['examQuestions'] = array();
                if(count($dailyTaskQuestions) > 0)
                {                    
                    foreach($dailyTaskQuestions as $question)
                    {
                        $options = array();
                        $option = array(
                            'id'     => 1,
                            'option' => $question->dy_ans_1,
                            );
                        array_push($options, $option);
                        $option = array(
                            'id'     => 2,
                            'option' => $question->dy_ans_2,
                            );
                        array_push($options, $option);
                        $option = array(
                            'id'     => 3,
                            'option' => $question->dy_ans_3,
                            );
                        array_push($options, $option);
                        $option = array(
                            'id'     => 4,
                            'option' => $question->dy_ans_4
                            );
                        array_push($options, $option);

                        $data1 = array(
                            'questionCode'      => $question->dy_ques_id, 
                            'languageCode'      => $languageCode, 
                            'questionOrder'     => $question->dy_order, 
                            'question'          => $question->dy_question, 
                            'questionPattern'   => $question->dy_pattern,
                            'questionSeconds'   => $question->dy_seconds,
                            'options'           => $options,
                            'correctAnswer'     => $question->dy_correct_ans,
                            'explanation'       => $question->dy_explain
                        );
                        array_push($data['examQuestions'], $data1);                       
                    }                     
                    $data['serverTime'] = date("Y-m-d H:i:s");
                    return response()->json(['Status' => true, 'Message' => 'Exams Available', 'data' => $data], 200);
                }
            }
            return response()->json(['Status' => false, 'Message' => 'No Exams Available'], 200);
        }
        return response()->json(['Status' => false, 'Message' => 'No Exams Available'], 200);
    }

    public function getExamCodeList(Request $request, $examType)
	{
        $authToken = $request->bearerToken();
        
        if(empty($authToken))
        {
            return response()->json(['status' => false, 'Message' => 'Authentication Failure'], 401);
        }

        $userDetail = UserDetail::where('auth_token', $authToken)->first();

        if(empty($userDetail))
        {
            return response()->json(['status' => false, 'Message' => 'Authentication Failure'], 401);
        }

        if(empty($examType))
        {
            return response()->json(['status' => false, 'Message' => 'Data Empty'], 200);
        }

        $currentDate       = date('Y-m-d');
        $currentTime       = "00:00:00";       
        $previousDate      = date('Y-m-d',strtotime("-1 days"));
        $previousTime      = "00:00:00";
        $currentDateStatus = false;

        $examLists = array();
        $planDetails = array();
        if(count($userDetail->userpaymentdetail) > 0)
        {
            $paymentDetails = $userDetail->userpaymentdetail->where('pack_status', 1)->where('payment_status', "CAPTURED");

            foreach ($paymentDetails as $key => $paymentDetail) 
            {
                $userplandetail = $paymentDetail->userplandetail;
                if(!empty($userplandetail))
                {
                    if($userplandetail->plandetail->plan_status == 2 && date('Y-m-d', strtotime($userplandetail->plandetail->plan_end_date)) >= date('Y-m-d'))
                    {
                        foreach($paymentDetail->userplandetail->plandetail->planexamdetail as $planexamdetail)
                        {
                            $data2['planCode']             = $paymentDetail->userplandetail->plandetail->plan_code;
                            $data2['online_exam_status']   = $paymentDetail->userplandetail->plandetail->online_exam_status;
                            $data2['daily_exam_status']    = $paymentDetail->userplandetail->plandetail->daily_exam_status;
                            $data2['model_exam_status']    = $paymentDetail->userplandetail->plandetail->model_exam_status;
                            $data2['schedule_exam_status'] = $paymentDetail->userplandetail->plandetail->schedule_exam_status;
                            $data2['live_exam_status']     = $paymentDetail->userplandetail->plandetail->live_exam_status;
                            $data2['ct_code']              = $planexamdetail->ct_code;
                            $data2['ex_code']              = $planexamdetail->ex_code;
                            array_push($planDetails, $data2);
                        }
                    }
                }
            }
        }
        if($examType === 'online')
        {
            if(count($planDetails) > 0)
            {
                foreach ($planDetails as $key => $planDetail) 
                {                    
                    if($planDetail['online_exam_status'])
                    {                        
                        $onlineExam = OnlineExam::where('ct_code', $planDetail['ct_code'])->where('ex_code', $planDetail['ex_code'])->whereDate('on_date', $currentDate)->first();
                        if(!empty($onlineExam))
                        {                            
                            $onlineExamDate  = $onlineExam->on_date;
                            $onlineExamTime  = $onlineExam->on_time;
                            $onlineExamCode  = $onlineExam->on_code;
                            $tempDate        = explode(" ", $onlineExamDate);
                            $examDateTime    = $tempDate[0]." ".$onlineExamTime;
                            $currentDateTime = date("Y-m-d H:i:s");
                            $currentTime     = strtotime($currentDateTime);
                            $ExamTime        = strtotime($examDateTime);
                            // return response()->json(['status' => $ExamTime >= $currentTime, 'Message' => 'onlineExam'], 200);
                            if($ExamTime >= $currentTime)
                            {                       
                                $data['examName']    = $onlineExam->examcategory->ct_name . '-' . $onlineExam->examsubcategory->ex_name. '- Online Exam';
                                $data['examDate']    = $tempDate[0];
                                $data['examTime']    = $onlineExamTime;
                                $data['examCode']    = $onlineExam->on_code;
                                $data['status']      = true;
                                $data['languages']   = array();
                                $data['subscribe']   = true;
                                $data['planCode']    = $planDetail['planCode'];
                                $data['isTimerLoad'] = false;
                                $newcurrentDateTime  = date('Y-m-d H:i:s');
                                if(strtotime(date('Y-m-d H:i:s', strtotime('-10 minutes', strtotime($tempDate[0].' '.$onlineExam->on_time)))) > strtotime($newcurrentDateTime))
			                    {                      
			                        $data['isTimerLoad'] = true;
			                        $data['taskTime'] = date('Y-m-d H:i:s', strtotime($tempDate[0] . ' '. $onlineExam->on_time));
			                    }
                                if(!empty($onlineExam->examsubcategory->commonlanguage))
                                {
                                    $languages = $onlineExam->examsubcategory->commonlanguage;
                                    foreach($languages as $language)
                                    {
                                        $data1['LanguageCode']  = $language->language->ln_code;
                                        $data1['LanguageName']  = $language->language->ln_name;
                                        array_push($data['languages'], $data1);
                                    }
                                }
                                $data['serverTime'] = date("Y-m-d H:i:s");
                                array_push($examLists, $data);
                            }
                        }
                    }
                }
                if(count($examLists) > 0)
                    return response()->json(['Status' => true, 'Message' => 'success', 'data' => $examLists], 200);
            }
        }
        elseif ($examType === 'model') 
        {
            if(count($planDetails) > 0)
            {
                foreach ($planDetails as $key => $planDetail) 
                {                    
                    if($planDetail['model_exam_status'])
                    {
                        $modelExam = ModelExam::where('ct_code', $planDetail['ct_code'])->where('ex_code', $planDetail['ex_code'])->whereDate('mq_date', $currentDate)->first();                       
                        if(!empty($modelExam))
                        {                            
                            $modelExamDate   = $modelExam->mq_date;
                            $modelExamTime   = $modelExam->mq_time;
                            $modelExamCode   = $modelExam->mq_code;
                            $tempDate        = explode(" ", $modelExamDate);

                            $examDateTime    = $tempDate[0]." ".$modelExamTime;
                            $currentDateTime = date("Y-m-d H:i:s");

                            $currentTime     = strtotime($currentDateTime);
                            $ExamTime        = strtotime($examDateTime);
                            if($ExamTime >= $currentTime)
                            {
                                $data['examName']    = $modelExam->examcategory->ct_name . '-' . $modelExam->examsubcategory->ex_name. '-Model Exam';
                                $data['examDate']    = $tempDate[0];
                                $data['examTime']    = $modelExamTime;
                                $data['examCode']    = $modelExam->mq_code;
                                $data['status']      = true;
                                $data['languages']   = array();
                                $data['subscribe']   = true;
                                $data['planCode']    = $planDetail['planCode'];
                                $data['isTimerLoad'] = false;
                                $newcurrentDateTime  = date('Y-m-d H:i:s');                    
			                    if(strtotime(date('Y-m-d H:i:s', strtotime('-10 minutes', strtotime($tempDate[0]. ' '.$modelExam->mq_time)))) > strtotime($newcurrentDateTime))
			                    {
			                        $data['isTimerLoad'] = true;
			                        $data['taskTime'] = date('Y-m-d H:i:s', strtotime($tempDate[0] . ' '. $modelExam->mq_time));
			                    }
                                if(!empty($modelExam->examsubcategory->commonlanguage))
                                {
                                    $languages = $modelExam->examsubcategory->commonlanguage;
                                    foreach($languages as $language)
                                    {
                                        $data1['LanguageCode']  = $language->language->ln_code;
                                        $data1['LanguageName']  = $language->language->ln_name;
                                        array_push($data['languages'], $data1);
                                    }
                                }
                                $data['serverTime'] = date("Y-m-d H:i:s");
                                array_push($examLists, $data);
                            }
                        }
                    }
                }               
                if(count($examLists) > 0)
                    return response()->json(['Status' => true, 'Message' => 'success', 'data' => $examLists], 200);
            }
        }
        elseif ($examType === 'daily') 
        {
            if(count($planDetails) > 0)
            {
                foreach ($planDetails as $key => $planDetail) 
                {
                    if($planDetail['daily_exam_status'])
                    {
                        $curDateOne = date("Y-m-d")." 00:00:00";                //current Date
                        $curDateTwo = date('Y-m-d',strtotime("-1 days"));
                        $curDateTwo = $curDateTwo." 00:00:00";                  //previous Date

                        $currentDate       = date("Y-m-d");
                        $currentTime       = "00:00:00";
                        $currentDailyCode  = "-";
                        $previousDate      = date('Y-m-d',strtotime("-1 days"));
                        $previousTime      = "00:00:00";
                        $previousDailyCode = "-";
                        
                        $dailyCode         = "-";
                        $isTimerLoad       = false;
                        $taskTime          = "-";
                      
                        $dailyTasks = DailyTask::where('ct_code', $planDetail['ct_code'])->where('ex_code', $planDetail['ex_code'])->whereDate('dy_date', $curDateOne)->orWhereDate('dy_date', $curDateTwo)->get();
                        foreach($dailyTasks as $dailyTask)
                        {
                            $dat = $dailyTask->dy_date;
                            $tim = $dailyTask->dy_time;
                            $dt  = explode(" ", $dat);
                            $tempDate = $dt[0]." 00:00:00";
                            // For Current Date
                            $TempTimeOne = strtotime($curDateOne);         //current_date
                            $TempTimeTwo = strtotime($tempDate);
                            //For Previous Date
                            $TempTimeThree = strtotime($curDateTwo);         //previous_date
                            if($TempTimeOne == $TempTimeTwo) 
                            {
                                $currentDate = $tempDate;
                                $currentTime = $tim;
                                $currentDailyCode = $dailyTask->dy_code;
                                $currentDateStatus = true;
                            }
                            elseif($TempTimeThree == $TempTimeTwo) 
                            {
                                $currentDateStatus = false;
                                $previousDate = $tempDate;
                                $previousTime = $tim;
                                $previousDailyCode = $dailyTask->dy_code;
                                $data['examName']  = $dailyTask->examcategory->ct_name . '-' . $dailyTask->examsubcategory->ex_name. '- Daily Task Exam';
                                $data['examDate'] = $dt[0];
                                $data['examTime'] = $tim;
                                $data['examCode'] = $dailyTask->dy_code;
                                $data['status']   = false;
                                $data['languages']  = array();
                                $data['subscribe']  = true;
                                $data['planCode']   = $planDetail['planCode'];
                                $data['isTimerLoad'] = false;
                                $newcurrentDateTime = date('Y-m-d H:i:s');
			                    if(strtotime(date("Y-m-d H:i:s", strtotime('-10 minutes', strtotime($dt[0].' '.$dailyTask->dy_time)))) > strtotime($newcurrentDateTime))
			                    {
			                        $data['isTimerLoad'] = true;
			                        $data['taskTime'] = date('Y-m-d H:i:s', strtotime($dt[0] . ' '. $dailyTask->dy_time));
			                    }
                                if(!empty($dailyTask->examsubcategory->commonlanguage))
                                {
                                    $languages = $dailyTask->examsubcategory->commonlanguage;
                                    foreach($languages as $language)
                                    {
                                        $data1['LanguageCode']  = $language->language->ln_code;
                                        $data1['LanguageName']  = $language->language->ln_name;
                                        array_push($data['languages'], $data1);
                                    }
                                }
                                $data['serverTime'] = date("Y-m-d H:i:s");
                                array_push($examLists, $data);
                            }

                            if($currentDateStatus)
                            {
                                $today_time = date("Y-m-d H:i:s");
                                if(strtotime($today_time) >= strtotime($dt[0].' '.$tim))
                                {
                                    $dailyCode = $currentDailyCode;
                                    $data['examName']    = $dailyTask->examcategory->ct_name . '-' . $dailyTask->examsubcategory->ex_name. '- Daily Task Exam';
                                    $data['examDate']    = $dt[0];
                                    $data['examTime']    = $tim;
                                    $data['examCode']    = $dailyTask->dy_code;
                                    $data['status']      = false;
                                    $data['subscribe']   = true;
                                    $data['planCode']    = $planDetail['planCode'];
                                    $data['languages']   = array();
                                    $data['isTimerLoad'] = false;
	                                $newcurrentDateTime = date('Y-m-d H:i:s');
				                    if(strtotime(date("Y-m-d H:i:s", strtotime('-10 minutes', strtotime($dt[0].' '.$dailyTask->dy_time)))) > strtotime($newcurrentDateTime))
				                    {
				                        $data['isTimerLoad'] = true;
				                        $data['taskTime'] = date('Y-m-d H:i:s', strtotime($dt[0] . ' '. $dailyTask->dy_time));
				                    }
                                    if(!empty($dailyTask->examsubcategory->commonlanguage))
                                    {
                                        $languages = $dailyTask->examsubcategory->commonlanguage;
                                        foreach($languages as $language)
                                        {
                                            $data1['LanguageCode']  = $language->language->ln_code;
                                            $data1['LanguageName']  = $language->language->ln_name;
                                            array_push($data['languages'], $data1);
                                        }
                                    }
                                    $data['serverTime'] = date("Y-m-d H:i:s");
                                    array_push($examLists, $data);
                                }
                                else 
                                {
                                    // $new_cur_time = strtotime($currentTime. ' - 1 hour'); // reduce one 1 hour
                                    // if(strtotime($today_time) >= $new_cur_time)
                                    // {
                                        $dailyCode = $currentDailyCode;
                                        $isTimerLoad = true;

                                        $ct_x = explode(" ", $currentDate);
                                        $taskTime = $ct_x[0]." ".$currentTime;
                                        $data['examName']    = $dailyTask->examcategory->ct_name . '-' . $dailyTask->examsubcategory->ex_name.'- Daily Task Exam';
                                        $data['examDate']    = $dt[0];
                                        $data['examTime']    = $tim;
                                        $data['examCode']    = $dailyTask->dy_code;
                                        $data['status']      = true;
                                        $data['subscribe']   = true;
                                        $data['planCode']    = $planDetail['planCode'];
                                        $data['languages']   = array();
                                        $data['isTimerLoad'] = false;
		                                $newcurrentDateTime = date('Y-m-d H:i:s');
					                    if(strtotime(date("Y-m-d H:i:s", strtotime('-10 minutes', strtotime($dt[0].' '.$dailyTask->dy_time)))) > strtotime($newcurrentDateTime))
					                    {
					                        $data['isTimerLoad'] = true;
					                        $data['taskTime'] = date('Y-m-d H:i:s', strtotime($dt[0] . ' '. $dailyTask->dy_time));
					                    }
                                        if(!empty($dailyTask->examsubcategory->commonlanguage))
                                        {
                                            $languages = $dailyTask->examsubcategory->commonlanguage;
                                            foreach($languages as $language)
                                            {
                                                $data1['LanguageCode']  = $language->language->ln_code;
                                                $data1['LanguageName']  = $language->language->ln_name;

                                                array_push($data['languages'], $data1);
                                            }
                                        }
                                        $data['serverTime'] = date("Y-m-d H:i:s");
                                        array_push($examLists, $data);
                                    /*}
                                    else 
                                    {
                                        $dailyCode = $previousDailyCode;
                                        $data['examName']    = $dailyTask->examcategory->ct_name . '-' . $dailyTask->examsubcategory->ex_name. '- Daily Task Exam';
                                        $data['examdate']    = $dt[0];
                                        $data['examtime']    = $tim;
                                        $data['examCode']    = $dailyTask->dy_code;
                                        $data['status']      = true;
                                        $data['languages']  = array();
                                        $data['planCode']    = $plandetail['planCode'];
                                        $data['languages']   = array();
                                        if(!empty($dailyTask->examsubcategory->commonlanguage))
                                        {
                                            $languages = $dailyTask->examsubcategory->commonlanguage;
                                            foreach($languages as $language)
                                            {
                                                $data1['LanguageCode']  = $language->language->ln_code;
                                                $data1['LanguageName']  = $language->language->ln_name;

                                                array_push($data['languages'], $data1);
                                            }
                                        }
                                        array_push($examLists, $data);
                                    }*/
                                }
                            }
                        }
                    }
                }
                if(count($examLists) > 0)
                    return response()->json(['Status' => true, 'Message' => 'success', 'data' => $examLists], 200);
            }
        }
        return response()->json(['Status' => false, 'Message' => 'Not success'], 200);
    }
    
    public function checkRegister($userCode, $examCode)
    {
        $paymentDetails = UserPaymentDetail::select('register_no')->where('user_code', $userCode)->first();

        if($paymentDetails)
        {
            $systemRegisterNo  = $paymentDetails->register_no;

            $registerQuery = UserRegistrationDetail::where('user_code', $userCode)->where('register_no', $systemRegisterNo)->where('exam_code', $examCode)->first();
            if($registerQuery){
                return true;
            }
        }
        return false;
    }

    public function getTermsAndConditions(Request $request, $examType)
    {
        $authToken = $request->bearerToken();
        if(empty($authToken))
        {
            return response()->json(['status' => false, 'Message' => 'Authentication Failure'], 401);            
        }        

        $userDetail = UserDetail::where('auth_token', $authToken)->first();

        if(empty($userDetail))
        {
            return response()->json(['status' => false, 'Message' => 'Authentication Failure'], 401);
        }

        if(empty($examType))
        {
            return response()->json(['status' => false, 'Message' => 'Data Empty'], 200);
        }

        $validator = Validator::make($request->all(), [
            'examCode'     => 'required',
            // 'languageCode' => 'required'
        ]);

        if($validator->fails()){  
            $errors = $validator->errors();
            $message = '';
            foreach ($errors->all() as $error) {
               $message .= $error . ' ';
            }       
            $result         = ['status' => false, 'message' => $message];
            return response()->json($result, 200);
        }

        $examCode     = trim($request->examCode);
        // $languageCode = trim($request->languageCode);

        $instraction = array();

        if($examType === 'online')
        {
            $examTypeId = VidhvaaExamList::where('slug', 'online-live-exam')->first();
            if(!empty($examTypeId))
            {
                $exam = OnlineExam::where('on_code', $examCode)->first();
                if(!empty($exam))
                {
                    $categoryId = $exam->examcategory->id;
                    $subcategoryId = $exam->examsubcategory->id;
                    $examInstractions = ExamInstruction::where('exam_category_id', $categoryId)->where('exam_subcategory_id', $subcategoryId)->where('exam_type_id', $examTypeId->id)->get();
                    if(count($examInstractions) > 0)
                    {
                        foreach ($examInstractions as $examInstraction) {
                            $data['languageCode'] = $examInstraction->language->ln_code;
                            $data['examInstraction'] = $examInstraction->instructions;

                            array_push($instraction, $data);
                        }

                        return response()->json(['Status' => true, 'Message' => 'success', 'TermsAndConditions' => $instraction], 200);
                    }
                }
            }
        }
        elseif ($examType === 'model') 
        {
            $examTypeId = VidhvaaExamList::where('slug', 'model-live-exam')->first();
            if(!empty($examTypeId))
            {
                $exam = ModelExam::where('mq_code', $examCode)->first();
                if(!empty($exam))
                {
                    $categoryId = $exam->examcategory->id;
                    $subcategoryId = $exam->examsubcategory->id;
                    $examInstractions = ExamInstruction::where('exam_category_id', $categoryId)->where('exam_subcategory_id', $subcategoryId)->where('exam_type_id', $examTypeId->id)->get();
                    if(count($examInstractions) > 0)
                    {
                        foreach ($examInstractions as $examInstraction) {
                            $data['languageCode'] = $examInstraction->language->ln_code;
                            $data['examInstraction'] = $examInstraction->instructions;

                            array_push($instraction, $data);
                        }
                        return response()->json(['Status' => true, 'Message' => 'success', 'TermsAndConditions' => $instraction], 200);
                    }
                }
            }
        }
        elseif ($examType === 'daily') 
        {            
            $examTypeId = VidhvaaExamList::select('id')->where('slug', 'daily-task')->first();            
            if(!empty($examTypeId))
            {
                $exam = DailyTask::where('dy_code', $examCode)->first();                
                if(!empty($exam))
                {                    
                    $categoryId = $exam->examcategory->id;
                    $subcategoryId = $exam->examsubcategory->id;                    
                    $examInstractions = ExamInstruction::where('exam_category_id', $categoryId)->where('exam_subcategory_id', $subcategoryId)->where('exam_type_id', $examTypeId->id)->get();
                    if(count($examInstractions) > 0)
                    {
                        foreach ($examInstractions as $examInstraction) {
                            $data['languageCode'] = $examInstraction->language->ln_code;
                            $data['examInstraction'] = $examInstraction->instructions;

                            array_push($instraction, $data);
                        }

                        return response()->json(['Status' => true, 'Message' => 'success', 'TermsAndConditions' => $instraction], 200);
                    }
                }
            }
        }      
        return response()->json(['Status' => false, 'Message' => 'not success', 'TermsAndConditions' => $instraction], 200);
    }

    public function getExamQuestions(Request $request, $examType)
    {
        $authToken = $request->bearerToken();
        if(empty($authToken))
        {
            return response()->json(['status' => false, 'Message' => 'Authentication Failure'], 401);            
        }
        $userDetail = UserDetail::where('auth_token', $authToken)->first();
        if(empty($userDetail))
        {
            return response()->json(['status' => false, 'Message' => 'Authentication Failure'], 401);
        }
        if(empty($examType))
        {
            return response()->json(['status' => false, 'Message' => 'Data Empty'], 200);
        }
        $validator = Validator::make($request->all(), [
            'examCode'     => 'required',
            'languageCode' => 'required'
        ]);
        if($validator->fails()){  
            $errors = $validator->errors();
            $message = '';
            foreach ($errors->all() as $error) {
               $message .= $error . ' ';
            }       
            $result         = ['status' => false, 'message' => $message];
            return response()->json($result, 200);
        }
        $examCode     = trim($request->examCode);
        $languageCode = trim($request->languageCode);
        $data              = [];            
        $currentDate       = date("Y-m-d");;
        $currentTime       = "00:00:00";            
        $previousDate      = date('Y-m-d',strtotime("-1 days"));
        $previousTime      = "00:00:00";
        $currentDateStatus = false;
        $isTimerLoad       = false;
        $taskTime          = "";
        if($examType === 'online')
        {
            $onlineExam = OnlineExam::where('on_code', $examCode)->whereDate('on_date', $currentDate)->first();
            if(!empty($onlineExam))
            {
                $data['examCode']       = $onlineExam->on_code;
                $data['examDate']       = $onlineExam->on_date;
                $data['examTime']       = $onlineExam->on_time;
                $data['taskTime']       = '';
                $data['currentStatus']  = false;
                $data['isTimerLoad']    = false;
                $examDate = explode(' ', $onlineExam->on_date);
                $taskDateTime    = date("Y-m-d H:i:s", strtotime($currentDate .' '. $onlineExam->on_time));
                $currentDateTime = date("Y-m-d H:i:s");
                if(strtotime(date('Y-m-d H:i:s', strtotime($examDate[0].' '.$onlineExam->on_time))) >= strtotime(date("Y-m-d H:i:s")))
                {
                    $data['currentStatus'] = true;
                    // $newcurrentDateTime = date('Y-m-d H:i:s', strtotime(' - 1 hour')); 
                    $newcurrentDateTime = date('Y-m-d H:i:s'); 
                    // if((((strtotime(date('Y-m-d H:i:s', strtotime($examDate[0].' '.$onlineExam->on_time))) - strtotime($newcurrentDateTime))/60)/60) >= 0)
                    if(strtotime(date('Y-m-d H:i:s', strtotime($examDate[0].' '.$onlineExam->on_time))) > strtotime($newcurrentDateTime))
                    {                      
                        $data['isTimerLoad'] = true;
                        $data['taskTime'] = date('Y-m-d H:i:s', strtotime($examDate[0] . ' '. $onlineExam->on_time));
                    }
                }
                $data['examQuestions'] = array();

                $onlineExamQuestions = OnlineExamQuestion::where('on_code', $data['examCode'])->where('ln_code', $languageCode)->orderByRaw("CAST(on_order as UNSIGNED) ASC")->get();
                if(count($onlineExamQuestions) > 0)
                {
                    foreach ($onlineExamQuestions as $question) 
                    {
                        $options = array();

                        $option = array(
                            'id'     => 1,
                            'option' => $question->on_ans_1,
                            );
                        array_push($options, $option);
                        $option = array(
                            'id'     => 2,
                            'option' => $question->on_ans_2,
                            );
                        array_push($options, $option);
                        $option = array(
                            'id'     => 3,
                            'option' => $question->on_ans_3,
                            );
                        array_push($options, $option);
                        $option = array(
                            'id'     => 4,
                            'option' => $question->on_ans_4
                            );
                        array_push($options, $option);
                        $data1 = array(
                            'questionCode'      => $question->on_ques_id, 
                            'languageCode'      => $languageCode, 
                            'questionOrder'     => $question->on_order, 
                            'question'          => $question->on_question, 
                            'questionPattern'   => $question->on_pattern,
                            'questionSeconds'   => $question->on_seconds,
                            'options'           => $options,
                            'correctAnswer'     => $question->on_correct_ans,
                            'explanation'       => $question->on_explain
                        );
                        array_push($data['examQuestions'], $data1);  
                    }
                    $data['serverTime'] = date("Y-m-d H:i:s");
                    return response()->json(['Status' => true, 'Message' => 'Exams Available', 'data' => $data], 200);
                }
            }
        }
        elseif ($examType === 'model') 
        {
            $modelExam = ModelExam::where('mq_code', $examCode)->whereDate('mq_date', $currentDate)->first();
            if(!empty($modelExam))
            {
                $data['examCode']       = $modelExam->mq_code;
                $data['examDate']       = $modelExam->mq_date;
                $data['examTime']       = $modelExam->mq_time;
                $data['taskTime']       = '';
                $data['currentStatus']  = false;
                $data['isTimerLoad']    = false;
                $examDate = explode(' ', $modelExam->mq_date);
                $taskDateTime    = date("Y-m-d H:i:s", strtotime($currentDate .' '. $modelExam->mq_time));               
                if(strtotime(date('Y-m-d H:i:s', strtotime($examDate[0]. ' '.$modelExam->mq_time))) >= strtotime(date("Y-m-d H:i:s")))
                {
                    $data['currentStatus'] = true;
                    // $newcurrentDateTime = date('Y-m-d H:i:s', strtotime(' - 1 hour'));                    
                    $newcurrentDateTime = date('Y-m-d H:i:s');                    
                    if(strtotime(date('Y-m-d H:i:s', strtotime($examDate[0]. ' '.$modelExam->mq_time))) > strtotime($newcurrentDateTime))
                    {
                        $data['isTimerLoad'] = true;
                        $data['taskTime'] = date('Y-m-d H:i:s', strtotime($examDate[0] . ' '. $modelExam->mq_time));
                    }
                }
                $data['examQuestions'] = array();

                $modelExamQuestions = ModelExamQuestion::where('mq_code', $data['examCode'])->where('ln_code', $languageCode)->orderByRaw("CAST(mq_order as UNSIGNED) ASC")->get();
                if(count($modelExamQuestions) > 0)
                {
                    foreach ($modelExamQuestions as $question) 
                    {
                        $options = array();

                        $option = array(
                            'id'     => 1,
                            'option' => $question->mq_ans_1,
                            );
                        array_push($options, $option);
                        $option = array(
                            'id'     => 2,
                            'option' => $question->mq_ans_2,
                            );
                        array_push($options, $option);
                        $option = array(
                            'id'     => 3,
                            'option' => $question->mq_ans_3,
                            );
                        array_push($options, $option);
                        $option = array(
                            'id'     => 4,
                            'option' => $question->mq_ans_4
                            );
                        array_push($options, $option);
                        $data1 = array(
                            'questionCode'      => $question->mq_ques_id, 
                            'languageCode'      => $languageCode, 
                            'questionOrder'     => $question->mq_order, 
                            'question'          => $question->mq_question, 
                            'questionPattern'   => $question->mq_pattern,
                            'questionSeconds'   => $question->mq_seconds,
                            'options'           => $options,
                            'correctAnswer'     => $question->mq_correct_ans,
                            'explanation'       => $question->mq_explain
                        );
                        array_push($data['examQuestions'], $data1);  
                    }
                    $data['serverTime'] = date("Y-m-d H:i:s");
                    return response()->json(['Status' => true, 'Message' => 'Exams Available', 'data' => $data], 200);
                }
            }
        }
        elseif ($examType === 'daily') 
        {
            $dailyTask = DailyTask::where('dy_code', $examCode)->first();           
            if(!empty($dailyTask))
            { 
                $examDate = explode(" ", $dailyTask->dy_date);
                $tempDate = $examDate[0]." 00:00:00";

                // For Current Date
                $TempTimeOne = strtotime($tempDate);
                $TempTimeTwo = strtotime($currentDate . ' ' . $currentTime);

                if($TempTimeOne == $TempTimeTwo) {
                    $currentDateStatus  = true;
                    $data['currentStatus'] = true;
                }
                //For Previous Date
                $TempTimeThree = strtotime($previousDate . ' ' . $previousTime);
                if($TempTimeThree == $TempTimeOne) {
                    $currentDateStatus     = false;
                    $data['currentStatus'] = false;
                }

                $todayTime = date("H:i:s");
                $examTime  = date("H:i:s", strtotime($dailyTask->dy_time));

                $data['examCode'] = $dailyTask->dy_code;
                $data['examDate'] = $dailyTask->dy_date;
                $data['examTime'] = $dailyTask->dy_time;
                $data['taskTime'] = '';
                $data['isTimerLoad'] = false;
                $examDate  = explode(' ', $dailyTask->dy_date);
                if(strtotime(date("Y-m-d H:i:s", strtotime($examDate[0].' '.$dailyTask->dy_time))) >= strtotime(date("Y-m-d H:i:s")))
                {
                    $newcurrentDateTime = date('Y-m-d H:i:s');                    
                    // $newcurrentDateTime = date('Y-m-d H:i:s', strtotime(' - 1 hour'));                    
                    // if((((strtotime(date("Y-m-d H:i:s", strtotime($examDate[0].' '.$dailyTask->dy_time))) - strtotime($newcurrentDateTime))/60)/60) >= 1)
                    if(strtotime(date("Y-m-d H:i:s", strtotime($examDate[0].' '.$dailyTask->dy_time))) > strtotime($newcurrentDateTime))
                    {
                        $data['isTimerLoad'] = true;
                        $data['taskTime'] = date('Y-m-d H:i:s', strtotime($examDate[0] . ' '. $dailyTask->dy_time));
                    }
                }

                $dailyTaskQuestions = DailyTaskQuestion::where('dy_code', $data['examCode'])->where('ln_code', $languageCode)->orderByRaw("CAST(dy_order as UNSIGNED) ASC")->get();
                $data['examQuestions'] = array();
                if(count($dailyTaskQuestions) > 0)
                {                    
                    foreach($dailyTaskQuestions as $question)
                    {
                        $options = array();
                        $option = array(
                            'id'     => 1,
                            'option' => $question->dy_ans_1,
                            );
                        array_push($options, $option);
                        $option = array(
                            'id'     => 2,
                            'option' => $question->dy_ans_2,
                            );
                        array_push($options, $option);
                        $option = array(
                            'id'     => 3,
                            'option' => $question->dy_ans_3,
                            );
                        array_push($options, $option);
                        $option = array(
                            'id'     => 4,
                            'option' => $question->dy_ans_4
                            );
                        array_push($options, $option);

                        $data1 = array(
                            'questionCode'      => $question->dy_ques_id, 
                            'languageCode'      => $languageCode, 
                            'questionOrder'     => $question->dy_order, 
                            'question'          => $question->dy_question, 
                            'questionPattern'   => $question->dy_pattern,
                            'questionSeconds'   => $question->dy_seconds,
                            'options'           => $options,
                            'correctAnswer'     => $question->dy_correct_ans,
                            'explanation'       => $question->dy_explain
                        );
                        array_push($data['examQuestions'], $data1);                       
                    }                     
                    $data['serverTime'] = date("Y-m-d H:i:s");
                    return response()->json(['Status' => true, 'Message' => 'Exams Available', 'data' => $data], 200);
                }
            }
            return response()->json(['Status' => false, 'Message' => 'No Exams Available'], 200);
        }
        return response()->json(['Status' => false, 'Message' => 'No Exams Available'], 200);
    }

    public function storeIndividualResult(Request $request, $examType)
    {
        $authToken = $request->bearerToken();
        if(empty($authToken))
        {
            return response()->json(['status' => false, 'Message' => 'Authentication Failure'], 401);            
        }
        $userDetail = UserDetail::where('auth_token', $authToken)->first();
        if(empty($userDetail))
        {
            return response()->json(['status' => false, 'Message' => 'Authentication Failure'], 401);
        }
        if(empty($examType))
        {
            return response()->json(['status' => false, 'Message' => 'Data Empty'], 200);
        }
        $userId = $userDetail->id;
        $validator = Validator::make($request->all(), [
            'examCode'      => 'required',
            'languageCode'  => 'required',
            'questionCode'  => 'required',
            'userAnswer'    => 'required',
            'userResult'    => 'required',
            'userTime'      => 'required',
            'allAnswer'     => 'required',
            'questionOrder' => 'required',
            'planCode'      => 'required'
        ]);
        if($validator->fails()){  
            $errors = $validator->errors();
            $message = '';
            foreach ($errors->all() as $error) {
               $message .= $error . ' ';
            }       
            $result         = ['status' => false, 'message' => $message];
            return response()->json($result, 200);
        }
        $examCode      = trim($request->examCode);
        $languageCode  = trim($request->languageCode);
        $questionCode  = trim($request->questionCode);
        $userAnswer    = trim($request->userAnswer);
        $userResult    = trim($request->userResult);
        $userTime      = trim($request->userTime);
        $questionOrder = trim($request->questionOrder);
        $planCode      = trim($request->planCode);
        $allAnswer     = $request->allAnswer;
      
        $data           = [];        
        $userCode       = $userDetail->user_code;
        if($examType === 'online')
        {
            $onlineExamOneResult = OnlineExamOneResult::where('user_code', $userCode)->where('on_code', $examCode)->where('on_ques_id', $questionCode)->first();
            if(empty($onlineExamOneResult))
            {
                $data = array(
                    'trans_id'      => $userId.'O'.date('Ymd').'Q'.$questionOrder, //rand(10000,99999), 
                    'user_code'     => $userCode, 
                    'on_code'       => $examCode, 
                    'ln_code'       => $languageCode, 
                    'on_ques_id'    => $questionCode, 
                    'user_answer'   => $userAnswer,
                    'user_time'     => $userTime, 
                    'plan_code'     => $planCode, 
                    'created_at'    => date('Y-m-d H:i:s'), 
                    'updated_at'    => date('Y-m-d H:i:s'),
                    'user_result'   => $userResult,
                    'user_status'   => 1
                );
                $queryStatus = OnlineExamOneResult::insert($data);
                if($queryStatus) 
                {
                    $onlineExamResult = OnlineExamResult::where('user_code', $userCode)->where('on_code', $examCode)->where('ln_code', $languageCode)->first();

                    $encryptedResult = base64_encode(serialize($allAnswer));

                    $crtCount = 0;
                    $wrgCount = 0;
                    $notCount = 0;

                    if(empty($onlineExamResult))
                    {
                        if($userResult)
                        {
                            $crtCount = $crtCount + 1;
                        }
                        else
                        {
                            $wrgCount = $wrgCount + 1;
                        }

                         $data = array(
                            'trans_id'          => $userId.'ON'.date('Ymd'), 
                            'user_code'         => $userCode, 
                            'on_code'           => $examCode, 
                            'ln_code'           => $languageCode,
                            'correct_answer'    => $crtCount,  
                            'wrong_answer'      => $wrgCount,
                            'not_answer'        => $notCount, 
                            'plan_code'         => $planCode, 
                            'all_answer'        => $encryptedResult,
                            'created_at'        => date('Y-m-d H:i:s'), 
                            'updated_at'        => date('Y-m-d H:i:s')
                        );
                        $queryStatus1 = OnlineExamResult::insert($data);  
                    }
                    else
                    {
                        $crtCount = intval($onlineExamResult->correct_answer);
                        $wrgCount = intval($onlineExamResult->wrong_answer);
                        if($userResult)
                        {
                            $crtCount = $crtCount + 1;
                        }
                        else
                        {
                            $wrgCount = $wrgCount + 1;
                        }

                        $onlineExamResult->correct_answer   = $crtCount; 
                        $onlineExamResult->wrong_answer     = $wrgCount;
                        $onlineExamResult->all_answer       = $encryptedResult;
                        $onlineExamResult->updated_at       = date('Y-m-d H:i:s');
                        $onlineExamResult->save();
                    }
                    return response()->json(['Status' => true, 'Message' => 'Result is updated...'], 200);
                }
            }
        }
        elseif ($examType === 'model') 
        {
            $modelExamOneResult = ModelExamOneResult::where('user_code', $userCode)->where('mq_code', $examCode)->where('mq_ques_id', $questionCode)->first();
            if(empty($modelExamOneResult))
            {
                $data = array(
                    'trans_id'      => $userId.'M'.date('Ymd').'Q'.$questionOrder, //rand(10000,99999), 
                    'user_code'     => $userCode, 
                    'mq_code'       => $examCode, 
                    'ln_code'       => $languageCode, 
                    'mq_ques_id'    => $questionCode, 
                    'user_answer'   => $userAnswer,
                    'user_time'     => $userTime, 
                    'plan_code'     => $planCode, 
                    'created_at'    => date('Y-m-d H:i:s'),
                    'updated_at'    => date('Y-m-d H:i:s'),
                    'user_result'   => $userResult,
                    'user_status'   => 1
                );
                $queryStatus = ModelExamOneResult::insert($data);
                if($queryStatus) 
                {
                    $modelExamResult = ModelExamResult::where('user_code', $userCode)->where('mq_code', $examCode)->where('ln_code', $languageCode)->first();

                    $encryptedResult = base64_encode(serialize($allAnswer));

                    $crtCount = 0;
                    $wrgCount = 0;
                    $notCount = 0;

                    if(empty($modelExamResult))
                    {
                        if($userResult)
                        {
                            $crtCount = $crtCount + 1;
                        }
                        else
                        {
                            $wrgCount = $wrgCount + 1;
                        }

                         $data = array(
                            'trans_id'          => $userId.'MQ'.date('Ymd'), 
                            'user_code'         => $userCode, 
                            'mq_code'           => $examCode, 
                            'ln_code'           => $languageCode,
                            'correct_answer'    => $crtCount,  
                            'wrong_answer'      => $wrgCount,
                            'not_answer'        => $notCount, 
                            'plan_code'         => $planCode, 
                            'all_answer'        => $encryptedResult,
                            'created_at'        => date('Y-m-d H:i:s'), 
                            'updated_at'        => date('Y-m-d H:i:s')
                        );
                        $queryStatus1 = ModelExamResult::insert($data);  
                    }
                    else
                    {
                        $crtCount = intval($modelExamResult->correct_answer);
                        $wrgCount = intval($modelExamResult->wrong_answer);
                        if($userResult)
                        {
                            $crtCount = $crtCount + 1;
                        }
                        else
                        {
                            $wrgCount = $wrgCount + 1;
                        }

                        $modelExamResult->correct_answer   = $crtCount; 
                        $modelExamResult->wrong_answer     = $wrgCount;
                        $modelExamResult->all_answer       = $encryptedResult;
                        $modelExamResult->updated_at       = date('Y-m-d H:i:s');
                        $modelExamResult->save();
                    }
                    return response()->json(['Status' => true, 'Message' => 'Result is updated...'], 200);
                }
            }
        }
        elseif ($examType === 'daily') 
        {            
            $dailyTaskOneResult = DailyTaskOneResult::where('user_code', $userCode)->where('dy_code', $examCode)->where('dy_ques_id', $questionCode)->first();
            if(empty($dailyTaskOneResult))
            {
                $data = array(
                    'trans_id'      => $userId.'D'.date('Ymd').'Q'.$questionOrder, //rand(10000,99999), 
                    'user_code'     => $userCode, 
                    'dy_code'       => $examCode, 
                    'ln_code'       => $languageCode, 
                    'dy_ques_id'    => $questionCode, 
                    'user_answer'   => $userAnswer,
                    'user_time'     => $userTime, 
                    'plan_code'     => $planCode, 
                    'created_at'    => date('Y-m-d H:i:s'),
                    'updated_at'    => date('Y-m-d H:i:s'),
                    'user_result'   => $userResult
                );
                $queryStatus = DailyTaskOneResult::insert($data);
                if($queryStatus) 
                {
                    $dailyResult = DailyTaskResult::where('user_code', $userCode)->where('dy_code', $examCode)->where('ln_code', $languageCode)->first();

                    $encryptedResult = base64_encode(serialize($allAnswer));

                    $crtCount = 0;
                    $wrgCount = 0;
                    $notCount = 0;

                    if(empty($dailyResult))
                    {
                        if($userResult)
                        {
                            $crtCount = $crtCount + 1;
                        }
                        else
                        {
                            $wrgCount = $wrgCount + 1;
                        }

                         $data = array(
                            'trans_id'          => $userId.'DY'.date('Ymd'), 
                            'user_code'         => $userCode, 
                            'dy_code'           => $examCode, 
                            'ln_code'           => $languageCode,
                            'correct_answer'    => $crtCount,  
                            'wrong_answer'      => $wrgCount,
                            'not_answer'        => $notCount, 
                            'all_answer'        => $encryptedResult,
                            'plan_code'         => $planCode,
                            'created_at'        => date('Y-m-d H:i:s'), 
                            'updated_at'        => date('Y-m-d H:i:s'),
                            'start_time'        => date('Y-m-d H:i:s')
                        );
                        $queryStatus1 = DailyTaskResult::insert($data);  
                    }
                    else
                    {
                        $crtCount = intval($dailyResult->correct_answer);
                        $wrgCount = intval($dailyResult->wrong_answer);
                        if($userResult)
                        {
                            $crtCount = $crtCount + 1;
                        }
                        else
                        {
                            $wrgCount = $wrgCount + 1;
                        }

                        $dailyResult->correct_answer   = $crtCount; 
                        $dailyResult->wrong_answer     = $wrgCount;
                        $dailyResult->all_answer       = $encryptedResult;
                        $dailyResult->updated_at       = date('Y-m-d H:i:s');
                        $dailyResult->save();
                    }
                    return response()->json(['Status' => true, 'Message' => 'Result is updated...'], 200);
                }
            }
        }
        return response()->json(['Status' => false, 'Message' => 'Result is Not updated...'], 200);        
    }

    public function storeResult(Request $request, $examType)
    {
        $authToken = $request->bearerToken();
        if(empty($authToken))
        {
            return response()->json(['status' => false, 'Message' => 'Authentication Failure'], 401);            
        }
        $userDetail = UserDetail::where('auth_token', $authToken)->first();
        if(empty($userDetail))
        {
            return response()->json(['status' => false, 'Message' => 'Authentication Failure'], 401);
        }
        if(empty($examType))
        {
            return response()->json(['status' => false, 'Message' => 'Data Empty'], 200);
        }

        $validator = Validator::make($request->all(), [
            'examCode'     => 'required',
            'languageCode' => 'required',
            'correctAnswer'=> 'required',
            'wrongAnswer'  => 'required',
            'notAnswer'    => 'required',
            'start_time'   => 'required',
            'timeTaken'    => 'required',
            'allAnswer'    => 'required',
            'planCode'      => 'required'
        ]);
        if($validator->fails()){  
            $errors = $validator->errors();
            $message = '';
            foreach ($errors->all() as $error) {
               $message .= $error . ' ';
            }       
            $result         = ['status' => false, 'message' => $message];
            return response()->json($result, 200);
        }

        $examCode      = trim($request->examCode);
        $languageCode  = trim($request->languageCode);

        $correctAnswer = trim($request->correctAnswer);
        $wrongAnswer   = trim($request->wrongAnswer);
        $notAnswer     = trim($request->notAnswer);
        $startTime     = trim($request->startTime);        
        $timeTaken     = trim($request->timeTaken);        
        $planCode      = trim($request->planCode);        
        $allAnswer     = $request->allAnswer;
       
        $data           = [];
        $userCode       = $userDetail->user_code;  
       
        if($examType === 'online')
        {
            $onlineExamResult = OnlineExamResult::where('user_code', $userCode)->where('on_code', $examCode)->where('ln_code', $languageCode)->first();
            $encryptedResult = base64_encode( serialize( $allAnswer ) );

            if(empty($onlineExamResult))
            {
                $transId = $userCode.'ON'.date('Ymd');

                $data = array(
                    'trans_id'       => $transId, 
                    'user_code'      => $userCode,
                    'on_code'        => $examCode, 
                    'ln_code'        => $languageCode,
                    'correct_answer' => $correctAnswer,  
                    'wrong_answer'   => $wrongAnswer,
                    'not_answer'     => $notAnswer, 
                    'all_answer'     => $encryptedResult,
                    'time_taken'     => $timeTaken,
                    'plan_code'      => $planCode,
                    'start_time'     => date('Y-m-d H:i:s', strtotime($startTime)),
                    'created_at'     => date('Y-m-d H:i:s'), 
                    'updated_at'     => date('Y-m-d H:i:s')
                );
                $queryStatus = OnlineExamResult::insert($data);
                if($queryStatus)
                    return response()->json(['Status' => true, 'Message' => 'Result is updated...'], 200);                
            }
            else
            {
                $onlineExamResult->correct_answer   = $correctAnswer; 
                $onlineExamResult->wrong_answer     = $wrongAnswer;
                $onlineExamResult->not_answer       = $notAnswer;
                $onlineExamResult->all_answer       = $encryptedResult;
                $onlineExamResult->time_taken       = $timeTaken;
                $onlineExamResult->start_time       = date('Y-m-d H:i:s', strtotime($startTime));
                $onlineExamResult->updated_at       = date('Y-m-d H:i:s');
                $onlineExamResult->save();
                if($onlineExamResult)
                    return response()->json(['Status' => true, 'Message' => 'Result is updated...'], 200);
            }
        }
        elseif ($examType === 'model') 
        {
            $modelExamResult = ModelExamResult::where('user_code', $userCode)->where('mq_code', $examCode)->where('ln_code', $languageCode)->first();
            $encryptedResult = base64_encode( serialize( $allAnswer ) );

            if(empty($modelExamResult))
            {
                $transId = $userCode.'MQ'.date('Ymd');

                $data = array(
                    'trans_id'       => $transId, 
                    'user_code'      => $userCode,
                    'mq_code'        => $examCode, 
                    'ln_code'        => $languageCode,
                    'correct_answer' => $correctAnswer,  
                    'wrong_answer'   => $wrongAnswer,
                    'not_answer'     => $notAnswer, 
                    'all_answer'     => $encryptedResult,
                    'plan_code'      => $planCode,
                    'time_taken'     => $timeTaken,
                    'start_time'     => date('Y-m-d H:i:s', strtotime($startTime)),
                    'created_at'     => date('Y-m-d H:i:s'), 
                    'updated_at'     => date('Y-m-d H:i:s')
                );
                $queryStatus = ModelExamResult::insert($data);
                if($queryStatus)
                    return response()->json(['Status' => true, 'Message' => 'Result is updated...'], 200);                
            }
            else
            {
                $modelExamResult->correct_answer   = $correctAnswer; 
                $modelExamResult->wrong_answer     = $wrongAnswer;
                $modelExamResult->not_answer       = $notAnswer;
                $modelExamResult->all_answer       = $encryptedResult;
                $modelExamResult->time_taken       = $timeTaken;
                $modelExamResult->start_time       = date('Y-m-d H:i:s', strtotime($startTime));
                $modelExamResult->updated_at       = date('Y-m-d H:i:s');
                $modelExamResult->save();
                if($modelExamResult)
                    return response()->json(['Status' => true, 'Message' => 'Result is updated...'], 200);
            }
        }
        elseif ($examType === 'daily') 
        {            
            $dailyResult = DailyTaskResult::where('user_code', $userCode)->where('dy_code', $examCode)->where('ln_code', $languageCode)->first();
            $encryptedResult = base64_encode( serialize( $allAnswer ) );

            if(empty($dailyResult))
            {
                $transId = $userCode.'DY'.date('Ymd');

                $data = array(
                    'trans_id'       => $transId, 
                    'user_code'      => $userCode,
                    'dy_code'        => $examCode, 
                    'ln_code'        => $languageCode,
                    'correct_answer' => $correctAnswer,  
                    'wrong_answer'   => $wrongAnswer,
                    'not_answer'     => $notAnswer, 
                    'all_answer'     => $encryptedResult,
                    'time_taken'     => $timeTaken,
                    'plan_code'      => $planCode,
                    'start_time'     => date('Y-m-d H:i:s', strtotime($startTime)),
                    'created_at'     => date('Y-m-d H:i:s'), 
                    'updated_at'     => date('Y-m-d H:i:s')
                );
                $queryStatus = DailyTaskResult::insert($data);
                if($queryStatus)
                    return response()->json(['Status' => true, 'Message' => 'Result is updated...'], 200);                
            }
            else
            {
                $dailyResult->correct_answer   = $correctAnswer; 
                $dailyResult->wrong_answer     = $wrongAnswer;
                $dailyResult->not_answer       = $notAnswer;
                $dailyResult->all_answer       = $encryptedResult;
                $dailyResult->time_taken       = $timeTaken;
                $dailyResult->start_time       = date('Y-m-d H:i:s', strtotime($startTime));
                $dailyResult->updated_at       = date('Y-m-d H:i:s');
                $dailyResult->save();
                if($dailyResult)
                    return response()->json(['Status' => true, 'Message' => 'Result is updated...'], 200);
            }
        }        
        return response()->json(['Status' => false, 'Message' => 'Result is not updated...'], 200);
    }

    public function getUserPlanDetails(Request $request)
    {
        $authToken = $request->bearerToken();
        if(empty($authToken))
        {
            return response()->json(['status' => false, 'Message' => 'Authentication Failure'], 401);            
        }
        $userDetail = UserDetail::where('auth_token', $authToken)->first();
        if(empty($userDetail))
        {
            return response()->json(['status' => false, 'Message' => 'Authentication Failure'], 401);
        }

        $data = array();        
        if(count($userDetail->userpaymentdetail) > 0)
        {
            $paymentDetails = $userDetail->userpaymentdetail->where('pack_status', 1)->where('payment_status', "CAPTURED");           
            foreach ($paymentDetails as $key => $paymentDetail) 
            {
                $userplandetail = $paymentDetail->userplandetail;               
                if(!empty($userplandetail))
                {                   
                    if($userplandetail->plandetail->plan_status == 2 && date('Y-m-d', strtotime($userplandetail->plandetail->plan_end_date)) >= date('Y-m-d'))
                    {
                        $data1['planCode']              = $userplandetail->plan_code;
                        $data1['planName']              = $userplandetail->plandetail->plan_name;
                        $data1['planRegistertDate']     = date('d-m-Y', strtotime($userplandetail->plan_registered_date));
                        $data1['planStartDate']         = date('d-m-Y', strtotime($userplandetail->plandetail->plan_start_date));
                        $data1['planEndDate']           = date('d-m-Y', strtotime($userplandetail->plandetail->plan_end_date));
                        $data1['dailyExamStatus']       = $userplandetail->plandetail->daily_exam_status;
                        $data1['modelExamStatus']       = $userplandetail->plandetail->model_exam_status;
                        $data1['onlineExamStatus']      = $userplandetail->plandetail->online_exam_status;
                        $data1['onlineLiveExamStatus']  = $userplandetail->plandetail->live_exam_status;
                        $data1['scheduleExamStatus']    = $userplandetail->plandetail->schedule_exam_status;
                        $data1['planAmount']            = $userplandetail->plandetail->plan_amount;
                        $data1['materialStatus']        = 1;
                        $data1['planActiveStatus']      = 'Current Plan';
                        $data1['courseCategory']        = array();
                        $data1['planDescription']       = array();
                        // foreach($paymentDetails[0]->userplandetail->plandetail->planexamdetail as $planexamdetail)
                        foreach($userplandetail->plandetail->planexamdetail as $planexamdetail)
                        {
                            $data2['categoryCode']      = $planexamdetail->ct_code;
                            $data2['subcategoryCode']   = $planexamdetail->ex_code;
                            array_push($data1['courseCategory'], $data2);
                        }

                        // foreach($paymentDetails[0]->userplandetail->plandetail->planaddetail as $planaddetail)
                        foreach($userplandetail->plandetail->planaddetail as $planaddetail)
                        {
                            $data22['languageCode']  = $planaddetail->ln_code;
                            $data22['description']   = $planaddetail->plan_detail;
                            array_push($data1['planDescription'], $data22);
                        }

                        $data1['ExamList']  = array();
                        $data1['DailyExamList']  = array();
                        $data1['OnlineExamList'] = array();
                        $data1['ModelExamList']  = array();
                        $dailyTaskExams = $userplandetail->plandetail->plandailytasks;
                        $onlineExams    = $userplandetail->plandetail->planonlineexams;
                        $modelExams     = $userplandetail->plandetail->planmodelexams;
                        if(count($dailyTaskExams) > 0)
                        {
                            foreach ($dailyTaskExams as $dailyTaskExam) {
                                $tempExamList = array(
                                    'examCode'              => $dailyTaskExam->daily_task_code,
                                    'examName'              => $dailyTaskExam->dailytask->dy_name.' - '.$dailyTaskExam->examcategory->ct_name.' - '.$dailyTaskExam->examsubcategory->ex_name,
                                    'examCategoryCode'      => $dailyTaskExam->ct_code,
                                    'examSubcategoryCode'   => $dailyTaskExam->ex_code,
                                    'examStartDateTime'     => date('d-m-Y H:i:s', strtotime($dailyTaskExam->exam_date.' '.$dailyTaskExam->exam_time))
                                );
                               
                                $tempExamList['examUserStatus'] =  'Upcoming';
                                if(strtotime(date('Y-m-d')) == strtotime($dailyTaskExam->exam_date))
                                {
                                    if(strtotime(date('H:i:s')) == strtotime($dailyTaskExam->exam_time))
                                    {
                                        $tempExamList['examUserStatus'] =  'Ongoing';
                                    }
                                    elseif(strtotime(date('H:i:s')) > strtotime($dailyTaskExam->exam_time))
                                    {
                                        $result = $dailyTaskExam->dailytaskresult->where('dy_code', $dailyTaskExam->dailytask->dy_code)->where('user_code', $userDetail->user_code);
                                        if(!empty($result))
                                        {
                                            // return response()->json(['status' => $userplandetail->plan_code.' --> ' .$userDetail->user_code, 'Message' => $result], 401);
                                            // $result = $result->userDailyTaskResult($userplandetail->plan_code, $userDetail->user_code);
                                            $tempExamList['examUserStatus'] =  'Completed';
                                        }
                                        else
                                        {
                                            $tempExamList['examUserStatus'] =  'Missed';
                                        }
                                    }
                                }
                                elseif (strtotime(date('Y-m-d')) > strtotime($dailyTaskExam->exam_date))
                                {                                    
                                    $result = $dailyTaskExam->dailytaskresult->where('dy_code', $dailyTaskExam->dailytask->dy_code)->where('user_code', $userDetail->user_code);
                                    if(!empty($result))
                                    {
                                        // $result = $result->userDailyTaskResult($userplandetail->plan_code, $userDetail->user_code);
                                        $tempExamList['examUserStatus'] =  'Completed';
                                    }
                                    else
                                    {
                                        $tempExamList['examUserStatus'] =  'Missed';
                                    }
                                }
                                
                                array_push($data1['ExamList'], $tempExamList);
                                array_push($data1['DailyExamList'], $tempExamList);
                            }
                        }

                        if(count($onlineExams) > 0)
                        {
                            foreach ($onlineExams as $onlineExam) {
                                $tempExamList = array(
                                    'examCode'              => $onlineExam->online_exam_code,
                                    'examName'              => $onlineExam->onlineexam->on_name.' - '.$onlineExam->examcategory->ct_name.' - '.$onlineExam->examsubcategory->ex_name,
                                    'examCategoryCode'      => $onlineExam->ct_code,
                                    'examSubcategoryCode'   => $onlineExam->ex_code,
                                    'examStartDateTime'     => date('d-m-Y H:i:s', strtotime($onlineExam->exam_date.' '.$onlineExam->exam_time))
                                );

                                $tempExamList['examUserStatus'] =  'Upcoming';
                                if(strtotime(date('Y-m-d')) == strtotime($onlineExam->exam_date))
                                {
                                    if(strtotime(date('H:i:s')) == strtotime($onlineExam->exam_time))
                                    {
                                        $tempExamList['examUserStatus'] =  'Ongoing';
                                    }
                                    elseif(strtotime(date('H:i:s')) > strtotime($onlineExam->exam_time))
                                    {
                                        $result = $onlineExam->onlineexamresult->where('on_code', $onlineExam->onlineexam->on_code)->where('user_code', $userDetail->user_code);
                                        if(!empty($result))
                                        {
                                            // $result = $result->userOnlineExamResult($userplandetail->plan_code, $userDetail->user_code);
                                            $tempExamList['examUserStatus'] =  'Completed';
                                        }
                                        else
                                        {
                                            $tempExamList['examUserStatus'] =  'Missed';
                                        }
                                    }
                                }
                                elseif (strtotime(date('Y-m-d')) > strtotime($onlineExam->exam_date))
                                {                                    
                                    $result = $onlineExam->onlineexamresult->where('on_code', $onlineExam->onlineexam->on_code)->where('user_code', $userDetail->user_code);
                                    if(!empty($result))
                                    {
                                        // $result = $result->userOnlineExamResult($userplandetail->plan_code, $userDetail->user_code);
                                        $tempExamList['examUserStatus'] =  'Completed';
                                    }
                                    else
                                    {
                                        $tempExamList['examUserStatus'] =  'Missed';
                                    }
                                }

                                array_push($data1['ExamList'], $tempExamList);                                
                                array_push($data1['OnlineExamList'], $tempExamList);                                
                            }
                        }

                        if(count($modelExams) > 0)
                        {
                            foreach ($modelExams as $modelExam) {
                                $tempExamList = array(
                                    'examCode'              => $modelExam->model_exam_code,
                                    'examName'              => $modelExam->modelexam->mq_name.' - '.$modelExam->examcategory->ct_name.' - '.$modelExam->examsubcategory->ex_name,
                                    'examCategoryCode'      => $modelExam->ct_code,
                                    'examSubcategoryCode'   => $modelExam->ex_code,
                                    'examStartDateTime'     => date('d-m-Y H:i:s', strtotime($modelExam->exam_date.' '.$modelExam->exam_time)),
                                    'examUserStatus'        => 'completed'
                                );

                                $tempExamList['examUserStatus'] =  'Upcoming';
                                if(strtotime(date('Y-m-d')) == strtotime($modelExam->exam_date))
                                {
                                    if(strtotime(date('H:i:s')) == strtotime($modelExam->exam_time))
                                    {
                                        $tempExamList['examUserStatus'] =  'Ongoing';
                                    }
                                    elseif(strtotime(date('H:i:s')) > strtotime($modelExam->exam_time))
                                    {
                                        $result = $modelExam->modelexamresult->where('mq_code', $modelExam->modelexam->mq_code)->where('user_code', $userDetail->user_code);
                                        if(!empty($result))
                                        {
                                            // $result = $result->userModelExamResult($userplandetail->plan_code, $userDetail->user_code);
                                            $tempExamList['examUserStatus'] =  'Completed';
                                        }
                                        else
                                        {
                                            $tempExamList['examUserStatus'] =  'Missed';
                                        }
                                    }
                                }
                                elseif (strtotime(date('Y-m-d')) > strtotime($modelExam->exam_date))
                                {                                    
                                    $result = $modelExam->modelexamresult->where('mq_code', $modelExam->modelexam->mq_code)->where('user_code', $userDetail->user_code);
                                    if(!empty($result))
                                    {
                                        // $result = $result->userModelExamResult($userplandetail->plan_code, $userDetail->user_code);
                                        $tempExamList['examUserStatus'] =  'Completed';
                                    }
                                    else
                                    {
                                        $tempExamList['examUserStatus'] =  'Missed';
                                    }
                                }

                                array_push($data1['ExamList'], $tempExamList);
                                array_push($data1['ModelExamList'], $tempExamList);
                            }
                        }

                        array_push($data, $data1);
                    }
                    elseif ($userplandetail->plandetail->plan_status == 1 && date('Y-m-d', strtotime($userplandetail->plandetail->plan_end_date)) >= date('Y-m-d'))
                    {
                        $data1['planCode']              = $userplandetail->plan_code;
                        $data1['planName']              = $userplandetail->plandetail->plan_name;
                        $data1['planRegistertDate']     = date('d-m-Y', strtotime($userplandetail->plan_registered_date));
                        $data1['planStartDate']         = date('d-m-Y', strtotime($userplandetail->plandetail->plan_start_date));
                        $data1['planEndDate']           = date('d-m-Y', strtotime($userplandetail->plandetail->plan_end_date));
                        $data1['planAmount']            = $userplandetail->plandetail->plan_amount;
                        $data1['dailyExamStatus']       = $userplandetail->plandetail->daily_exam_status;
                        $data1['modelExamStatus']       = $userplandetail->plandetail->model_exam_status;
                        $data1['onlineExamStatus']      = $userplandetail->plandetail->online_exam_status;
                        $data1['onlineLiveExamStatus']  = $userplandetail->plandetail->live_exam_status;
                        $data1['scheduleExamStatus']    = $userplandetail->plandetail->schedule_exam_status;
                        $data1['materialStatus']         = 1;
                        $data1['planActiveStatus']      = 'Upcoming Plan';
                        $data1['courseCategory']        = array();
                        foreach($userplandetail->plandetail->planexamdetail as $planexamdetail)
                        {
                            $data2['categoryCode']      = $planexamdetail->ct_code;
                            $data2['subcategoryCode']   = $planexamdetail->ex_code;
                            array_push($data1['courseCategory'], $data2);
                        }

                        // foreach($paymentDetails[0]->userplandetail->plandetail->planaddetail as $planaddetail)
                        foreach($userplandetail->plandetail->planaddetail as $planaddetail)
                        {
                            $data22['languageCode']  = $planaddetail->ln_code;
                            $data22['description']   = $planaddetail->plan_detail;
                            array_push($data1['planDescription'], $data22);
                        }

                        $data1['DailyExamList'] = array();
                        $data1['OnlineExamList'] = array();
                        $data1['ModelExamList']  = array();

                        $dailyTaskExams = $userplandetail->plandetail->plandailytasks;
                        $onlineExams    = $userplandetail->plandetail->planonlineexams;
                        $modelExams     = $userplandetail->plandetail->planmodelexams;

                        if(count($dailyTaskExams) > 0)
                        {
                            foreach ($dailyTaskExams as $dailyTaskExam) {
                                $tempExamList = array(
                                    'examCode'              => $dailyTaskExam->daily_task_code,
                                    'examName'              => $dailyTaskExam->dailytask->dy_name.' - '.$dailyTaskExam->examcategory->ct_name.' - '.$dailyTaskExam->examsubcategory->ex_name,
                                    'examCategoryCode'      => $dailyTaskExam->ct_code,
                                    'examSubcategoryCode'   => $dailyTaskExam->ex_code,
                                    'examStartDateTime'     => date('d-m-Y H:i:s', strtotime($dailyTaskExam->exam_date.' '.$dailyTaskExam->exam_time)),
                                );
                                $tempExamList['examUserStatus'] =  'Upcoming';
                                
                                if(strtotime(date('Y-m-d')) == strtotime($dailyTaskExam->exam_date))
                                {
                                    if(strtotime(date('H:i:s')) == strtotime($dailyTaskExam->exam_time))
                                    {
                                        $tempExamList['examUserStatus'] =  'Ongoing';
                                    }
                                    elseif (strtotime(date('H:i:s')) > strtotime($dailyTaskExam->exam_time))
                                    {
                                        $result = $dailyTaskExam->dailytaskresult->where('dy_code', $dailyTaskExam->dailytask->dy_code)->where('user_code', $userDetail->user_code);
                                        if(!empty($result))
                                        {
                                            $tempExamList['examUserStatus'] =  'Completed';
                                        }
                                        else
                                        {
                                            $tempExamList['examUserStatus'] =  'Missed';
                                        }
                                    }
                                }
                                elseif (strtotime(date('Y-m-d')) > strtotime($dailyTaskExam->exam_date))
                                {                                    
                                    $result = $dailyTaskExam->dailytaskresult->where('dy_code', $dailyTaskExam->dailytask->dy_code)->where('user_code', $userDetail->user_code);
                                    if(!empty($result))
                                    {
                                        $tempExamList['examUserStatus'] =  'Completed';
                                    }
                                    else
                                    {
                                        $tempExamList['examUserStatus'] =  'Missed';
                                    }
                                }
                                // elseif (strtotime(date('Y-m-d')) < strtotime($dailyTaskExam->exam_date))
                                // {
                                // }
                                array_push($data1['DailyExamList'], $tempExamList);
                            }
                        }

                        if(count($onlineExams) > 0)
                        {
                            foreach ($onlineExams as $onlineExam) {
                                $tempExamList = array(
                                    'examCode'              => $onlineExam->online_exam_code,
                                    'examName'              => $onlineExam->onlineexam->on_name.' - '.$onlineExam->examcategory->ct_name.' - '.$onlineExam->examsubcategory->ex_name,
                                    'examCategoryCode'      => $onlineExam->ct_code,
                                    'examSubcategoryCode'   => $onlineExam->ex_code,
                                    'examStartDateTime'     => date('d-m-Y H:i:s', strtotime($onlineExam->exam_date.' '.$onlineExam->exam_time)),
                                );
                                $tempExamList['examUserStatus'] =  'Upcoming';
                                if(strtotime(date('Y-m-d')) == strtotime($onlineExam->exam_date))
                                {
                                    if(strtotime(date('H:i:s')) == strtotime($onlineExam->exam_time))
                                    {
                                        $tempExamList['examUserStatus'] =  'Ongoing';
                                    }
                                    elseif (strtotime(date('H:i:s')) > strtotime($onlineExam->exam_time))
                                    {
                                        $result = $onlineExam->onlineexamresult->where('on_code', $onlineExam->onlinexam->on_code)->where('user_code', $userDetail->user_code);
                                        if(!empty($result))
                                        {
                                            // $result = $result->userOnlineExamResult($userplandetail->plan_code, $userDetail->user_code);
                                            $tempExamList['examUserStatus'] =  'Completed';
                                        }
                                        else
                                        {
                                            $tempExamList['examUserStatus'] =  'Missed';
                                        }
                                    }
                                }
                                elseif (strtotime(date('Y-m-d')) > strtotime($onlineExam->exam_date))
                                {                                    
                                    $result = $onlineExam->onlineexamresult->where('on_code', $onlineExam->onlineexam->on_code)->where('user_code', $userDetail->user_code);
                                    if(!empty($result))
                                    {
                                        // $result = $result->userOnlineExamResult($userplandetail->plan_code, $userDetail->user_code);
                                        $tempExamList['examUserStatus'] =  'Completed';
                                    }
                                    else
                                    {
                                        $tempExamList['examUserStatus'] =  'Missed';
                                    }
                                }
                                // elseif (strtotime(date('Y-m-d')) < strtotime($onlineExam->exam_date))
                                // {
                                // }
                                array_push($data1['OnlineExamList'], $tempExamList);                                
                            }
                        }

                        if(count($modelExams) > 0)
                        {
                            foreach ($modelExams as $modelExam) {
                                $tempExamList = array(
                                    'examCode'              => $modelExam->model_exam_code,
                                    'examName'              => $modelExam->modelexam->mq_name.' - '.$modelExam->examcategory->ct_name.' - '.$modelExam->examsubcategory->ex_name,
                                    'examCategoryCode'      => $modelExam->ct_code,
                                    'examSubcategoryCode'   => $modelExam->ex_code,
                                    'examStartDateTime'     => date('d-m-Y H:i:s', strtotime($modelExam->exam_date.' '.$modelExam->exam_time)),
                                );
                                $tempExamList['examUserStatus'] =  'Upcoming';
                                if(strtotime(date('Y-m-d')) == strtotime($modelExam->exam_date))
                                {
                                    if(strtotime(date('H:i:s')) == strtotime($modelExam->exam_time))
                                    {
                                        $tempExamList['examUserStatus'] =  'Ongoing';
                                    }
                                    elseif (strtotime(date('H:i:s')) > strtotime($modelExam->exam_time))
                                    {
                                        $result = $modelExam->modelexamresult->where('mq_code', $modelExam->modelexam->mq_code)->where('user_code', $userDetail->user_code);
                                        if(!empty($result))
                                        {
                                            // $result = $result->userModelExamResult($userplandetail->plan_code, $userDetail->user_code);
                                            $tempExamList['examUserStatus'] =  'Completed';
                                        }
                                        else
                                        {
                                            $tempExamList['examUserStatus'] =  'Missed';
                                        }
                                    }
                                }
                                elseif (strtotime(date('Y-m-d')) > strtotime($modelExam->exam_date))
                                {                                    
                                    $result = $modelExam->modelexamresult->where('mq_code', $modelExam->modelexam->mq_code)->where('user_code', $userDetail->user_code);
                                    if(!empty($result))
                                    {
                                        // $result = $result->userModelExamResult($userplandetail->plan_code, $userDetail->user_code);
                                        $tempExamList['examUserStatus'] =  'Completed';
                                    }
                                    else
                                    {
                                        $tempExamList['examUserStatus'] =  'Missed';
                                    }
                                }
                                // elseif (strtotime(date('Y-m-d')) < strtotime($modelExam->exam_date))
                                // {
                                // }
                                array_push($data1['ModelExamList'], $tempExamList);
                            }
                        }
                        array_push($data, $data1);
                    }                  
                }
            }
            $result         = ['status' => true, 'message' => 'success', 'data' => $data];
            return response()->json($result, 200);
        }
        $result         = ['status' => false, 'message' => 'Data Not Found'];
        return response()->json($result, 200);
    }
}