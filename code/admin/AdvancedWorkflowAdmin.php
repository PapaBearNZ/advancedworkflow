<?php
/**
 * @package advancedworkflow
 */
class AdvancedWorkflowAdmin extends ModelAdmin {

	public static $menu_title    = 'Workflows';
	public static $menu_priority = -1;
	public static $url_segment   = 'workflows';

	public static $managed_models  = 'WorkflowDefinition';
	public static $model_importers = array();
	
	public static $dependencies = array(
		'workflowService'		=> '%$WorkflowService',
	);
	
	/**
	 * @var WorkflowService
	 */
	public $workflowService;
	
	public function getEditForm($id = null, $fields = null) {
		$form = parent::getEditForm($id, $fields);
		
		
		$assigned = $this->workflowService->usersWorkflows(Member::currentUser());
		
		if ($assigned->count()) {
			$config = new GridFieldConfig_Base();
			$config->addComponent(new GridFieldEditButton());
			$config->addComponent(new GridFieldDetailForm());

			$grid = GridField::create('AssignedWorkflows', 'Items waiting for you', $assigned, $config);
			$grid->setForm($form);

			$form->Fields()->insertBefore($grid, 'WorkflowDefinition');
		}

		$submitted = $this->workflowService->userSubmissions(Member::currentUser());

		if ($submitted->count()) {
			$config = new GridFieldConfig_Base();
			$config->addComponent(new GridFieldEditButton());
			$config->addComponent(new GridFieldDetailForm());

			$grid = GridField::create('SubmittedWorkflows', 'Items you have submitted', $submitted, $config);
			$grid->setForm($form);

			$form->Fields()->insertBefore($grid, 'WorkflowDefinition');
		}

		return $form;
	}

}
