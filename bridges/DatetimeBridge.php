<?php

	class DatetimeBridge extends AbstractYCMBridge {

		public function render() {
			
			$form = $this->getForm();
			$model = $this->getModel();
			$attribute = $this->getAttribute();
			$context = $this->getContext();
			$controller = $this->getController();

			$lang = Yii::app()->language;

			if ($form->type==TbActiveForm::TYPE_HORIZONTAL) {
				echo '<div class="control-group">';
				echo $form->labelEx($model,$attribute,array('class'=>'control-label'));
				echo '<div class="controls">';
			} else {
				echo $form->labelEx($model,$attribute);
			}
			echo '<div class="input-prepend"><span class="add-on"><i class="icon-calendar"></i></span>';
			$options=array(
				'model'=>$model,
				'attribute'=>$attribute,
				'language'=>$lang,
				'mode'=>'datetime',
				'htmlOptions'=>array(
					'class'=>'size-medium',
				),
				'options'=>array(
					'dateFormat'=>'yy-mm-dd',
					'timeFormat'=>'hh:mm:ss',
					'showSecond'=>true,
					//'stepHour'=>'1',
					//'stepMinute'=>'10',
					//'stepSecond'=>'60',
				),
			);
			$options=$context->getAttributeOptions($attribute,$options);
			$controller->widget($context->name.'.extensions.jui.EJuiDateTimePicker',$options);
			echo '</div>';
			echo $form->error($model,$attribute);
			if ($form->type==TbActiveForm::TYPE_HORIZONTAL) {
				echo '</div></div>';
			}

		}

	}