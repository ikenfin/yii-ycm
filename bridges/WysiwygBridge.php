<?php

	class WysiwygBridge extends AbstractYCMBridge {

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
			$options=array(
				'model'=>$model,
				'attribute'=>$attribute,
				'options'=>array(
					'lang'=>$lang,
					'buttons'=>array(
						'formatting','|','bold','italic','deleted','|',
						'unorderedlist','orderedlist','outdent','indent','|',
						'image','link','|','html',
					),
				),
			);
			$options=$context->getAttributeOptions($attribute,$options);
			if ($context->redactorUpload===true) {
				$redactorOptions=array(
					'options'=>array(
						'imageUpload'=>Yii::app()->createUrl($context->name.'/model/redactorImageUpload',array(
							'name'=>get_class($model),
							'attr'=>$attribute)
						),
						'imageGetJson'=>Yii::app()->createUrl($context->name.'/model/redactorImageList',array(
							'name'=>get_class($model),
							'attr'=>$attribute)
						),
						'imageUploadErrorCallback'=>new CJavaScriptExpression(
							'function(obj,json) { alert(json.error); }'
						),
					),
				);
				$options=array_merge_recursive($options,$redactorOptions);
			}
			$controller->widget($context->name.'.extensions.redactor.ERedactorWidget',$options);
			echo $form->error($model,$attribute);
			if ($form->type==TbActiveForm::TYPE_HORIZONTAL) {
				echo '</div></div>';
			}
		}
	}