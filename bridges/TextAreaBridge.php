<?php

	class TextAreaBridge extends AbstractYCMBridge {

		public function render() {

			$form = $this->getForm();
			$model = $this->getModel();
			$attribute = $this->getAttribute();
			$context = $this->getContext();

			$options=array(
				'rows'=>5,
				'cols'=>50,
				'class'=>'span8',
			);
			$options=$context->getAttributeOptions($attribute,$options);
			echo $form->textAreaRow($model,$attribute,$options);
			
		}

	}