<?php
class questionnaire_questionnaireList extends bas_frmx_form {

	public function OnLoad(){
		parent::OnLoad();
		
		$this->title = 'Cuestionarios';		
	
		$this->toolbar = new bas_frmx_toolbar('filtro,pdf,close');
		
		// ### Definicion del buttonbar
		$this->buttonbar= new bas_frmx_buttonbar();
		$this->buttonbar->addAction('question',"Preguntas");$this->buttonbar->addAction('borrar');$this->buttonbar->addAction('nuevo'); $this->buttonbar->addAction('editar');	$this->buttonbar->addAction('salir');
		
		$list = new bas_frmx_listframe('lista_cuestionarios',"Cuestionarios Existentes");
		
		$list->query->add('questionnaire');
		$list->query->setkey(array('id'));
		
		$list->query->addcol('id','Identificador', 'questionnaire',true);
		$list->query->addcol('description','Descripción','questionnaire',false);

				
		$width=100; $height=1;
		
		$list->addComponent($width, $height,"id");
		$list->addComponent($width, $height,"description");
		
		//$list->autoSize(); # El autosize es incompatible con el loadconfig.
		$list->setRecord();
		$this->addFrame($list);
	}
	public function OnRefresh(){
        $this->frames['lista_cuestionarios']->Reload();
    }
	
	
	
	private function OnFilter(){
		$query = new bas_sqlx_querydef();
		
		$query->addcol('item','Nombre', 'item',true);
		$query->addcol('itemGroup','Grupo','item',false);

		$query->addcol('description','Descripción','item',false);
		$query->addcol('price','Precio','item',false);
        
        $filters = $this->frames["lista_cuestionarios"]->query->getfilters();
        $query->setfilterRecord($filters);    

		return $query;
    }
	
	
	public function OnAction($action, $data){
		if ($ret = parent::OnAction($action,$data)) return $ret;
		if (isset($data['selected'])){
			$this->frames["lista_cuestionarios"]->setSelected($data['selected']);
		}
		switch ($action){
            case 'salir': case 'cancelar':
                return array("close");
            break;
			case 'nuevo':
                return array('open','questionnaire_questionnaireCard',"new");
            break;
            case 'editar':
                if (isset($data['selected'])){
                    $aux = $this->frames["lista_cuestionarios"]->getkeySelected();
                    return array('open','questionnaire_questionnaireCard','edit',$aux);
                }
                else{
                    $msg= new bas_html_messageBox(false, 'Atención', "Seleccione una tarea");
                    echo $msg->jscommand();
                }
            break;
            
            case 'borrar':
				 if (isset($data['selected'])){
					$data = $this->frames["lista_cuestionarios"]->getkeySelected();

                    $proc = new bas_sql_myprocedure('questionnaire_delete', $data);
					if ($proc->success){
						$this->frames["lista_cuestionarios"]->Reload(true);
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
                $this->frames['lista_cuestionarios']->query->setfilterRecord($data);   
                $this->frames['lista_cuestionarios']->Reload(true);
            break;
            case 'setFilter';
                $this->frames['lista_cuestionarios']->query->setfilterRecord($data);   
                $this->frames['lista_cuestionarios']->Reload();
            break;
            
            case "question":
            
				if (isset($data['selected'])){
                    $aux = $this->frames["lista_cuestionarios"]->getkeySelected();
                    return array('open','questionnaire_questionList','setQuestionnaire',array("questionnaire"=>$aux["id"]));
                }
                else{
                    $msg= new bas_html_messageBox(false, 'Atención', "Seleccione una tarea");
                    echo $msg->jscommand();
                }

            break;
            
            case "lookup":
                $this->buttonbar= new bas_frmx_buttonbar();
                $this->buttonbar->addAction('aceptar'); $this->buttonbar->addAction('cancelar');
            break;
            case "aceptar":
                if (isset($data['selected'])){
                    $aux = $this->frames["lista_cuestionarios"]->getSelected();
                    return array("return","setvalues",$aux[0]);
                }
                else{
                    $msg= new bas_html_messageBox(false, 'Atención', "Seleccione una tarea");
                    echo $msg->jscommand();
                }
                
            break;
            
            case "recordMode":
				$this->buttonbar= new bas_frmx_buttonbar();
                $this->buttonbar->addAction("historic",'Ver registros');
            break;
            
            case "historic":
                if (isset($data['selected'])){
                    $aux = $this->frames["lista_cuestionarios"]->getkeySelected();
                    return array('open','questionnaire_recordList','init',array("questionnaire"=>$aux["id"]));
                }
                else{
                    $msg= new bas_html_messageBox(false, 'Atención', "Seleccione una tarea");
                    echo $msg->jscommand();
                }
                
            break;
            
             case "executeMode":
				$this->buttonbar= new bas_frmx_buttonbar();
                $this->buttonbar->addAction("execute",'Realizar');
            break;
            
            case "execute":
                if (isset($data['selected'])){
                    $aux = $this->frames["lista_cuestionarios"]->getkeySelected();
                    return array('open','questionnaire_step1','init',array("questionnaire"=>$aux["id"]));
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
