<?php
class questionnaire_step1 extends bas_frmx_form{
	private $level=0;
	private $maxlevel=1;
	private $questionnaire=1;
	private $currentQuestion=0;
	private $answers = array();
	
	public function OnLoad(){
		parent::OnLoad();
		
		
		$this->toolbar= new bas_frmx_toolbar('close');
		$this->title= 'Cuestionario';
		
		$qry_maxlevel = "select max(level) as maxlevel from questions left join questionByquestionnaire on questionByquestionnaire.question = questions.id where questionByquestionnaire.questionnaire = {$this->questionnaire} ";
		$qry= new bas_sql_myquery($qry_maxlevel);
		
		$this->maxlevel = $qry->result['maxlevel'];

		$this->buttonbar = new bas_frmx_buttonbar();

		$this->createFrame();
	}

	private function createFrame(){
		$answer = "select questionByquestionnaire.question as idquestion,questions.question as question,questionByquestionnaire.level as level from questions left join questionByquestionnaire on questionByquestionnaire.question = questions.id where questionByquestionnaire.questionnaire = {$this->questionnaire} and questionByquestionnaire.level > {$this->level} order by level asc limit 1";
		$qry= new bas_sql_myquery($answer);
		$this->level = $qry->result["level"];
		$this->currentQuestion = $qry->result["idquestion"];
		$caption = $qry->result["question"];
	
		$answer = "select answer as answer from questionByquestionnaire left join answerByquestion on answerByquestion.question = questionByquestionnaire.question where questionByquestionnaire.level = {$this->level} limit 1";
		
		$qry= new bas_sql_myquery($answer);
		
		if (is_null($qry->result['answer'])){ // Se trata de una pregunta a rellenar
			$this->createCard($caption);
		}
		else{  // Es una pregunta con posibles respuestas.
			$this->createGrid($caption);
		}
	}
	private function createGrid($caption){
		
		$this->buttonbar = new bas_frmx_buttonbar();
		$answer = "select caption,answerByquestion.level from questionByquestionnaire left join answerByquestion on answerByquestion.question = questionByquestionnaire.question where questionByquestionnaire.level = {$this->level} order by answerByquestion.level asc";
		$options = array();
		$ds = new bas_sql_myqrydataset($answer);
		$rec = $ds->reset();
		while ($rec){ // obtenemos los periodos por factura
			$options [] = $rec["caption"];
			$rec = $ds->next();			
		}	
		$ds->close();

		// id,obj,y,x,width,height
		$frame= new bas_frmx_gridFrame("questions", array("Cuestionario"));
		$frame->setHeader($caption);
		
		$question= new bas_frmx_panelGrid("items",array('width'=>count($options),'height'=>1));
		$question->setEvent("sel_answer");
		$ind=1;
		foreach($options as $item){
			$question->addComponent(1,$ind,$item,$item);
			$ind++;
		}
		
		$frame->addComponent("qty"	,$question,1,1, 4,5);
		$this->addFrame($frame);
	}
	
	private function createCard($caption){
		$this->buttonbar->addframeAction('aceptar','questions');
		
		$card=new bas_frmx_cardframe('questions');
		
		$card->query->add('temp');

		$card->query->addcol('question',$caption,'temp',true);
		$card->tabs= array('General');
		$card->addComponent('General', 1, 1, 4, 1, 'question');
		$this->addFrame($card);

	}
	
	private function sendForm(){
		global $_LOG ;
		$proc = new bas_sqlx_connection();
		$proc->call("hist","create",array($this->questionnaire));
		if ($proc->success){
// 			$hist = $proc->message;
			foreach($this->answers as $item){
				$proc->call("hist","answerInsert",array($item["id"],$item["answer"]));
				if (! $proc->success){
					break;
				}
	// 			$_LOG->log("Pregunta {$item["id"]}, Respuesta: {$item["answer"]}");
			}
			if ($proc->success){
				$proc->commit();
			}
			else{
				$proc->rollback();
				$msg= new bas_html_messageBox(false, 'Atención!',$proc->message);
				echo $msg->jscommand();
			}
		}
		else{
			$proc->rollback();
			$msg= new bas_html_messageBox(false, 'Atención!',$proc->message);
			echo $msg->jscommand();
		}
		$proc->close();
	}
	
	
	private function insertItem($data=""){
		if (is_null($data)) $data = "";
		$this->answers [] = array('id'=>$this->currentQuestion,'answer'=>$data);
		if ($this->level == $this->maxlevel){ 
			$this->sendForm();
			return true;
		}
		else{
			$this->createFrame();
			$this->OnPaint('jscommand');
			return false;
		}
	}
	
	public function OnAction($action, $data=""){
		parent::OnAction($action,$data);
		switch($action){
			case 'close': return array('close');
			case 'aceptar':
					if ($this->insertItem($data["question"]) ) return array('close');

				break;
			case 'prevGrid':case 'nextGrid':
				$this->frames[$data["idFrame"]]->OnAction($action,$data);
// 				$this->frames[$data["idFrame"]]->OnAction($action,$data);
				
				$this->OnPaint("jscommand");
				break;
			case 'sel_answer':
			
				 if ($this->insertItem($data["item"]) ) return array('close');
				
// 				$msg= new bas_html_messageBox(false, 'Item!!',$data["item"]);
// 				echo $msg->jscommand();
				
				break;
			case 'select_group':
				$this->frames["buttons"]->getObjComponent("item")->query->setfilter($data["item"],"itemGroup");
				$this->frames["buttons"]->getObjComponent("item")->Reload();
				$this->OnPaint("jscommand");				
				break;
		}
	}
}
