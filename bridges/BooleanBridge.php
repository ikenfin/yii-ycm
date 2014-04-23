<?php

	class BooleanBridge extends AbstractYCMBridge {

		public function render() {

			$form = $this->getForm();
			$model = $this->getModel();
			$attribute = $this->getAttribute();
			$context = $this->getContext();

			$options=array();
			$options=$context->getAttributeOptions($attribute,$options);
			echo $form->checkboxRow($model,$attribute,$options);
			
		}
	}