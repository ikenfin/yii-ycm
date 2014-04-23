<?php

	class DropDownBridge extends AbstractYCMBridge {

		public function render() {

			$form = $this->getForm();
			$model = $this->getModel();
			$attribute = $this->getAttribute();
			$context = $this->getContext();

			$options=array(
				'empty'=>Yii::t('YcmModule.ycm',
					'Choose {name}',
					array('{name}'=>$model->getAttributeLabel($attribute))
				),
				'class'=>'span5',
			);
			$options=$context->getAttributeOptions($attribute,$options);
			echo $form->dropDownListRow($model,$attribute,$context->getAttributeChoices($model,$attribute),$options);
			
		}

	}