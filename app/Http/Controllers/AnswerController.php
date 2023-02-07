<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\AnswersOptionsQuestions;
use App\Models\Project;
use App\Models\Question;
use App\Models\Survey;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class AnswerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

    }

    /**
     * Save answers responses in storage
     *
     * @param Request $request
     * @param Question $question
     * @param $cod_answer
     * @param $answer
     * @return bool
     */
    public function saveAnswerQuestion(Request $request, Question $question, $cod_answer,$answer){
        $data=[
            'cod_question'=>$question->cod_question,
            'cod_answer'=>$cod_answer
        ];

        switch ($question['type']) {
            case 'short_answer';
                $data['answer_txt']=$answer;
                $validate = \Validator::make($data, [
                    'answer_txt' => 'max:255',
                ]);
                if (!$validate->fails())
                    AnswersOptionsQuestions::where('cod_question', '=', $question->cod_question)
                        ->where('cod_answer', '=', $cod_answer)
                        ->delete();
                break;
            case 'long_text';
                $data['answer_txt']=$answer;
                AnswersOptionsQuestions::where('cod_question', '=', $question->cod_question)
                    ->where('cod_answer', '=', $cod_answer)
                    ->delete();
                break;
            case 'multiple_choice';
            case 'dropdown';
                $data['cod_option']=$answer;
                if($data['cod_option']!=null){
                    $validate = \Validator::make($data, [
                        'cod_option' => 'exists:options,cod_option,cod_question,' . $question->cod_question . '|unique:answers_options_questions,cod_option,null,id,cod_question,' . $question->cod_question . ',cod_answer,' . $cod_answer,
                    ]);
                }
                    AnswersOptionsQuestions::where('cod_question', '=', $question->cod_question)
                        ->where('cod_answer', '=', $cod_answer)
                        ->delete();
                break;
            case  'checkboxes';
                $data['cod_option']=$answer;
                if($data['cod_option']!=null) {
                    $validate = \Validator::make($data, [
                        'cod_option' => 'exists:options,cod_option,cod_question,' . $question->cod_question . '|unique:answers_options_questions,cod_option,null,id,cod_question,' . $question->cod_question . ',cod_answer,' . $cod_answer,
                    ]);
                }
                break;
            case 'date';
                $data['answer_txt']=$answer;
                if($data['answer_txt']!=null) {
                    $validate = \Validator::make($data, [
                        'answer_txt' => 'date_format:Y-m-d',
                    ]);
                }
                    AnswersOptionsQuestions::where('cod_question', '=', $question->cod_question)
                        ->where('cod_answer', '=', $cod_answer)
                        ->delete();
                break;
            case 'time';
                $data['answer_txt']=$answer;
                if($data['answer_txt']!=null) {
                    $validate = \Validator::make($data, [
                        'answer_txt' => 'date_format:H:i',
                    ]);
                }
                    AnswersOptionsQuestions::where('cod_question', '=', $question->cod_question)
                        ->where('cod_answer', '=', $cod_answer)
                        ->delete();
                break;
            case 'datetime';
                $data['answer_txt']=$answer;
                $validate = \Validator::make($data, [
                    'answer_txt' => 'required',
                ]);
                if (!$validate->fails())
                    AnswersOptionsQuestions::where('cod_question', '=', $question->cod_question)
                        ->where('cod_answer', '=', $cod_answer)
                        ->delete();
                break;
            case 'numerical';
                $data['answer_txt'] = $answer;
                if($data['answer_txt']!=null) {
                    $validate = \Validator::make($data, [
                        'answer_txt' => 'numeric',
                    ]);
                }
                    AnswersOptionsQuestions::where('cod_question', '=', $question->cod_question)
                        ->where('cod_answer', '=', $cod_answer)
                        ->delete();
                break;
        }

        if (isset($validate)&&$validate->fails()) {
            return false;
        }

        $answer = AnswersOptionsQuestions::create($data);
        if($answer){
            $log = "The user '" . $request->user()->id . "' create new answer $cod_answer, to question $question->cod_question";
            $this->log('info', $log, 'web', $request->user());
        }
        return true;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request,$id)
    {
        Controller::validatePermissions($request->user(),'POST','/answers');
        $survey=Survey::findOrFail($id);

        if(Project::select('projects.*')
                ->join('project_user','projects.cod_project','project_user.project_cod_project')
                ->where('project_user.user_id','=',$request->user()->id)
                ->where('project_user.project_cod_project','=',$survey->cod_project)
                ->first()||$request->user()->role->access
                ->where('method','=','GET')
                ->where('endpoint','=','/allprojects')
                ->first()) {
            DB::beginTransaction();
            $cod_answer = null;
            $errors = [];
            $validate = \Validator::make($request->all(), [
                'location.latitude' => 'required',
                'location.longitude' => 'required',
            ]);
            if ($validate->fails()) {
                DB::rollback();
                return $this->response('true', Response::HTTP_BAD_REQUEST, '400 BAD REQUEST', $validate->errors());
            }

            $questions= Question::select('questions.*','surveys.cod_survey','surveys.max_answers')
                ->join('sections','sections.cod_section','questions.cod_section')
                ->join('surveys','surveys.cod_survey','sections.cod_survey')
                ->where('surveys.cod_survey','=',$id)
                ->get();

            foreach ($questions as $ans){
                $data = [];
                $data['cod_question']=$ans['cod_question'];
                foreach ($request->answers as $res) {
                    if($ans['cod_question']==$res['cod_question']){
                        $data['answer']=$res['answer'];
                    }
                }

                if (!isset($data['answer'])) {
                    $data['answer'] = null;
                }

                $question = $ans;

                if ($cod_answer == null) {
                    if ($this->countAnswers($question->cod_survey) < $question->max_answers || $question->max_answers == -1) {
                        $answ = [
                            'latitude' => $request->location['latitude'],
                            'longitude' => $request->location['longitude'],
                            'id_user' => $request->user()->id,
                            'cod_survey' => $question['cod_survey']
                        ];
                        $ans = Answer::create($answ);
                        $cod_answer = $ans->cod_answer;
                    } else {
                        $errors[] = 'Número máximo de respuestas alcanzado';
                        DB::rollback();
                        return $this->response('true', Response::HTTP_BAD_REQUEST, '400 BAD REQUEST', $errors);
                    }
                }

                if ($question->required) {
                    if ($data['answer'] == null) {
                        $errors[] = 'Cuestionario incompleto';
                    }
                }

                if ($question->type != 'checkboxes') {
                    if (!$this->saveAnswerQuestion($request, $question, $cod_answer, $data['answer'])) {
                        $error=[
                            "question"=>$question->question,
                            "cod_question"=>$question->cod_question
                        ];
                        return $this->response('true', Response::HTTP_BAD_REQUEST, '400 BAD REQUEST',$error);
                    }
                }

                if ($question->type == 'checkboxes' && is_array($data['answer'])) {
                    AnswersOptionsQuestions::where('cod_question', '=', $question->cod_question)
                        ->where('cod_answer', '=', $cod_answer)
                        ->delete();
                    foreach ($data['answer'] as $d) {
                        if (!$this->saveAnswerQuestion($request, $question, $cod_answer, $d['value'])) {
                            $errors[] = 'Opciones incorrectas en seleccion multiple ' . $question->cod_question;
                        }
                    }
                }

                if ($errors != []) {
                    DB::rollback();
                    return $this->response('true', Response::HTTP_BAD_REQUEST, '400 BAD REQUEST', $errors);
                }
            }

            DB::commit();
            return $this->response(false, Response::HTTP_CREATED, '201 Created');
        }
        return $this->response('true', Response::HTTP_BAD_REQUEST, '400 BAD REQUEST');
    }

    public function responses(Request $request, $id){
        Controller::validatePermissions($request->user(),'POST','/projects/{project}/surveys');
        $survey=Survey::findOrFail($id);

        if(Project::select('projects.*')
                ->join('project_user','projects.cod_project','project_user.project_cod_project')
                ->where('project_user.user_id','=',$request->user()->id)
                ->where('project_user.project_cod_project','=',$survey->cod_project)
                ->first()||$request->user()->role->access
                ->where('method','=','GET')
                ->where('endpoint','=','/allprojects')
                ->first()) {
                $answers=Answer::where('cod_survey','=',$survey->cod_survey)
                    ->get();
                $json=[];
                foreach ($answers as $ans) {
                    $data=[];
                    $responses = AnswersOptionsQuestions::select('question', 'option', 'answer_txt', 'type','questions.cod_question','answers_options_questions.updated_at')
                        ->join('questions', 'answers_options_questions.cod_question', 'questions.cod_question')
                        ->leftjoin('options', 'answers_options_questions.cod_option', 'options.cod_option')
                        ->where('cod_answer', '=', $ans->cod_answer)
                        ->get();
                    $data['cod_answer'] = $ans->cod_answer;
                    $data['latitude'] = $ans->latitude;
                    $data['longitude'] = $ans->longitude;
                    $data['id_user'] = $ans->id_user;
                    $data['cod_survey'] = $ans->cod_survey;
                    $data['date']=date_format($ans->updated_at,"Y/m/d H:i:s");

                    foreach ($responses as $r) {
                        if($r->type!='checkboxes'){
                            $data[$r->question]= $r->option ?? $r->answer_txt;
                        }else{
                            if(isset($data[$r->question])){
                                $data[$r->question].=",$r->option";
                            }else{
                                $data[$r->question]=$r->option;
                            }

                        }
                    }
                    $json[]= $data;
                }
                return $this->response('false', Response::HTTP_OK, '200 OK',$json);
        }
    }

    public function responseGraphs(Request $request, $id){
        Controller::validatePermissions($request->user(),'POST','/projects/{project}/surveys');
        $survey=Survey::findOrFail($id);

        if(Project::select('projects.*')
                ->join('project_user','projects.cod_project','project_user.project_cod_project')
                ->where('project_user.user_id','=',$request->user()->id)
                ->where('project_user.project_cod_project','=',$survey->cod_project)
                ->first()||$request->user()->role->access
                ->where('method','=','GET')
                ->where('endpoint','=','/allprojects')
                ->first()) {

             $questions=Question::join('sections','sections.cod_section','questions.cod_section')
                ->where('sections.cod_survey','=',$survey->cod_survey)
                ->get();
             $answers=Answer::where('cod_survey','=',$survey->cod_survey)
                ->get();
             $responses=[];
             foreach ($questions  as $key =>$q){
                 $key=$key+1;
                 switch ($q->type){
                     case 'short_answer':
                     case 'numeric':
                     case 'long_text':
                         $question=[];
                         $question['question']="$key. $q->question";
                         $question['type']=$q->type;
                         $question['detail']=$q->detail;
                         $words = AnswersOptionsQuestions::where('cod_question','=',$q->cod_question)
                             ->select(DB::raw("SUBSTRING_INDEX(answer_txt, ' ', 1) as name"), DB::raw('count(*) as value'))
                             ->groupBy('name')
                             ->get();
                         $question['data']=$words;
                             $responses[]= $question;
                             break;
                     case 'dropdown':
                     case 'multiple_choice':
                         $question=[];
                         $question['question']="$key. $q->question";
                         $question['type']=$q->type;
                         $question['detail']=$q->detail;
                         $question['data']=array();
                         $options= $q->options;
                         foreach ($options as $o){
                             $o->count=AnswersOptionsQuestions::where('cod_option','=',$o->cod_option)
                                 ->where('cod_question','=',$q->cod_question)
                                 ->count();
                             $question['data'][] = [
                                 'value' => $o->count,
                                 'name' => $o->option
                             ];
                         }
                     $responses[]= $question;
                         break;
                     case 'checkboxes':
                         $question=[];
                         $question['question']="$key. $q->question";
                         $question['type']=$q->type;
                         $question['detail']=$q->detail;
                         $options= $q->options;
                         $question['options']=[];
                         foreach ($options as $o){
                             $o->count=AnswersOptionsQuestions::where('cod_option','=',$o->cod_option)
                                 ->where('cod_question','=',$q->cod_question)
                                 ->count();
                             $question['answers'][]=$o->count;
                                $question['options'][]=$o->option;
                         }
                         $responses[]= $question;
                         break;

                 }
             }
            return $this->response('false', Response::HTTP_OK, '200 OK',$responses);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request,$id)
    {
        //
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,$id)
    {
        Controller::validatePermissions($request->user(),'DELETE','/answers');
        $question=Answer::join('questions','answers.cod_question','=','questions.cod_question')
            ->where('cod_answer','=',$id)->firstOrFail();

        if(Project::select('projects.*')
                ->join('project_user','projects.cod_project','project_user.project_cod_project')
                ->join('surveys','surveys.cod_project','projects.cod_project')
                ->join('sections','sections.cod_survey','surveys.cod_survey')
                ->join('questions','questions.cod_section','sections.cod_section')
                ->where('project_user.user_id','=',$request->user()->id)
                ->where('questions.cod_section','=',$question->cod_section)
                ->first()||$request->user()->role->access
                ->where('method','=','GET')
                ->where('endpoint','=','/allprojects')
                ->first()) {
            Answer::where('cod_answer','=',$id)->delete();
            return $this->response('false', Response::HTTP_OK, '200 OK');
        }
        return $this->response('true', Response::HTTP_BAD_REQUEST, '400 BAD REQUEST');
    }


    /**
     * count survey responses, refactoring
     *
     * @param $cod_survey
     * @return int
     * @note refactor in another stage
     */
    public function countAnswers($cod_survey){
        return count(Answer::select('cod_answer')
            ->join('surveys as s2','s2.cod_survey','=','answers.cod_survey')
            ->where('s2.cod_survey','=',$cod_survey)
            ->groupBy('cod_answer')->get());
    }
}
