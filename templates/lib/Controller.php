<?php
		
	abstract class Controller{

		function __construct(){
		}
		
		function __before($params,$action){}
		function __after($params,$action){}

		static function respond($array){
			echo json_encode($array);
		}

		static function redirect($route){
			header("Location: ".$route);
		}

		function partial($name){
			include __DIR__."/../views/_partials/".$name.".html";
		}

		function render($view = ""){
			$this->view = $view;
			include __DIR__."/../views/layouts/".$this->layout.".html";
		}

		function view(){
			if(strlen($this->view) > 0) {
				$ctrl = explode("#",$this->view)[0];
				$view = explode("#",$this->view)[1];
				include __DIR__."/../views/$ctrl/$view.html";
			}
		}

	}

?>
