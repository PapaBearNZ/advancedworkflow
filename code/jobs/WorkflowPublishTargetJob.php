<?php
/**
 * A queued job that publishes a target after a delay.
 *
 * @package advancedworkflow
 */
class WorkflowPublishTargetJob extends AbstractQueuedJob {

	public function __construct($obj = null, $type = null) {
		if ($obj) {
			$this->setObject($obj);
			$this->publishType = $type ? strtolower($type) : 'publish';
			$this->totalSteps = 1;
		}
	}

	public function getTitle() {
		return _t('AdvancedWorkflowPublishJob.SCHEDULEJOBTITLE', "Scheduled $this->publishType of " . $this->getObject()->Title);
	}

	public function process() {
		if ($target = $this->getObject()) {
			
			// if we're running with a workflow applied, need to 
			// temporarily disable the 'canPublish' check in it
			if ($target->hasExtension('WorkflowApplicable')) {
				$target->disablePublishCheck();
			}

			if ($this->publishType == 'publish') {
				$target->PublishOnDate = '';
				$target->writeWithoutVersion();
				$target->doPublish();
			} else if ($this->publishType == 'unpublish') {
				$target->UnPublishOnDate = '';
				$target->writeWithoutVersion();
				$target->doUnpublish();
			}
		}
		$this->currentStep = 1;
		$this->isComplete = true;
	}

}