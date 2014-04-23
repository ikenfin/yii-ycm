<?php

	class FileBridge extends AbstractYCMBridge {

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
				ob_start();
				echo '<p>';
				$controller->widget('bootstrap.widgets.TbButton',array(
					'label'=>Yii::t('YcmModule.ycm','Download'),
					'type'=>'',
					'url'=>$model->getFileUrl($attribute),
				));
				echo '</p>';
				$html=ob_get_clean();
				$options['hint']=$html;
			}
			echo $form->fileFieldRow($model,$attribute,$options);
			
		}

	}