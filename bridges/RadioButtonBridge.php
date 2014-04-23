<?php 

	class RadioButtonBridge extends AbstractYCMBridge {

		public function render() {

			$form = $this->getForm();
			$model = $this->getModel();
			$attribute = $this->getAttribute();
			$context = $this->getContext();

			$options=array();
			$options=$context->getAttributeOptions($attribute,$options);
			echo $form->radioButtonListRow($model,$attribute,$context->getAttributeChoices($model,$attribute),$options);
			
		}

	}