<?php

	class TaggableBridge extends AbstractYCMBridge {

		public function render() {

			$form = $this->getForm();
			$model = $this->getModel();
			$attribute = $this->getAttribute();
			$context = $this->getContext();
			$controller = $this->getController();

			if ($form->type==TbActiveForm::TYPE_HORIZONTAL) {
				echo '<div class="control-group">';
				echo $form->labelEx($model,$attribute,array('class'=>'control-label'));
				echo '<div class="controls">';
			} else {
				echo $form->labelEx($model,$attribute);
			}
			$options=array(
				'name'=>$attribute,
				'value'=>$model->$attribute->toString(),
				'url'=>Yii::app()->createUrl($context->name.'/model/suggestTags',array(
					'name'=>get_class($model),
					'attr'=>$attribute,
				)),
				'multiple'=>true,
				'mustMatch'=>false,
				'matchCase'=>false,
				'htmlOptions'=>array(
					'size'=>50,
					'class'=>'span5',
				),
			);
			$options=$context->getAttributeOptions($attribute,$options);
			$controller->widget('CAutoComplete',$options);
			echo '<span class="help-inline">'.Yii::t('YcmModule.ycm','Separate words with commas.').'</span>';
			echo $form->error($model,$attribute);
			if ($form->type==TbActiveForm::TYPE_HORIZONTAL) {
				echo '</div></div>';
			}
		}

	}