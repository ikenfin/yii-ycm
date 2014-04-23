<?php

	class TypeHeadBridge extends AbstractYCMBridge {

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
				'model'=>$model,
				'attribute'=>$attribute,
				'htmlOptions'=>array(
					'class'=>'span5',
					'autocomplete'=>'off',
				),
				'options'=>array(
					'name'=>'typeahead',
					'source'=>$context->getAttributeChoices($model,$attribute),
					'matcher'=>"js:function(item) {
						return ~item.toLowerCase().indexOf(this.query.toLowerCase());
					}",
				),
			);
			$options=$context->getAttributeOptions($attribute,$options,true);
			$controller->widget('bootstrap.widgets.TbTypeahead',$options);
			echo $form->error($model,$attribute);
			if ($form->type==TbActiveForm::TYPE_HORIZONTAL) {
				echo '</div></div>';
			}

		}

	}