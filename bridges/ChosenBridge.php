<?php

	class ChosenBridge extends AbstractYCMBridge {

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
				'class'=>'span5 chosen-select',
			);
			$options=$context->getAttributeOptions($attribute,$options);
			$context->getController()->widget($context->name.'.extensions.chosen.EChosenWidget');
			echo $form->dropDownListRow($model,$attribute,$context->getAttributeChoices($model,$attribute),$options);
			
		}

	}