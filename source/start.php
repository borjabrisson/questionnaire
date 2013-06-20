<?php
class questionnaire_start extends bas_frmx_form{
	private $menu;

	public function OnLoad(){
		parent::OnLoad();
		
		$menu= new bas_frmx_menu("main","Main Menu");
		$menu->add('Cuestionario', 'questionnaire_step1');
		
		$this->addFrame($menu);
		
	}
	
	public function OnAction($action, $data){
		if ($ret = parent::OnAction($action,$data)) return $ret;
		return array('open', "$action");
		
	}
	
	public function getBreadCrumbCaption(){ return "Main"; }
	
}


?>


