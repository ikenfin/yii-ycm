<?php
	
	class TextFieldBridge extends AbstractYCMBridge {

		protected function beforeRenderWidget() {}

		public function render() {
			
			$form = $this->getForm();
			$model = $this->getModel();
			$attribute = $this->getAttribute();
			$context = $this->getContext();

			$options = [
				'class'=>'span5',
			];
			$options = $context->getAttributeOptions($attribute, $options);

			echo $form->textFieldRow($model,$attribute,$options);
			return '';
		}

	}