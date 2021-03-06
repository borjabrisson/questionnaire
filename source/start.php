<?php
class questionnaire_start extends bas_frmx_form{
	private $menu;

	public function OnLoad(){
		parent::OnLoad();
		
		$menu= new bas_frmx_menu("main","Main Menu");
		$menu->add('Cuestionario', 'questionnaire_step1');
		
		$this->addFrame($menu);
		
		$menu= new bas_frmx_menu("mainQuestionnaire","Main de Cuestionarios");
		$menu->add('Lista de cuestionarios', 'questionnaire_questionnaireList');
		$menu->add('Lista de preguntas', 'questionnaire_questionList');
		$this->addFrame($menu);
		
		$menu= new bas_frmx_menu("mainRecord","Menú de Registros");
		$menu->add('Registros realizados', 'questionnaire_recordList');
		$this->addFrame($menu);
	}
	
	public function OnAction($action, $data){
		if ($ret = parent::OnAction($action,$data)) return $ret;
		
		
		if($action == "questionnaire_recordList")return array('open', "questionnaire_questionnaireList","recordMode");
		if($action == "questionnaire_step1")return array('open', "questionnaire_questionnaireList","executeMode");

		return array('open', "$action");
		
	}
	
	public function getBreadCrumbCaption(){ return "Main"; }
	
}


?>


