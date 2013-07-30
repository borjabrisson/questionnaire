<?php
class questionnaire_answerList extends bas_frmx_form {
	private $question;
	public function OnLoad(){
		parent::OnLoad();
		
		$this->title = 'Preguntas';		
	
		$this->toolbar = new bas_frmx_toolbar('filtro,pdf,close');
		
		// ### Definicion del buttonbar
		$this->buttonbar= new bas_frmx_buttonbar();
		$this->buttonbar->addAction('borrar');$this->buttonbar->addAction('nuevo'); $this->buttonbar->addAction('editar');	$this->buttonbar->addAction('salir');
		
		$list = new bas_frmx_listframe('lista_respuestas',"Preguntas Existentes");
		
		$list->query->add('answerByquestion');
		$list->query->setkey(array('question','answer'));
		
		$list->query->addcol('question','Pregunta','answerByquestion',false);
		$list->query->addcol('answer','Respuesta','answerByquestion',false);

		$list->query->addcol('caption','Caption','answerByquestion',false);
		$list->query->addcol('level','orden','answerByquestion',false,"","number");

		
		$width=300; $height=1;
		
// 		$list->addComponent($width, $height,"question");
// 		$list->addComponent($width, $height,"answer");
		$list->addComponent($width, $height,"caption");
		$list->addComponent($width, $height,"level");

		
		//$list->autoSize(); # El autosize es incompatible con el loadconfig.
		$list->setRecord();
		$this->addFrame($list);
	}
	public function OnRefresh(){
        $this->frames['lista_respuestas']->Reload();
    }
	
	
	
	private function OnFilter(){
		$query = new bas_sqlx_querydef();
		
		$query->addcol('item','Nombre', 'item',true);
		$query->addcol('itemGroup','Grupo','item',false);

		$query->addcol('description','Descripción','item',false);
		$query->addcol('price','Precio','item',false);
        
        $filters = $this->frames["lista_respuestas"]->query->getfilters();
        $query->setfilterRecord($filters);    

		return $query;
    }
	
	
	public function OnAction($action, $data){
		if ($ret = parent::OnAction($action,$data)) return $ret;
		if (isset($data['selected'])){
			$this->frames["lista_respuestas"]->setSelected($data['selected']);
		}
		switch ($action){
            case 'salir': case 'cancelar':
                return array("close");
            break;
			case 'nuevo':
                return array('open','questionnaire_answerCard',"new",array("question"=>$this->question));
            break;
            case 'editar':
                if (isset($data['selected'])){
                    $aux = $this->frames["lista_respuestas"]->getkeySelected();
                    return array('open','questionnaire_answerCard','edit',$aux);
                }
                else{
                    $msg= new bas_html_messageBox(false, 'Atención', "Seleccione una tarea");
                    echo $msg->jscommand();
                }
            break;
            
            case 'borrar':
				 if (isset($data['selected'])){
					$data = $this->frames["lista_respuestas"]->getkeySelected();

                    $proc = new bas_sql_myprocedure('answer_delete', $data);
					if ($proc->success){
						$this->frames["lista_respuestas"]->Reload(true);
					}
					else{
						$msg= new bas_html_messageBox(false, 'error', $proc->errormsg);
						echo $msg->jscommand();
					} 
                }
                else{
                    $msg= new bas_html_messageBox(false, 'Atención', "Seleccione una tarea");
                    echo $msg->jscommand();
                }
            break;


            case 'filtro':
                $save[] =  array('id'=> "setfilterRecord", 'type'=>'command', 'caption'=>"Aceptar", 'description'=>"guardar");
				$save[] =  array('id'=> "cancel", 'type'=>'command', 'caption'=>"cancelar", 'description'=>"Cancelar");
				
				$query = $this->OnFilter();
                $login= new bas_html_filterBox($query, "Filtros",$save);
				echo $login->jscommand();
            break;
            
            case 'cancel':
                echo '{"command": "void",'. substr(json_encode($this),1);
            break;
            
            case 'setfilterRecord':
                $this->frames['lista_respuestas']->query->setfilterRecord($data);
                $this->frames['lista_respuestas']->Reload(true);
            break;
            case 'setFilter';
				$this->question = $data["question"];
                $this->frames['lista_respuestas']->query->setfilterRecord($data);
                $this->frames['lista_respuestas']->Reload();
            break;
            
            case "lookup":
                $this->buttonbar= new bas_frmx_buttonbar();
                $this->buttonbar->addAction('aceptar'); $this->buttonbar->addAction('cancelar');
            break;
            case "aceptar":
                if (isset($data['selected'])){
                    $aux = $this->frames["lista_respuestas"]->getSelected();
                    return array("return","setvalues",$aux[0]);
                }
                else{
                    $msg= new bas_html_messageBox(false, 'Atención', "Seleccione una tarea");
                    echo $msg->jscommand();
                }
                
            break;
		}
	}
}
?>
