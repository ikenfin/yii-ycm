<?php

	class ChosenMultipleBridge extends AbstractYCMBridge {

		public function render() {
			
			$form = $this->getForm();
			$model = $this->getModel();
			$attribute = $this->getAttribute();
			$context = $this->getContext();
			$controller = $this->getController();

			$options=array(
				'data-placeholder'=>Yii::t('YcmModule.ycm',
					'Choose {name}',
					array('{name}'=>$model->getAttributeLabel($attribute))
				),
				'multiple'=>'multiple',
				'class'=>'span5 chosen-select',
			);
			$options=$context->getAttributeOptions($attribute,$options);
			$controller->widget($context->name.'.extensions.chosen.EChosenWidget');
			echo $form->dropDownListRow($model,$attribute,$context->getAttributeChoices($model,$attribute),$options);
			
		}

	}