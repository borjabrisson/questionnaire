<?php
class questionnaire_questionCard extends bas_frmx_form{ 
	private $type="item";
	public function OnLoad(){
		parent::OnLoad();
		
		$this->toolbar = new bas_frmx_toolbar('pdf,close');
		
		$this->buttonbar= new bas_frmx_buttonbar();
		$this->buttonbar->addframeAction('aceptar','ficha_pregunta');
		$this->buttonbar->addAction('cancelar');
		
		$card=new bas_frmx_cardframe('ficha_pregunta',array("Pregunta"),array("width"=>4,"height"=>3));
		
		$card->query->add('questions');
		$card->query->setkey(array('id'));
		
		$card->query->addcol('id','Identificador', 'questions',true);
		$card->query->addcol('question','Pregunta','questions',false);

		$card->query->addcol('language','Idioma','questions',false);
		$card->query->addcol('multivalue','Multievaluada','questions',false,"","boolean");
		
		
		$card->addComponent('Pregunta', 1, 1, 2, 2, 'question');
		$card->addComponent('Pregunta', 3, 1, 2, 2, 'multivalue');	
		
		$card->createRecord();
		$this->addFrame($card);
	}
	
	public function OnAction($action, $data){
		global $ICONFIG;
		
		if ($ret =parent::OnAction($action, $data)) return $ret;
		switch($action){
			case 'cancelar': return array('close');
			case 'aceptar': 
				$data["type"] = $this->type;
				if(!isset($data["price"]) ) $data["price"] = null;
				if ($this->frames['ficha_pregunta']->GetMode() == "new"){
                     $proc = new bas_sql_myprocedure('item_new', array( $data['item'],$data['type'],$data['itemGroup'],$data['description'],$data['price']));
                }else{
					 $proc = new bas_sql_myprocedure('item_edit', array( $data['item'],$data['type'],$data['itemGroup'],$data['description'],$data['price'],$this->frames['ficha_pregunta']->record->original["item"]));
                }   
                if ($proc->success){
					return array('close');
                }
                else{
                    $msg= new bas_html_messageBox(false, 'error', $proc->errormsg);
                    echo $msg->jscommand();
                }  
				break;
			case 'new': 
				$this->title = "Nueva Pregunta"; 
				$this->frames["ficha_pregunta"]->SetMode("new");
				break;
			case 'edit':			
				$this->title = "Editar Pregunta"; 
				
				$this->frames["ficha_pregunta"]->SetMode("edit");
				$this->frames["ficha_pregunta"]->query->setfilterRecord($data);
				$this->frames["ficha_pregunta"]->setRecord();
				
				break;
			case 'setvalues':
				$this->frames["ficha_pregunta"]->saveData($data);
				break;
			case 'setfilter':
				$this->frames["ficha_pregunta"]->query->setfilterRecord($data);
				$this->frames["ficha_pregunta"]->Reload();
				break;				
			case 'lookup':
				$this->frames["ficha_pregunta"]->saveData($data);
				return (array('open',$data["lookup"],'lookup',array()));
			break;
			
			case 'filtro':
				$save[] =  array('id'=> "setfilterRecord", 'type'=>'command', 'caption'=>"guardar", 'description'=>"guardar");
				$save[] =  array('id'=> "cancel", 'type'=>'command', 'caption'=>"cancelar", 'description'=>"Cancelar");
				
				$login= new bas_html_filterBox($this->frames["ficha_pregunta"]->query, "Filtros",$save);
				echo $login->jscommand();
			break;
			
			case 'ok':case 'cancel':
				echo '{"command": "void"}';//. substr(json_encode($this),1);
			break;
		}
	}


}
?>
