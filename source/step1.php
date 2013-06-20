<?php
class questionnaire_step1 extends bas_frmx_form{
	private $level=0;
	private $maxlevel=1;
	private $questionnaire=1;
	private $answers = array();
	
	public function OnLoad(){
		parent::OnLoad();
		
		
		$this->toolbar= new bas_frmx_toolbar('close');
		$this->title= 'The estheticien';
		
		$qry_maxlevel = "select count(*) as maxlevel from questions left join questionByquestionnaire on questionByquestionnaire.question = questions.id where questionByquestionnaire.questionnaire = {$this->questionnaire} ";
		$qry= new bas_sql_myquery($qry_maxlevel);
		
		$this->maxlevel = $qry->result['maxlevel'];
		
		

		$this->buttonbar = new bas_frmx_buttonbar();

		$this->createFrame();
	}

	private function createFrame(){
		$answer = "select questions.question as question,questionByquestionnaire.level as level from questions left join questionByquestionnaire on questionByquestionnaire.question = questions.id where questionByquestionnaire.questionnaire = {$this->questionnaire} and questionByquestionnaire.level > {$this->level} order by level asc limit 1";
		$qry= new bas_sql_myquery($answer);
		$this->level = $qry->result["level"];
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
	
	public function OnAction($action, $data=""){
		parent::OnAction($action,$data);
		switch($action){
			case 'close': return array('close');
			case 'aceptar':
					$this->createFrame();
					$this->OnPaint('jscommand');
				break;
			case 'prevGrid':case 'nextGrid':
				$this->frames[$data["idFrame"]]->OnAction($action,$data);
// 				$this->frames[$data["idFrame"]]->OnAction($action,$data);
				
				$this->OnPaint("jscommand");
				break;
			case 'sel_answer': 
				$this->createFrame();
				$this->OnPaint('jscommand');
				
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
