<?php

namespace App\Http\Controllers;

use App\Models\Answer;
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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Controller::validatePermissions($request->user(),'POST','/answers');
        $data=[];
        $edit_permission=[
            'cod_question',
            'cod_option',
            'answer_txt',
            'latitude',
            'length',
            'cod_answer'
        ];
        foreach ($edit_permission as $d){
            if(isset($request->$d)){
                $data[$d]=$request->$d;
            }
        }

        $validate=\Validator::make($data,[
            'cod_question'=>'required|exists:questions,cod_question',
            'latitude'=>'required',
            'length'=>'required',
            'cod_answer'=>'exists:answers,cod_answer',
        ]);

        if ($validate->fails())
        {
            return $this->response('true', Response::HTTP_BAD_REQUEST, '400 BAD REQUEST', $validate->errors());
        }

          $question=Question::find($data['cod_question'])
             ->join('sections','sections.cod_section','=','questions.cod_section')
             ->join('surveys','surveys.cod_survey','=','sections.cod_survey')
             ->first();

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

            if(!isset($data['cod_answer'])){
                if($this->countAnswers($question->cod_survey)<$question->max_answers || $question->max_answers==-1){
                    $cod_answer=$this->generateCodeAnswer();
                }else{
                    return $this->response('true', Response::HTTP_BAD_REQUEST, '400 BAD REQUEST');
                }
            }else{
                $cod_answer=$data['cod_answer'];
            }

            switch ($question['type']) {
                case 'short_answer';
                    $validate = \Validator::make($data, [
                        'answer_txt' => 'required|max:255',
                    ]);
                    if (!$validate->fails())
                        Answer::where('cod_question', '=', $question->cod_question)
                            ->where('cod_answer', '=', $cod_answer)
                            ->delete();
                    unset($data['cod_option']);
                    break;
                case 'long_text';
                    $validate = \Validator::make($data, [
                        'answer_txt' => 'required',
                    ]);
                    if (!$validate->fails())
                        Answer::where('cod_question', '=', $question->cod_question)
                            ->where('cod_answer', '=', $cod_answer)
                            ->delete();
                    unset($data['cod_option']);
                    break;
                case 'multiple_choice';
                case 'dropdown';
                    $validate = \Validator::make($data, [
                        'cod_option' => 'exists:options,cod_option,cod_question,' . $question->cod_question . '|required|unique:answers,cod_option,cod_question,cod_answer',
                    ]);
                    if (!$validate->fails())
                        Answer::where('cod_question', '=', $question->cod_question)
                            ->where('cod_answer', '=', $cod_answer)
                            ->delete();
                    unset($data['answer_txt']);
                    break;
                case  'checkboxes';
                    $validate = \Validator::make($data, [
                        'cod_option' => 'exists:options,cod_option,cod_question,' . $question->cod_question . '|required|unique:answers,cod_option,cod_question,cod_answer',
                    ]);
                    unset($data['answer_txt']);
                    break;
                case 'date';
                    $validate = \Validator::make($data, [
                        'answer_txt' => 'date_format:Y-m-d|required',
                    ]);
                    if (!$validate->fails())
                        Answer::where('cod_question', '=', $question->cod_question)
                            ->where('cod_answer', '=', $cod_answer)
                            ->delete();
                    unset($data['cod_option']);
                    break;
                case 'time';
                    $validate = \Validator::make($data, [
                        'answer_txt' => 'date_format:H:i:s|required|',
                    ]);
                    if (!$validate->fails())
                        Answer::where('cod_question', '=', $question->cod_question)
                            ->where('cod_answer', '=', $cod_answer)
                            ->delete();
                    unset($data['cod_option']);
                    break;
                case 'datetime';
                    $validate = \Validator::make($data, [
                        'answer_txt' => 'date_format:Y-m-d H:i:s|required',
                    ]);
                    if (!$validate->fails())
                        Answer::where('cod_question', '=', $question->cod_question)
                            ->where('cod_answer', '=', $cod_answer)
                            ->delete();
                    unset($data['cod_option']);
                    break;
                case 'numerical';
                    $validate = \Validator::make($data, [
                        'answer_txt' => 'numeric|required',
                    ]);
                    if (!$validate->fails())
                        Answer::where('cod_question', '=', $question->cod_question)
                            ->where('cod_answer', '=', $cod_answer)
                            ->delete();
                    unset($data['cod_option']);
                    break;
            }
            if ($validate->fails()) {
                return $this->response('true', Response::HTTP_BAD_REQUEST, '400 BAD REQUEST', $validate->errors());
            }
            $data['id_user'] = $request->user()->id;
            $data['cod_answer'] = $cod_answer;
            $answer = Answer::create($data);
            $log = "The user '" . $request->user()->id . "' create new answer $cod_answer, question $question->cod_question";
            $this->log('info', $log, 'web', $request->user());
            return $this->response(false, Response::HTTP_CREATED, '201 Created', $answer);
        }
        return $this->response('true', Response::HTTP_BAD_REQUEST, '400 BAD REQUEST');
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
     * generate a new code answer
     *
     * @return int|mixed
     */
    public function generateCodeAnswer(){
       $cod_answer=Answer::select('cod_answer')
        ->orderBy('answers.cod_answer', 'DESC')
           ->first();
       return isset($cod_answer)?$cod_answer['cod_answer']+1:1;
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
            ->join('questions as q','answers.cod_question','=','q.cod_question')
            ->join('sections as s','q.cod_section','=','s.cod_section')
            ->join('surveys as s2','s2.cod_survey','=','s.cod_survey')
            ->where('s2.cod_survey','=',$cod_survey)
            ->groupBy('cod_answer')->get());
    }
}
