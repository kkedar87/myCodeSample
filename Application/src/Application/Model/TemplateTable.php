<?php
namespace Application\Model;

use Zend\Db\TableGateway\TableGateway;

class TemplateTable
{
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll($status = false)
    {
    	if($status === false)
    	{
    		$whr = array('1' => '1');
    	}
    	else
    	{
    		$whr = array('1' => '1', 'status' => $status);
    	}
    	
        $resultSet = $this->tableGateway->select($whr);
        return $resultSet;
    }

    public function getTemplates()
    {
        $resultSet = $this->tableGateway->select();
        $templates = array();
        foreach($resultSet as $row)
        {
        	$templates[$row->template_id] = $row->name;
       	}
        return $templates;
    }

    public function getTemplate($id)
    {
    	if(!is_numeric($id))
    	{
    		$alias = $id;
    		$rowset = $this->tableGateway->select(array('alias' => $alias));
    	}
    	else
    	{
	        $id  = (int) $id;
	        $rowset = $this->tableGateway->select(array('template_id' => $id));
    	}
    	$row = $rowset->current();

        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        
        return $row;
    }

    public function saveTemplate(Template $template)
    {
        $data = array(
					'template_id' => $template->template_id,
					'alias' => $template->alias,
					'page_title' => $template->page_title,
					'meta_title' => $template->meta_title,
					'meta_description' => $template->meta_description,
					'meta_keywords' => $template->meta_keywords,
					'description_heading' => $template->description_heading,
					'description' => $template->description,
					'welcome_heading' => $template->welcome_heading,
					'welcome_message' => $template->welcome_message,
					'nbaa_image_url' => $template->nbaa_image_url,
					'nbaa_image_alt' => $template->nbaa_image_alt,
					'nbaa_image_title' => $template->nbaa_image_title,
					'main_content' => $template->main_content,
					'additional_left_top' => $template->additional_left_top,
					'additional_left_bottom' => $template->additional_left_bottom,
					'additional_right_top' => $template->additional_right_top,
					'additional_right_bottom' => $template->additional_right_bottom,
					'status' => $template->status,
        );
		
        $template_id = (int)$template->template_id;
       	if ($template_id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($testimon = $this->getTemplate($template_id)) {
            	$this->tableGateway->update($data, array('template_id' => $template_id));
            } else {
                throw new \Exception('Form id does not exist');
            }
        }
    }

    public function deleteTemplate($template_id)
    {
        $this->tableGateway->delete(array('template_id' => $template_id));
    }
}