<?php
class questionnaire_recordList extends bas_frmx_form {

	public function OnLoad(){
		parent::OnLoad();
		
		$this->title = 'Cuestionarios Realizados';		
	
		$this->toolbar = new bas_frmx_toolbar('csv,pdf,close');
		
		// ### Definicion del buttonbar
		$this->buttonbar= new bas_frmx_buttonbar();
		$this->buttonbar->addAction('salir');
		
		$list = new bas_frmx_listframe('lista_cuestionarios',"Resultados");
		
		$list->query->add('answerRecord');
		$list->query->setkey(array('hist','question'));
		
// 		$list->query->addcol('id','Identificador', 'answerRecord',true);
		$list->query->addcol('hist','Registro','answerRecord',false);

		$list->query->addcol('question','pregunta','answerRecord',false);
		$list->query->addcol('answer','respuesta','answerRecord',false);
		
		$list->query->addrelated('historic','hist','answerRecord');
		$list->query->addcol('questionnaire','cuestionario','historic',false);
// 		$list->query->addcondition("questionnaire = 1");
		$width=100; $height=1;
		
		$list->addComponent($width, $height,"hist");
		$list->addComponent($width, $height,"questionnaire");
// 		$list->addComponent($width, $height,"question");
// 		$list->addComponent($width, $height,"answer");


		$list->createRecord();
        $list->dataset->setPivot("question","answer");
		$this->addFrame($list);

// 		$this->createPivot();
	}
	public function OnRefresh(){
        $this->frames['lista_cuestionarios']->Reload();
    }
	
	private function createPivot($questionnaire=0){
		global $_LOG;
		$width=100; $height=1;
		$qry = "select questions.id as id, questions.question as caption from questionByquestionnaire left join questions on questions.id = questionByquestionnaire.question where questionnaire=$questionnaire";

        $ds = new bas_sql_myqrydataset($qry);       
        $rec = $ds->reset();
        
        while ($rec){ // obtenemos los periodos por factura
            $this->frames["lista_cuestionarios"]->query->addcol($rec["id"],$rec["caption"],'hist');
            $this->frames["lista_cuestionarios"]->setAttr($rec["id"],'selected',false);
            
            $this->frames["lista_cuestionarios"]->addComponent($width, $height,$rec["id"]);
            $rec = $ds->next();         
        }   
        $ds->close();
         $this->frames["lista_cuestionarios"]->query->setfilter($questionnaire,"questionnaire");
        $this->frames["lista_cuestionarios"]->Reload();
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
            case "init":
				$this->createPivot($data["questionnaire"]);
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
		}
	}
}
?>
