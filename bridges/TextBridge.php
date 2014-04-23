<?php

	class TextBridge extends AbstractYCMBridge {

		public function render() {

			$form = $this->getForm();
			$model = $this->getModel();
			$attribute = $this->getAttribute();
			$context = $this->getContext();

			$options=array(
				'class'=>'span5',
			);
			$options=$context->getAttributeOptions($attribute,$options);
			echo $form->textFieldRow($model,$attribute,$options);
			
		}

	}