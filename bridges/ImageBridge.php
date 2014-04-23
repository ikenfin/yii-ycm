<?php

	class ImageBridge extends AbstractYCMBridge {

		public function render() {

			$form = $this->getForm();
			$model = $this->getModel();
			$attribute = $this->getAttribute();
			$context = $this->getContext();
			$controller = $this->getController();

			$options=array(
				'class'=>'span5',
			);
			$options=$context->getAttributeOptions($attribute,$options);
			if (!$model->isNewRecord && !empty($model->$attribute)) {
				$modalName='modal-image-'.$attribute;
				$image=CHtml::image($model->getFileUrl($attribute),Yii::t('YcmModule.ycm','Image'),array(
					'class'=>'modal-image')
				);
				ob_start();
				$controller->beginWidget('bootstrap.widgets.TbModal',array('id'=>$modalName));
				echo '<div class="modal-header"><a class="close" data-dismiss="modal">&times;</a><h4>';
				echo Yii::t('YcmModule.ycm','Image preview').'</h4></div>';
				echo '<div class="modal-body">'.$image.'</div>';
				$this->controller->endWidget();
				echo '<p>';
				$controller->widget('bootstrap.widgets.TbButton',array(
					'label'=>Yii::t('YcmModule.ycm','Preview'),
					'type'=>'',
					'htmlOptions'=>array(
						'data-toggle'=>'modal',
						'data-target'=>'#'.$modalName,
					),
				));
				echo '</p>';
				$html=ob_get_clean();
				$options['hint']=$html;
			}
			echo $form->fileFieldRow($model,$attribute,$options);
			
		}

	}