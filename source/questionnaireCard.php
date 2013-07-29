<?php
class questionnaire_questionnaireCard extends bas_frmx_form{ 
	private $type="item";
	public function OnLoad(){
		parent::OnLoad();
		
		$this->toolbar = new bas_frmx_toolbar('pdf,close');
		
		$this->buttonbar= new bas_frmx_buttonbar();
		$this->buttonbar->addframeAction('aceptar','ficha_cuestionario');
		$this->buttonbar->addAction('cancelar');
		
		$card=new bas_frmx_cardframe('ficha_cuestionario',array("Cuestionario"),array("width"=>4,"height"=>2));
		
		$card->query->add('questionnaire');
		$card->query->setkey(array('id'));
		
		$card->query->addcol('id','Identificador', 'questionnaire',true);
		$card->query->addcol('description','Descripción','questionnaire',false);

		$card->addComponent('Cuestionario', 1, 1, 3, 1, 'description');
			
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
				if ($this->frames['ficha_cuestionario']->GetMode() == "new"){
                     $proc = new bas_sql_myprocedure('questionnaire_new', array( $data['description']));
                }else{
					 $proc = new bas_sql_myprocedure('questionnaire_edit', array($this->frames['ficha_cuestionario']->record->original["id"],$data['description']));
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
				$this->title = "Nuevo Cuestionario"; 
				$this->frames["ficha_cuestionario"]->SetMode("new");
				break;
			case 'edit':
				$this->title = "Editar Cuestionario"; 

				$this->frames["ficha_cuestionario"]->SetMode("edit");
				$this->frames["ficha_cuestionario"]->query->setfilterRecord($data);
				$this->frames["ficha_cuestionario"]->setRecord();
				break;	
			case 'setvalues':
				$this->frames["ficha_cuestionario"]->saveData($data);
				break;
			case 'setfilter':
				$this->frames["ficha_cuestionario"]->query->setfilterRecord($data);
				$this->frames["ficha_cuestionario"]->Reload();
				break;				
			case 'lookup':
				$this->frames["ficha_cuestionario"]->saveData($data);
				return (array('open',$data["lookup"],'lookup',array()));
			break;
			
			case 'filtro':
				$save[] =  array('id'=> "setfilterRecord", 'type'=>'command', 'caption'=>"guardar", 'description'=>"guardar");
				$save[] =  array('id'=> "cancel", 'type'=>'command', 'caption'=>"cancelar", 'description'=>"Cancelar");
				
				$login= new bas_html_filterBox($this->frames["ficha_cuestionario"]->query, "Filtros",$save);
				echo $login->jscommand();
			break;
			
			case 'ok':case 'cancel':
				echo '{"command": "void"}';//. substr(json_encode($this),1);
			break;
		}
	}


}
?>
